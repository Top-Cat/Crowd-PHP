<?php

class Crowd_invalidatePrincipalToken extends Crowd_applicationRequest {

	protected function appCall(SSO_Application $app, $args) {
		SSO_User::invalidateToken($args->in1);
	}

}

?>