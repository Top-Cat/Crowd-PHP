<?php

class Crowd_isValidPrincipalToken extends Crowd_applicationRequest {

	protected function appCall(SSO_Application $app, $args) {
		$out = false;
		if (SSO_User::fromToken($args->in1)) {
			$out = true;
		}
		return array("out" => $out);
	}

}

?>