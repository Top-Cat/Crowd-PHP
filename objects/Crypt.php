<?php

class Crypt {

	public static function hashPassword($pass, $iter = 1000) {
		$next = "";
		for ($x = 0; $x < $iter; $x++) {
			$next .= $pass;
			$next = hash('whirlpool', $next);
		}
		return $next;
	}

	public static function generateToken($seed = "") {
		return base64_encode(md5(time() . $seed . mt_rand(1000000, 9999999)));
	}

}

?>