<?php

class REST_usermanagement_1_user extends REST_resource {

	public function call() {
		$user = $this->app->findPrincipalByName($_GET['username']);
		return $user->toXML($this->app);
	}

}

?>