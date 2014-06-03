<?php

class SSO_User_LDAP extends SSO_User {

	public function asArray(SSO_Application $app) {
		$info = $this->directory->getInfo($app, $this->alias);

		return array(
			"ID" => -1,
			"active" => true,
			"name" => $this->getAlias($app, $name),
			"first-name" => $info['givenname'][0],
			"last-name" => $info['sn'][0],
			"display-name" => $info['displayname'][0],
			"email" => $info['mail'][0],
			"conception" => Util::time($info['whencreated'][0]),
			"directoryId" => $this->getId(),
			"lastModified" => Util::time($info['whenchanged'][0])
		);
	}

	public function toJSON(SSO_Application $app) {
		$info = $this->directory->getInfo($app, $this->alias);

		$out = array(
			'link' => array('rel' => "self", 'href' => Util::base() . "/rest/usermanagement/1/user?username=" . $this->alias),
			'first-name' => $info['givenname'][0],
			'last-name' => $info['sn'][0],
			'display-name' => $info['displayname'][0],
			'email' => $info['mail'][0],
			'active' => true
		);

		return json_encode($out);
	}

	public function toXML(SSO_Application $app) {
		$info = $this->directory->getInfo($app, $this->alias);

		$xml = new SimpleXMLElement('<user/>');
		$xml->addAttribute('name', $this->alias);

		$link = $xml->addChild('link');
		$link->addAttribute('href', Util::base() . "/rest/usermanagement/1/user?username=" . $this->alias);
		$link->addAttribute('rel', "self");

		$xml->addChild('first-name', $info['givenname'][0]);
		$xml->addChild('last-name', $info['sn'][0]);
		$xml->addChild('display-name', $info['displayname'][0]);
		$xml->addChild('email', $info['mail'][0]);
		$xml->addChild('active', 'true');
		return $xml->asXML();
	}

}

?>