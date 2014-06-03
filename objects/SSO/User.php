<?php

abstract class SSO_User {

	protected $alias;
	protected $directory;

	public function __construct($alias, SSO_Directory $directory) {
		$this->alias = $alias;
		$this->directory = $directory;
	}

	public function getAlias() {
		return $this->alias;
	}

	public function generateToken() {
		$token = Crypt::generateToken($this->alias);
		pg_query_params(SQL::getConn(), "INSERT INTO user_token (alias, dir_id, token) VALUES ($1, $2, $3)", array($this->alias, $this->directory->getId(), $token));
		return $token;
	}

	public abstract function asArray(SSO_Application $app);

	public abstract function toJSON(SSO_Application $app);

	public abstract function toXML(SSO_Application $app);

	public function __toString() {
		return "SSO_User[alias=" . $this->alias . ", directoryId=" . $this->directory->getId() . "]";
	}

	public static function fromToken($token) {
		$result = pg_query_params(SQL::getConn(), "SELECT user_token.alias, directory.id, directory.type FROM user_token JOIN directory ON user_token.dir_id = directory.id WHERE token = $1", array($token));
		if (pg_num_rows($result) > 0) {
			$row = pg_fetch_array($result);
			$dir = "SSO_Directory_" . $row['type'];
			$user = "SSO_User_" . $row['type'];
			return new $user($row['alias'], new $dir($row['id']));
		}
	}

	public static function invalidateToken($token) {
		pg_query_params(SQL::getConn(), "DELETE FROM user_token WHERE token = $1", array($token));
	}

}

?>