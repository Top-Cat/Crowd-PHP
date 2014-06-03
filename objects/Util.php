<?php

/**
 * Contains common small utility functions.
 */
class Util {

	/**
	 * Converts time from the form found in LDAP databases
	 * to a more human-readable form
	 */
	function time($ldapTime) {
		$unixTime = 0;
		$pat = '/^([0-9]{4,4})([0-9]{2,2})([0-9]{2,2})([0-9]{2,2})([0-9]{2,2})([0-9]{2,2}).*/';

		if(preg_match($pat, $ldapTime, $matches) > 0) {
			$unixTime = gmmktime($matches[4], $matches[5], $matches[6], $matches[2], $matches[3], $matches[1]);
		}
		return gmdate("Y-m-d\TH:i:s", $unixTime);
	}

	/**
	 * Points to the public location of this install
	 */
	function base() {
		return "https://" . $_SERVER['HTTP_HOST'];
	}

}

?>