<?php

class Crowd_isCacheEnabled extends Crowd_applicationRequest {

	protected function appCall(SSO_Application $app, $args) {
		return array("out" => false);
	}

}

?>