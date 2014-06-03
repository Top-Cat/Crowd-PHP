<?php

$root = "/data/webs/sso/www/";

function __autoload($class_name) {
	global $root;
	@include $root . 'objects/' . str_replace("_", "/", $class_name) . '.php';
}

?>