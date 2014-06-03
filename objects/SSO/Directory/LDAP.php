<?php

class SSO_Directory_LDAP extends SSO_Directory {

	private $conn;

	public function __construct($id) {
		parent::__construct($id);

		putenv('LDAPTLS_REQCERT=never');

		$this->conn = ldap_connect($this->ldap->url);
		ldap_set_option($this->conn, LDAP_OPT_PROTOCOL_VERSION, $this->ldap->version);
		ldap_set_option($this->conn, LDAP_OPT_REFERRALS, 0);
		$this->bind();
	}

	private function bind() {
		ldap_bind($this->conn, $this->ldap->userdn, $this->ldap->password);
	}

	public function authUser(SSO_Application $app, SOAP_UserAuthenticationContext $uac) {
		$user = $this->getAlias($app, $uac->getName());
		$pass = $uac->getCredential()->getUnencrypted();
		$bind = @ldap_bind($this->conn, $user . "@ystv.local", $pass);

		$this->bind();

		return (!empty($pass) && $bind);
	}

	public function getGroups(SSO_Application $app, $name) {
		$user = $this->getAlias($app, $name);
		$entries = $this->ldapInfo($user);

		$output = $entries[0][$this->ldap->user->group];
		$token = $entries[0]['primarygroupid'][0];

		array_shift($output);

		$results2 = ldap_search($this->conn, $this->ldap->basedn, $this->ldap->group->filter, array("distinguishedname","primarygrouptoken"));
		$entries2 = ldap_get_entries($this->conn, $results2);

		foreach ($entries2 as $e) {
			if($e['primarygrouptoken'][0] == $token) {
				$output[] = $e['distinguishedname'][0];
				break;
			}
		}

		$final = array();
		foreach ($output as $g) {
			preg_match('/CN=([a-z0-9 -]+),/i', $g, $f);
			$final[] = $f[1];
		}
		return $final;
	}

	private function ldapInfo($name) {
		$sr = ldap_search($this->conn, $this->ldap->user->dn . "," . $this->ldap->basedn, str_replace("$", $name, $this->ldap->user->filter));
		return ldap_get_entries($this->conn, $sr);
	}

	public function userExists(SSO_Application $app, $name) {
		$user = $this->getAlias($app, $name);
		return $this->ldapInfo($user)['count'] > 0;
	}

	public function getInfo(SSO_Application $app, $name) {
		$out = $this->ldapInfo($name);
		if ($out['count'] > 0) {
			return $this->ldapInfo($name)[0];
		}
	}

	public function setPassword(SSO_Application $app, $name, SOAP_PasswordCredential $credential) {
		$user = $this->getAlias($app, $name);

		$encPwd = $this->encodePwd($credential->getUnencrypted());
		$userData = array("unicodePwd" => $encPwd);
		$sr = ldap_search($this->conn, $this->ldap->user->dn . "," . $this->ldap->basedn, str_replace("$", $user, $this->ldap->user->filter));
		$entry = ldap_first_entry($this->conn, $sr);
		$userDn = ldap_get_dn($this->conn, $entry);

		$result = ldap_mod_replace($this->conn, $userDn, $userData);
	}

	private function encodePwd($pw) {
		$newpw = '';
		$pw = "\"" . $pw . "\"";
		$len = strlen($pw);

		for ($i = 0; $i < $len; $i++)
			$newpw .= "{$pw{$i}}\000";

		//$newpw = base64_encode($newpw);
		return $newpw;
	}

	public function getAlias(SSO_Application $app, $name) {
		$result = pg_query_params(SQL::getConnection(), "SELECT server_name FROM members WHERE (username = $1 or server_name = $1)", array($name));
		if ($row = pg_fetch_array($result)) {
			return $row['server_name'];
		}

		return $name;
	}

}

?>