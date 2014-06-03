<?php

class Crowd_getCookieInfo extends Crowd_applicationRequest {

	protected function appCall(SSO_Application $app, $args) {
		return array("out" => array("domain" => ".ystv.co.uk", "name" => "sso_token", "secure" => false, "expire" => time() + 60*60*24*7));
	}

}

?>