<?php

class Util {

	function time($ldapTime) {
		$unixTime = 0;
		$pat = '/^([0-9]{4,4})([0-9]{2,2})([0-9]{2,2})([0-9]{2,2})([0-9]{2,2})([0-9]{2,2}).*/';

		if(preg_match($pat, $ldapTime, $matches) > 0) {
			$unixTime = gmmktime($matches[4], $matches[5], $matches[6], $matches[2], $matches[3], $matches[1]);
		}
		return gmdate("Y-m-d\TH:i:s", $unixTime);
	}

	function base() {
		return "https://" . $_SERVER['HTTP_HOST'];
	}

}

?>