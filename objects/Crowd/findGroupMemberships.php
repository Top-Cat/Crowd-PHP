<?php

class Crowd_findGroupMemberships extends Crowd_applicationRequest {

	protected function appCall(SSO_Application $app, $args) {
		return array("out" => $app->getGroups($args->in1));
	}

}

?>