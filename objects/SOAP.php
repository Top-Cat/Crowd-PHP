<?php

/**
 * Passed to SoapServer and used by PHP to initiate SOAP requests
 */
class SOAP {

	/**
	 * When a function is called, instead find the class for
	 * the request and pass the call and arguments
	 */
	public function __call($name, $args) {
		error_log("Remote server called " . $name);
		$name = "Crowd_" . $name;
		$api = new $name;

		return $api->call($args[0]);
	}

}

?>