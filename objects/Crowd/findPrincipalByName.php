<?php

class Crowd_findPrincipalByName extends Crowd_applicationRequest {

	protected function appCall(SSO_Application $app, $args) {
		$name = $args->in1;

		if ($user = $app->findPrincipalByName($name)) {
			return array("out" => $user->asArray($app));
		}

		$detail = new stdClass;
		$detail->ObjectNotFoundException = "";
		return new SoapFault("soap:Server", "UserNotFoundException User <" . $name . "> does not exist", "", $detail);
	}

}

?>