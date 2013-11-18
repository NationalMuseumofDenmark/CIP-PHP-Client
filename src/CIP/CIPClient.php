<?php
namespace CIP;
class CIPClient {

	const CLIENT_VERSION = '0.1';
	const SERVER_VERSION = '9.0';
	const API_VERSION = 4; // 1: CIP 8.5.2 release, 2: CIP 8.6 release, 3: CIP 8.6.1 release, 4: CIP 9.0 release
	const SERVICE_CLASS_FORMAT = '\%s\services\%s\%sService';
	const USERAGENT = 'CIP PHP Client v.%s';
	
	protected $_server;
	
	protected $_curl_handle;
	
	protected $_jsessionid;
	
	/**
	 * Constructs a client for a CIP webservice.
	 * @param string $server The base URL of the CIP service.
	 */
	public function __construct($server) {
		// Remove any trailing / from the server.
		$server = trim($server, '/');
		$this->_server = $server;
	}
	
	public static function is_debugging() {
		if(array_key_exists('DEBUGGING', $_SERVER)) {
			return $_SERVER['DEBUGGING'] == '1' || $_SERVER['DEBUGGING'] == 'true';
		}
	}
	
	protected $_services = array();
	
	public function getService($service) {
		if(!array_key_exists($service, $this->_services)) {
			$className = sprintf(self::SERVICE_CLASS_FORMAT, __NAMESPACE__, $service, ucfirst($service));
			$this->_services[$service] = new $className($this);
		}
		return $this->_services[$service];
	}
	
	/**
	 * This service provides operations to create and close server-side sessions. 
	 * A session can store the credentials for later use in any subsequent request for the same session. 
	 * Any credentials provided explicitly with a request take precedence over session credentials.
	 * @see http://crc.canto.com/CIP/doc/CIP.html#session
	 * @return \CIP\services\session\SessionService
	 */
	public function session() {
		return $this->getService(__FUNCTION__);
	}
	
	/**
	 * The main purpose of the metadata service is to provide operations for searching, retrieving, and modifying metadata. When searching you have the following options to keep the result:
	 * Immediately return all IDs of the resulting items.
	 * Immediately return metadata field values for each of the items of the result.
	 * Store the resulting item IDs in a collection by optionally combining this search result with the previous contents of the collection. You then use the getfieldvalues operation to retrieve metadata for items in the collection. This way you can implement “paging” through a long list of items without returning metadata for all items found in a single operation.
	 * Collections 
	 * The search operations allow you to optionally store the result in a named collection which is stored in the current session (see session handling above). You give the collection a name which has to be unique within the current session. The collection is bound to a specific catalog and table within the catalog and only contains a list of item IDs.
	 * After storing a search result in a collection you can then either retrieve the metadata for items in the collection using a getfieldvalues operation or combine the collection with the result of a subsequent search operation. Each search always returns the total number of items found. The getfieldvalues operation allows you to specify a starting offset in the item IDs and a maximum number of items to return. This way you can implement client-side “paging” through a long list of resulting items.
	 * @see http://crc.canto.com/CIP/doc/CIP.html#metadata
	 * @return \CIP\services\metadata\MetadataService
	 */
	public function metadata() {
		return $this->getService(__FUNCTION__);
	}
	
	/**
	 * Return a pixel preview for an asset.
	 * An optional location allows you to store the result in the local file system of the CIP server or on an FTP server instead of downloading the result to the client.
	 * Several options allow specifying the parameters for generating the preview image. The options are applied in the following order:
	 * Cropping (use optional parameters left, top, width, height).
	 * Scale down the image (use optional parameter maxsize or size).
	 * Rotate the image in 90 degree steps (use optional the parameter rotate).
	 * Output file format (use optional parameter format and quality).
	 * To improve the performance of delivering preview images the CIP server caches the generated preview images.
	 * The cache location is configured using the predefined name com.canto.cip.location.previewcache. If the image of the original asset cannot be delivered (e.g. the asset is not accessible) then this operation returns the thumbnail image instead. If even then thumbnail cannot be determined you can specify whether it should return a fallback image or an error instead.
	 * @see http://crc.canto.com/CIP/doc/CIP.html#preview
	 * @return \CIP\services\preview\PreviewService
	 */
	public function preview() {
		return $this->getService(__FUNCTION__);
	}
	
	/**
	 * The Asset service offers operations that work with the original assets: importing, downloading, checking out, checking in, deleting an asset.
	 * @see http://crc.canto.com/CIP/doc/CIP.html#asset
	 * @return \CIP\services\asset\AssetService
	 */
	public function asset() {
		return $this->getService(__FUNCTION__);
	}
	
	/**
	 * The comments service allow working with user comments (annotations).
	 * @see http://crc.canto.com/CIP/doc/CIP.html#comments
	 * @return \CIP\services\comments\CommentsService
	 */
	public function comments() {
		return $this->getService(__FUNCTION__);
	}
	
