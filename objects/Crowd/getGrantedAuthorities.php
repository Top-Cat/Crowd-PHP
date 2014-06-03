<?php

class Crowd_getGrantedAuthorities extends Crowd_applicationRequest {

	protected function appCall(SSO_Application $app, $args) {
		return array("out" => $app->getGrantedAuthorities());
	}

}

?>