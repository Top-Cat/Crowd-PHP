<?php

class SSO_Application {

	private $id;
	private $name;
	private $lowerCase = false;

	private $directories;

	private function __construct($id) {
		$this->id = $id;

		$result = pg_query_params(SQL::getConn(), "SELECT name, \"lowerCase\" FROM applications WHERE id = $1", array($this->id));
		if (pg_num_rows($result) > 0) {
			$row = pg_fetch_array($result);
			$this->name = $row['name'];
			$this->lowerCase = $row['lowerCase'];
		}
	}

	public function getName() {
		return $this->name;
	}

	private function ccase($str) {
		return $this->lowerCase ? strtolower($str) : $str;
	}

	public function generateToken() {
		$token = Crypt::generateToken($this->id);
		pg_query_params(SQL::getConn(), "INSERT INTO application_token (app_id, token) VALUES ($1, $2)", array($this->id, $token));
		return $token;
	}

	private function getDirectories() {
		if (!isset($this->directories)) {
			$this->directories = array();
			$result = pg_query_params(SQL::getConn(), "SELECT type, \"all\", dir_id FROM app_directories JOIN directory ON directory.id = app_directories.dir_id WHERE app_directories.app_id = $1 AND directory.active = TRUE ORDER BY app_directories.id ASC", array($this->id));
			while ($row = pg_fetch_array($result)) {
				$dir = "SSO_Directory_" . $row['type'];
				$this->directories[] = array(new $dir($row['dir_id']), $row['all'], $row['type']);
			}
		}
		return $this->directories;
	}

	public function authenticateUser(SOAP_UserAuthenticationContext $uac) {
		$dirs = $this->getDirectories();
		foreach ($dirs as $v) {
			if ($v[0]->authUser($this, $uac)) {
				$user = "SSO_User_" . $v[2];
				$alias = new $user($uac->getName(), $v[0]);
				if ($v[1] === "t") {
					return $alias;
				}
				$result2 = pg_query_params(SQL::getConn(), "SELECT COUNT(*) as c FROM app_dir_groups WHERE app_dir_groups.app_id = $1 AND app_dir_groups.dir_id = $2 AND app_dir_groups.group IN ('" . implode("', '", $v[0]->getGroups($this, $uac->getName())) . "')", array($this->id, $v[0]->getId()));
				$row2 = pg_fetch_array($result2);
				if ($row2['c'] > 0) {
					return $alias;
				}
			}
		}
		return false;
	}

	public function findPrincipalByName($name) {
		$dirs = $this->getDirectories();
		foreach ($dirs as $v) {
			if ($v[0]->userExists($this, $name)) {
				$user = "SSO_User_" . $v[2];
				return new $user($name, $v[0]);
			}
		}
		return false;
	}

	public function setPassword($name, SOAP_PasswordCredential $credential) {
		$dirs = $this->getDirectories();
		foreach ($dirs as $v) {
			if ($v[0]->userExists($this, $name)) {
				$v[0]->setPassword($this, $name, $credential);
			}
		}
		return false;
	}

	public function getGroups($name) {
		$out = array();

		$dirs = $this->getDirectories();
		foreach ($dirs as $v) {
			if ($v[0]->userExists($this, $name)) {
				$out = array_merge($out, $v[0]->getGroups($this, $name));
			}
		}
		return array_map(array($this, 'ccase'), $out);
	}

	public function getGrantedAuthorities() {
		$out = array();

		$result = pg_query_params(SQL::getConn(), "SELECT DISTINCT \"group\" FROM app_dir_groups WHERE app_id = $1", array($this->id));
		while ($row = pg_fetch_array($result)) {
			$out[] = $this->ccase($row['group']);
		}
		return $out;
	}

	private static $applications;

	public static function fromId($id) {
		if (!isset($applications[$id])) {
			$applications[$id] = new SSO_Application($id);
		}
		return $applications[$id];
	}

	public static function fromCredentials(SOAP_ApplicationAuthenticationContext $aac) {
		$result = pg_query_params(SQL::getConn(), "SELECT id, salt, pass FROM applications WHERE name = $1 AND active = TRUE", array($aac->getName()));
		if ($row = pg_fetch_array($result)) {
			if ($row['pass'] === Crypt::hashPassword($row['salt'] . $aac->getCredential()->getEncrypted($row['salt']))) {
				return self::fromId($row['id']);
			}
		}
		return false;
	}

	public static function fromToken(SOAP_AuthenticatedToken $token) {
		$result = pg_query_params(SQL::getConn(), "SELECT app_id FROM application_token JOIN applications ON application_token.app_id = applications.id WHERE applications.name = $1 AND applications.active = TRUE AND application_token.expire > clock_timestamp() AND application_token.token = $2", array($token->getApplicationName(), $token->getToken()));
		if (pg_num_rows($result) > 0) {
			$row = pg_fetch_array($result);
			return self::fromId($row['app_id']);
		}
		return false;
	}

}

?>