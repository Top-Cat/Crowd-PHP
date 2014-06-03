<?php

abstract class Crowd_applicationRequest extends Crowd_request {

	public function call($args) {
		$token = new SOAP_AuthenticatedToken($args->in0);
		$app = $token->getApplication();

		if ($app) {
			return $this->appCall($app, $args);
		}
		// TODO: Raise an exception
	}

	abstract protected function appCall(SSO_Application $app, $args);

}

?>