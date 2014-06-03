<?php

class SQL {

	private static $ystv;
	private static $sso;

	public static function getYSTV() {
		if (!isset(self::$ystv)) {
			self::$ystv = pg_connect(Logins::$sql_ystv_connectstring);
		}
		return self::$ystv;
	}

	public static function getSSO() {
		if (!isset(self::$sso)) {
			self::$sso = pg_connect(Logins::$sql_sso_connectstring);
		}
		return self::$sso;
	}
	
	public static function getConnection() {
		return self::getYSTV();
	}

	public static function getConn() {
		return self::getSSO();
	}

}

?>