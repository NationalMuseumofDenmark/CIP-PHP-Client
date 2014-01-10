<?php
namespace CIP\callers;
class CURLCaller extends ACaller {

	protected $_curl_handle;
	
	public function __construct() {
		$this->_curl_handle = curl_init();
		curl_setopt($this->_curl_handle, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($this->_curl_handle, CURLOPT_USERAGENT, sprintf(\CIP\CIPClient::USERAGENT, \CIP\CIPClient::CLIENT_VERSION) );
	}
	
	public function call($url, $http_method, $data, $content_type = 'application/x-www-form-urlencoded') {
		curl_setopt($this->_curl_handle, CURLOPT_HTTPHEADER, array("Content-Type: $content_type"));
		
		// Using the provided HTTP method.
		if($http_method == 'POST') {
			curl_setopt($this->_curl_handle, CURLOPT_POST, true);
			// TODO: Consider if these named parameters should actually be added to the URL instead of the request body.
			curl_setopt($this->_curl_handle, CURLOPT_POSTFIELDS, $data);
		} elseif($http_method == 'GET') {
			curl_setopt($this->_curl_handle, CURLOPT_POST, false);
			$url .= '?' . $data;
		} else {
			throw new \RuntimeException('Unsupported HTTP method: ' . $http_method);
		}
		
		curl_setopt($this->_curl_handle, CURLOPT_URL, $url);
			
		$response = curl_exec($this->_curl_handle);
		$status_code = curl_getinfo($this->_curl_handle, CURLINFO_HTTP_CODE);
		
		if($status_code >= 400 && $status_code <= 599) {
			throw new \CIP\CIPServersideException( $response, $status_code );
		}
		
		if($response === false) {
			throw new \Exception('The cURL call to the service failed: ' . curl_error($this->_curl_handle));
		} elseif ($response === '') {
			return true;
		} else {
			return json_decode($response, true);
		}
		
	}
	
}