<?php

abstract class REST_resource {

	protected $app;

	public function __construct(SSO_Application $app) {
		$this->app = $app;
	}

	abstract public function call();

}

?>