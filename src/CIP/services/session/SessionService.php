<?php
/**
 * This service provides operations to create and close server-side sessions. 
 * A session can store the credentials for later use in any subsequent request for the same session. 
 * Any credentials provided explicitly with a request take precedence over session credentials.
 * @see http://crc.canto.com/CIP/doc/CIP.html#session
 */
namespace CIP\services\session;
class SessionService extends \CIP\services\BaseService {
	// TODO: Implement this service.
	
	/**
	 * Open a new session at the server. You can provide user name and password to store them in the session for subsequent requests. 
	 * The credentials can be provided in two different ways:
	 * 1. As named URL parameters “user” and “password”
	 * 2. In the HTTP request header following the HTTP “Basic Access Authentication” standard
	 * The first option is more flexible as it can also handle user names and passwords that contain colon (":") characters. 
	 * If you want to hide the credentials and encrypt all communication you should configure your web application server to use SSL. 
	 * Then each URL you call needs to start with “https” instead of “http.”
	 * @param string[optional] $serveraddress The DAM server IP address for later catalog access. e.g. localhost, 192.168.0.2
	 * @param string[optional] $user string The user name for login to the server for later catalog access.
	 * @param string[optional] $password string The password for login to the server. The user’s password to be used for later catalog access
	 * @param string[optional] $catalogname The DAM system catalog name for later catalog access e.g. Sample Catalog
	 * @param string[optional] $locale The two-letter language code (ISO 639-1) to be used for later catalog metadata fields access.
	 * @param boolean[optional] $remember_session Should the client remember this session for subsequent calls to the service? Default: true.
	 * @return mixed The result contains the session ID of the newly created session. The session ID is returned as a HTTP cookie "jsessionid" as well as in the response text.
	 */
	public function open($serveraddress = null, $user = null, $password = null, $catalogname = null, $locale = null, $remember_session = true) {
		$response = $this->_client->call(self::getServiceName(), __FUNCTION__, array(), array(
			'serveraddress' => $serveraddress,
			'user' => $user,
			'password' => $password,
			'catalogname' => $catalogname,
			'locale' => $locale
		));
		if($remember_session) {
			$this->_client->setSessionID($response['jsessionid']);
		}
		return $response;
	}
	
	/**
	 * Close an existing session at the server. 
	 * Any subsequent request for this session will fail after this operation is executed. 
	 * You need to provide a session ID (see section about session handling above) for this operation.
	 * @return mixed None. Returns true on success.
	 */
	public function close() {
		$response = $this->_client->call(self::getServiceName(), __FUNCTION__, array( ), array( ));
		// Make the client forget the session id.
		$this->_client->setSessionID(null);
		return $response;
	}
}