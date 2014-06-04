<?php

class SSOAutoload {

	public static $root = "/data/webs/sso/www/";

	static function autoload($class_name) {
		global $root;
		@include $root . 'objects/' . str_replace("_", "/", $class_name) . '.php';
	}

}

spl_autoload_register(array('SSOAutoload', 'autoload'));

?>