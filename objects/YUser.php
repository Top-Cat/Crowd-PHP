<?php

class YUser {

	private $user;
	private $id;

	public $name;
	public $first_name;
	public $last_name;
	public $itsname;

	private function __construct(SSO_User $user) {
		$this->user = $user;

		$result = pg_query_params(SQL::getConnection(), "SELECT id, first_name, last_name, server_name, username FROM members WHERE (username = $1 or server_name = $1)", array($user->getAlias()));
		if (pg_num_rows($result) > 0) {
			$row = pg_fetch_array($result);
			$this->id = $row['id'];
			$this->name = $row['server_name'];
			$this->first_name = $row['first_name'];
			$this->last_name = $row['last_name'];
			$this->itsname = $row['username'];
		}
	}

	public function getId() {
		return $this->id;
	}

	public function getServerName() {
		return $this->name;
	}

	public function getITSName() {
		return $this->itsname;
	}

	public function getGroups() {
		$app = SSO_Application::fromId(1);

		return $app->getGroups($this->user->getAlias());

		if (!empty($this->name)) {
			return LDAP::getInstance()->getGroups($this->name);
		}
		return array();
	}

	public function setPassword($pass) {
		$hashed = Crypt::hashPassword($pass);
		$result = pg_query_params(SQL::getConnection(), "UPDATE members SET newpw = $2 WHERE id = $1", array($this->id, $hashed));
		return pg_affected_rows($result) > 0;
	}

	public function updateSession() {
		/*if (isset($_COOKIE['sso_token'])) {
			$result = pg_query_params(SQL::getConnection(), "SELECT * FROM sso_session WHERE hash = $1 and uid = $2 and expire > NOW()", array($_COOKIE['sso_token'], $this->id));
			if (pg_num_rows($result) > 0) {
				$row = pg_fetch_array($result);
				$sid = $row['hash'];
			} else {
				return false;
			}
		} else {
			$sid = Crypt::generateToken($this->id);
			$result = pg_query_params(SQL::getConnection(), "INSERT INTO sso_session (hash, uid) VALUES ($1, $2)", array($sid, $this->id));
		}
		setcookie("sso_token", $sid, time() + 60*60*24*7, "/", ".ystv.co.uk");*/
		return true;
	}

	private static $users;

	public static function fromUser(SSO_User $ssoUser) {
		$user = $ssoUser->__toString();
		if (!isset($users[$user])) {
			$users[$user] = new YUser($ssoUser);
		}
		return $users[$user];
	}

	public static function fromUsername($user) {
		$app = SSO_Application::fromId(1);

		if ($user = $app->findPrincipalByName($user)) {
			return self::fromUser($user);
		}
		return false;
	}

	public static function fromSession() {
		if (isset($_COOKIE['sso_token'])) {
			if ($user = SSO_User::fromToken($_COOKIE['sso_token'])) {
				return self::fromUser($user);
			}

			/*$result = pg_query_params(SQL::getConnection(), "SELECT uid FROM sso_session WHERE hash = $1 and expire > NOW()", array($_COOKIE['sso_token']));
			if (pg_num_rows($result) > 0) {
				$row = pg_fetch_array($result);
				return self::fromId($row['uid']);
			}*/
		}
		return false;
	}

	public static function logout() {
		setcookie("sso_token", "", 0, "/", ".ystv.co.uk");
	}

	public static function fromLogin($user, $pass) {
		$app = SSO_Application::fromId(1);

		$uac = new stdClass();
		$uac->application = $app->getName();
		$cre = new stdClass();
		$cre->credential = $pass;
		$uac->credential = $cre;
		$uac->name = $user;
		$vf = new stdClass();
		$vf->ValidationFactor = array();
		$uac->validationFactors = $vf;

		if ($alias = $app->authenticateUser(new SOAP_UserAuthenticationContext($uac))) {
			$sid = $alias->generateToken();
			setcookie("sso_token", $sid, time() + 60*60*24*7, "/", ".ystv.co.uk");
			return self::fromUser($alias);
		}

		/*$user = strtolower($user);
		$id = false;
		if (!($id = self::ldapLogin($user, $pass))) {
			if (!($id = self::newLogin($user, $pass))) {
				if (!($id = self::oldLogin($user, $pass))) {
					// Failed login
					return false;
				}
				self::fromId($id)->setPassword($pass);
			}
		}
		return self::fromId($id);*/
	}

	private static function ldapLogin($user, $pass) {
		$result = pg_query_params(SQL::getConnection(), "SELECT server_name FROM members WHERE username = $1", array($user));
		if (pg_num_rows($result) > 0) {
			$row = pg_fetch_array($result);
			$user = $row['server_name'];
		}

		$res = LDAP::getInstance()->checkLogin($user, $pass);
		if ($res) {
			$result = pg_query_params(SQL::getConnection(), "SELECT id FROM members WHERE server_name = $1", array($user));
			if (pg_num_rows($result) > 0) {
				$row = pg_fetch_array($result);
				return $row['id'];
			}
		}
		return false;
	}

	private static function newLogin($user, $pass) {
		$hashed = Crypt::hashPassword($pass);
		$result = pg_query_params(SQL::getConnection(), "SELECT id FROM members WHERE (username = $1 or server_name = $1) and newpw = $2", array($user, $hashed));
		if (pg_num_rows($result) > 0) {
			$row = pg_fetch_array($result);
			return $row['id'];
		}
		return false;
	}

	private static function oldLogin($user, $pass) {
		$hashed = hash('sha256', $pass);
		$result = pg_query_params(SQL::getConnection(), "SELECT id FROM members WHERE (username = $1 or server_name = $1) and password = $2", array($user, $hashed));
		if (pg_num_rows($result) > 0) {
			$row = pg_fetch_array($result);
			return $row['id'];
		}
		return false;
	}

}

?>