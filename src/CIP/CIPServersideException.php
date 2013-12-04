<?php

namespace CIP;

class CIPServersideException extends \Exception {

	public function __construct($response, $status_code = null) {
		$message = "CIP Error";
		if($status_code) {
			$message .= " (status $status_code)";
		}
		$response_decoded = json_decode($response, true);
		if($response_decoded !== null) {
			$message .= ": " . $response_decoded['message'] . ".";
			$this->_response_decoded = $response_decoded;
		} else {
			if($response) {
				$message .= ": " . $response;
			} else {
				$message .= ".";
			}
		}
		parent::__construct($message, $status_code);
	}

	/**
	 * A JSON decoded version of the response from the service.
	 * @var mixed
	 */
	protected $_response_decoded;

	public function getRemoteTraceAsString() {
		if(count($this->_response_decoded['exception']['stacktrace']) > 0) {
			$result = "Serverside stack:\n";
			for($position = 0; $position < count($this->_response_decoded['exception']['stacktrace']); $position++) {
				$result .= "#$position " . $this->_response_decoded['exception']['stacktrace'][$position] . "\n";
			}
		} else {
			$result = "";
		}
		return $result;
	}

}