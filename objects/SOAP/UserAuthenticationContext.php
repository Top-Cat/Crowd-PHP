<?php

class SOAP_UserAuthenticationContext {

	private $application;
	private $credential;
	private $name;
	private $validationFactors = array();

	public function __construct($args) {
		$this->application = $args->application;
		$this->credential = new SOAP_PasswordCredential($args->credential);
		$this->name = $args->name;

		foreach ($args->validationFactors->ValidationFactor as $v) {
			$validationFactors[] = new SOAP_ValidationFactor($v);
		}
	}

	public function getName() {
		return $this->name;
	}

	public function getApplicationName() {
		return $this->application;
	}

	public function getCredential() {
		return $this->credential;
	}

}

?>