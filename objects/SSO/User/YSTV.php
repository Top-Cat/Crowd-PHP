<?php

class SSO_User_YSTV extends SSO_User {

	public function asArray(SSO_Application $app) {
		$info = $this->directory->getInfo($app, $this->alias);

		return array(
			"ID" => -1,
			"active" => true,
			"name" => $this->getAlias($app, $name),
			"first-name" => $info['first_name'],
			"last-name" => $info['last_name'],
			"display-name" => $info['first_name'] . " " . $info['last_name'],
			"email" => $info['email_address'],
			"conception" => gmdate("Y-m-d\TH:i:s", strtotime($info['created_date'])),
			"directoryId" => $this->getId(),
			"lastModified" => gmdate("Y-m-d\TH:i:s", strtotime($info['last_edited_date']))
		);
	}

	public function toJSON(SSO_Application $app) {
		$info = $this->directory->getInfo($app, $this->alias);

		$out = array(
			'link' => array('rel' => "self", 'href' => Util::base() . "/rest/usermanagement/1/user?username=" . $this->alias),
			'first-name' => $info['first_name'],
			'last-name' => $info['last_name'],
			'display-name' => $info['first_name'] . " " . $info['last_name'],
			'email' => $info['email_address'],
			'active' => true
		);

		return json_encode($out);
	}

	public function toXML(SSO_Application $app) {
		$info = $this->directory->getInfo($app, $this->alias);
		error_log(print_r($info, true));

		$xml = new SimpleXMLElement('<user/>');
		$xml->addAttribute('name', $this->alias);

		$link = $xml->addChild('link');
		$link->addAttribute('href', Util::base() . "/rest/usermanagement/1/user?username=" . $this->alias);
		$link->addAttribute('rel', "self");

		$xml->addChild('first-name', $info['first_name']);
		$xml->addChild('last-name', $info['last_name']);
		$xml->addChild('display-name', $info['first_name'] . " " . $info['last_name']);
		$xml->addChild('email', $info['email_address']);
		$xml->addChild('active', 'true');
		return $xml->asXML();
	}

}

?>