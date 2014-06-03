<?php

class Crowd_authenticateApplication extends Crowd_request {

	public function call($args) {
		$aac = new SOAP_ApplicationAuthenticationContext($args->in0);
		$app = SSO_Application::fromCredentials($aac);

		if ($app) {
			$token = $app->generateToken();

			return array("out" => array("name" => $app->getName(), "token" => $token));
		}
	}

}

?>