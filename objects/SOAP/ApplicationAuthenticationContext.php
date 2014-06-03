<?php

class SOAP_ApplicationAuthenticationContext {

	private $name;
	private $credential;

	public function __construct() {
		$args = func_get_args();

		if (!is_object($args[0])) {
			$this->name = $args[0];
			$this->credential = new SOAP_PasswordCredential($args[1]);
		} else {
			$args = $args[0];

			$this->name = $args->name;
			$this->credential = new SOAP_PasswordCredential($args->credential);
		}
	}

	public function getName() {
		return $this->name;
	}

	public function getCredential() {
		return $this->credential;
	}

}

?>