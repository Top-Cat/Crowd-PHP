<?php

abstract class SSO_Directory {

	private $id;

	public function __construct($id) {
		$this->id = $id;

		$result = pg_query_params(SQL::getConn(), "SELECT name, value FROM dir_attributes WHERE app_id = $1", array($this->id));
		while ($row = pg_fetch_array($result)) {
			$parts = explode(".", $row['name']);
			unset($obj);
			$obj = $this;
			foreach ($parts as $part) {
				if (!isset($obj->{$part})) {
					$obj->{$part} = new stdClass();
				}
				$obj =& $obj->{$part}; // Dark magic
			}
			$obj = $row['value'];
		}
	}

	public function getId() {
		return $this->id;
	}

	abstract public function authUser(SSO_Application $app, SOAP_UserAuthenticationContext $uac);

	abstract public function getGroups(SSO_Application $app, $name);

	abstract public function userExists(SSO_Application $app, $name);

	abstract public function getInfo(SSO_Application $app, $name);

	abstract public function setPassword(SSO_Application $app, $name, SOAP_PasswordCredential $credential);

	public function getAlias(SSO_Application $app, $name) {
		return $name;
	}

}

?>