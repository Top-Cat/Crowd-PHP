<?php

class Crowd_isGroupMember extends Crowd_applicationRequest {

	protected function appCall(SSO_Application $app, $args) {
		$group = $args->in1;
		$name = $args->in2;

		$ingroup = false;

		if ($groups = $app->getGroups($name)) {
			$ingroup = in_array($group, $groups);
		}

		return array("out" => $ingroup);
	}

}

?>