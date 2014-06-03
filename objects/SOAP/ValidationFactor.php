<?php

class SOAP_ValidationFactor extends SOAP_KeyValue {

	public function __construct($args) {
		$this->name = $args->name;
		$this->token = $args->value;
	}

}

?>