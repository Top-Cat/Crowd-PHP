<?php

class SSO_Directory_YSTVOLD extends SSO_Directory {

	private $conn;

	public function __construct($id) {
		parent::__construct($id);

		$this->conn = SQL::getYSTV();
	}

	public function authUser(SSO_Application $app, SOAP_UserAuthenticationContext $uac) {
		$result = pg_query_params(SQL::getConnection(), "SELECT salt FROM members WHERE (username = $1 or server_name = $1) and password = $2 and newpw IS NULL", array($uac->getName(), hash("sha256", $uac->getCredential()->getUnencrypted())));
		if ($row = pg_fetch_array($result)) {
			pg_query_params(SQL::getConnection(), "UPDATE members SET newpw = $2 WHERE (username = $1 or server_name = $1) and newpw IS NULL", array($uac->getName(), $uac->getCredential()->getEncrypted($row['salt'])));
		}
		return false;
	}

	public function getGroups(SSO_Application $app, $user) {
		return array();
	}

	public function userExists(SSO_Application $app, $name) {
		return false;
	}

	public function setPassword(SSO_Application $app, $name, SOAP_PasswordCredential $credential) {
		
	}

	public function getInfo(SSO_Application $app, $name) {
		
	}

}

?>