<?php

include "autoload.php";

$soap = new SoapServer("SecurityServer", array("cache_wsdl" => WSDL_CACHE_NONE));
$soap->setClass("SOAP");

$post = file_get_contents('php://input');
$soap->handle($post);

?>