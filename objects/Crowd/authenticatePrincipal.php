<?php

class Crowd_authenticatePrincipal extends Crowd_applicationRequest {

	protected function appCall(SSO_Application $app, $args) {
		$uac = new SOAP_UserAuthenticationContext($args->in1);

		if ($app->getName() == $uac->getApplicationName()) { // Sanity Check
			if ($alias = $app->authenticateUser($uac)) {
				$usrToken = $alias->generateToken();
				return array("out" => $usrToken);
			}
		}
	}

}

?>