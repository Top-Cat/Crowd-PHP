<?php

class REST_error extends Exception {

	public function __construct($reason, $message, $code = 0, Exception $previous = null) {
		$xml = new SimpleXMLElement('<error/>');
		$xml->addChild('reason', $reason);
		$xml->addChild('message', $message);

		parent::__construct($xml->asXML(), $code, $previous);
	}

}

?>