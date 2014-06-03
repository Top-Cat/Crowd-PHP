<?php

include "autoload.php";

$auth = false;

if (isset($_SERVER['PHP_AUTH_USER'])) {
	$app = SSO_Application::fromCredentials(new SOAP_ApplicationAuthenticationContext($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW']));
	if ($app) {
		$auth = true;

		if ($_GET['api-version'] == "latest") {
			$_GET['api-version'] = readlink($root . "objects/REST/" . $_GET['api-name'] . "/latest");
		}

		header('Content-type: text/xml');
		try {
			$name = "REST_" . $_GET['api-name'] . "_" . $_GET['api-version'] . "_" . $_GET['resource-name'];
			$api = new $name($app);

			$parts = preg_split("#/#", $_GET['uri'], -1, PREG_SPLIT_NO_EMPTY);
			if (sizeof($parts) > 0) {
				$method = array_shift($parts);
				print call_user_func(array($api, $method), $parts);
			} else {
				print $api->call();
			}
		} catch (REST_error $e) {
			print $e->getMessage();
		}
	}
}

if (!$auth) {
	header('WWW-Authenticate: Basic realm="YSTV SSO REST"');
	header('HTTP/1.0 401 Unauthorized');
	echo 'Bad Credentials';
	die();
}

?>