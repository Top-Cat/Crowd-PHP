<?php

/**
 * Deals with any cryptography used internally
 * e.g. Hashing and token generation
 */
class Crypt {

	/**
	 * Hashes a password over multiple iterations using the
	 * whirlpool algorithm
	 * 
	 * The default number of iterations is 1000, this can easily be
	 * increased in future to increase the difficulty of cracking
	 * passwords
	 * 
	 * Passwords should be salted before being passed to this function
	 */
	public static function hashPassword($pass, $iter = 1000) {
		$next = "";
		for ($x = 0; $x < $iter; $x++) {
			$next .= $pass;
			$next = hash('whirlpool', $next);
		}
		return $next;
	}

	/**
	 * Generates a token. This can be used to identify a user or
	 * application session
	 */
	public static function generateToken($seed = "") {
		return base64_encode(md5(time() . $seed . mt_rand(1000000, 9999999)));
	}

}

?>