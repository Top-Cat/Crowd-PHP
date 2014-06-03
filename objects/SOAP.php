<?php

class SOAP {

	public function __call($name, $args) {
		error_log("Remote server called " . $name);
		$name = "Crowd_" . $name;
		$api = new $name;

		return $api->call($args[0]);
	}

}

?>