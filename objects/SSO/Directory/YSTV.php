<?php

class SSO_Directory_YSTV extends SSO_Directory {

	private $conn;

	public function __construct($id) {
		parent::__construct($id);

		$this->conn = SQL::getYSTV();
	}

	public function authUser(SSO_Application $app, SOAP_UserAuthenticationContext $uac) {
		$result = pg_query_params(SQL::getConnection(), "SELECT id, salt, newpw FROM members WHERE (username = $1 or server_name = $1)", array($uac->getName()));
		if ($row = pg_fetch_array($result)) {
			return $row['newpw'] === $uac->getCredential()->getEncrypted($row['salt']);
		}
		return false;
	}

	public function getGroups(SSO_Application $app, $user) {
		$out = array();

		$result = pg_query_params(SQL::getConnection(), "SELECT member_group_members.member_group_name as name FROM member_group_members JOIN members ON member_group_members.member_id = members.id WHERE (members.username = $1 or members.server_name = $1)", array($user));
		while ($row = pg_fetch_array($result)) {
			$out[] = $row['name'];
		}
		return $out;
	}

	public function userExists(SSO_Application $app, $name) {
		$result = pg_query_params(SQL::getConnection(), "SELECT id FROM members WHERE (username = $1 or server_name = $1)", array($name));

		return pg_num_rows($result) > 0;
	}

	public function setPassword(SSO_Application $app, $name, SOAP_PasswordCredential $credential) {
		$result = pg_query_params(SQL::getConnection(), "SELECT salt FROM members WHERE (username = $1 or server_name = $1)", array($name));
		if ($row = pg_fetch_array($result)) {
			pg_query_params(SQL::getConnection(), "UPDATE members SET newpw = $2 WHERE (username = $1 or server_name = $1)", array($name, $credential->getEncrypted($row['salt'])));
		}
	}

	public function getInfo(SSO_Application $app, $name) {
		$result = pg_query_params(SQL::getConnection(), "SELECT * FROM members WHERE (username = $1 or server_name = $1)", array($name));
		if ($row = pg_fetch_array($result)) {
			return $row;
		}
	}

}

?>