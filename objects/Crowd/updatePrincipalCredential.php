<?php

class Crowd_updatePrincipalCredential extends Crowd_applicationRequest {

	protected function appCall(SSO_Application $app, $args) {
		$user = $args->in1;
		$credential = new SOAP_PasswordCredential($args->in2);

		$app->setPassword($user, $credential);
	}

}

?>