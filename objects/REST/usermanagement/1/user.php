<?php

class REST_usermanagement_1_user extends REST_resource {

	public function call() {
		$user = $this->app->findPrincipalByName($_GET['username']);

		if (!$user) {
			throw(new REST_error("USER_NOT_FOUND", "User <" . $_GET['username'] . "> does not exist"));
		}

		return $user->toXML($this->app);
	}

}

?>