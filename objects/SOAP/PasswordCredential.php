<?php

class SOAP_PasswordCredential {

	private $credential = "";
	private $encrypted = false;

	public function __construct($args) {
		$args = func_get_args();

		if (!is_object($args[0])) {
			$this->credential = $args[0];
			$this->encrypted = isset($args[1]) && $args[1];
		} else {
			$args = $args[0];

			$this->credential = $args->credential;
			$this->encrypted = isset($args->encryptedCredential) && $args->encryptedCredential;
		}
	}

	public function getEncryptedObj($salt) {
		if ($this->encrypted) {
			return $this;
		}
		return new SOAP_PasswordCredential(Crypt::hashPassword($salt . $this->credential), true);
	}

	public function getEncrypted($salt) {
		return $this->getEncryptedObj($salt)->getValue();
	}

	public function getUnencrypted() {
		if ($this->encrypted) {
			return "";
		}
		return $this->getValue();
	}

	public function getValue() {
		return $this->credential;
	}

	public function __toString() {
		return "SOAP_PasswordCredential[encrypted=" . ($this->encrypted ?  "true" : "false") . "]";
	}

}

?>