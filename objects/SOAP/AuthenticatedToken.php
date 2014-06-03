<?php

class SOAP_AuthenticatedToken extends SOAP_KeyValue {

	public function __construct($args) {
		$this->name = $args->name;
		$this->token = $args->token;
	}

	public function getToken() {
		return $this->token;
	}

	public function getApplicationName() {
		return $this->name;
	}

	public function getApplication() {
		return SSO_Application::fromToken($this);
	}

}

?>