	/**
	 * The comments service allow working with user comments (annotations).
	 * @see http://crc.canto.com/CIP/doc/CIP.html#location
	 * @return \CIP\services\location\LocationService
	 */
	public function location() {
		return $this->getService(__FUNCTION__);
	}
	
	/**
	 * The developer service offers operations that can be used by developers writing client-side code.
	 * @see http://crc.canto.com/CIP/doc/CIP.html#developer
	 * @return \CIP\services\developer\DeveloperService
	 */
	public function developer() {
		return $this->getService(__FUNCTION__);
	}
	
	/**
	 * The developer service offers operations that can be used by developers writing client-side code.
	 * @see http://crc.canto.com/CIP/doc/CIP.html#configuration
	 * @return \CIP\services\configuration\ConfigurationService
	 */
	public function configuration() {
		return $this->getService(__FUNCTION__);
	}
	
	/**
	 * The developer service offers operations that can be used by developers writing client-side code.
	 * @see http://crc.canto.com/CIP/doc/CIP.html#system
	 * @return \CIP\services\system\SystemService
	 */
	public function system() {
		return $this->getService(__FUNCTION__);
	}
	
	/**
	 * Process a call to the CIP server.
	 * @param string $service_name
	 * @param string $operation_name
	 * @param string[]|null $path_parameters
	 * @param string[string]|null $named_parameters
	 * @param string|null $http_method POST (default) or GET.
	 * @throws \Exception If the server fails to respond.
	 * @return mixed A json decoding of the servers response.
	 */
	public function call($service_name, $operation_name, $path_parameters = array(), $named_parameters = array(), $http_method = 'POST') {
		$url = $this->_server . '/CIP/' . $service_name . '/' . $operation_name;
		
		if(!array_key_exists('apiversion', $named_parameters)) {
			$named_parameters['apiversion'] = self::API_VERSION;
		}
		
		if($this->_jsessionid !== null && is_string($this->_jsessionid)) {
			$url .= ';jsessionid=' . $this->_jsessionid;
		}
		
		if(count($path_parameters) > 0) {
			$url .= '/' . implode('/', $path_parameters);
		}
		if(count($named_parameters) > 0) {
			$named_parameters = http_build_query($named_parameters);
		} else {
			$named_parameters = '';
		}
		
		if(self::is_debugging()) {
			echo "Calling the service on: $url\n";
		}
		
		// If the curl handle has not been initialized, it will be.
		if($this->_curl_handle == null) {
			$this->_curl_handle = curl_init();
			curl_setopt($this->_curl_handle, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($this->_curl_handle, CURLOPT_USERAGENT, sprintf(self::USERAGENT, self::CLIENT_VERSION) );
		}
		
		// Using the provided HTTP method.
		if($http_method == 'POST') {
			curl_setopt($this->_curl_handle, CURLOPT_POST, true);
			// TODO: Consider if these named parameters should actually be added to the URL instead of the request body.
			curl_setopt($this->_curl_handle, CURLOPT_POSTFIELDS, $named_parameters);
		} elseif($http_method == 'GET') {
			curl_setopt($this->_curl_handle, CURLOPT_POST, false);
			$url .= '?' . $named_parameters;
		} else {
			throw new \RuntimeException('Unsupported HTTP method: ' . $http_method);
		}
		
		curl_setopt($this->_curl_handle, CURLOPT_URL, $url);
		$response = curl_exec($this->_curl_handle);
		
		$status_code = curl_getinfo($this->_curl_handle, CURLINFO_HTTP_CODE);
		
		if($status_code !== 200) {
			if(preg_match('|<title>(.*)</title>|', $response, $message) > 0) {
				$message = $message[1];
			} else {
				$message = $response;
			}
			throw new \Exception('The CIP service did not respond with the 200 OK status, it responded: ' . $status_code . ': ' . $message);
		}
		
		if($response === false) {
			throw new \Exception('The cURL call to the service failed: ' . curl_error($this->_curl_handle));
		} elseif ($response === '') {
			// A void response should simply return true.
			return true;
		} else {
			return json_decode($response, true);
		}
	}
	
	public function checkCompatibility() {
		$response = $this->system()->getversion();
		assert(array_key_exists('version', $response));
		assert(array_key_exists('cip', $response['version']));
		if($response['version']['cip']['version'] !== self::SERVER_VERSION) {
			throw new \RuntimeException('Compatibility check failed! Server is running another version (' .$response['version']['cip']['version']. ') than the client expected (' .self::SERVER_VERSION. '), this might result in unexpected behaviour.');
		}
	}
	
	/**
	 * Make the client remember the jsessionid, set to null to reset.
	 * @param string $jsessionid
	 */
	public function setSessionID($jsessionid) {
		$this->_jsessionid = $jsessionid;
	}
}

// Bootstrap the classloader.
set_include_path(get_include_path() . PATH_SEPARATOR . __DIR__ . DIRECTORY_SEPARATOR . "..");
require_once 'classloader.php';