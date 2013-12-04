<?php
namespace CIP;

class CIPClient {

	const CLIENT_VERSION = '0.1';
	const SERVER_VERSION = '9.0';
	const API_VERSION = 4; // 1: CIP 8.5.2 release, 2: CIP 8.6 release, 3: CIP 8.6.1 release, 4: CIP 9.0 release
	const SERVICE_CLASS_FORMAT = '\%s\services\%s\%sService';
	const USERAGENT = 'CIP PHP Client v.%s';
	const CACHE_TTL = 0; // Cached responses live forever.
	const FILTERS_DIRECTORY = '/filters/';
	
	protected $_server;
	
	protected $_curl_handle;
	
	protected $_jsessionid;
	
	protected $_dam_serveraddress;
	protected $_dam_user;
	protected $_dam_password;
	
	/**
	 * Constructs a client for a CIP webservice.
	 * @param string $server The base URL of the CIP service.
	 * @param boolean $autocreate_session Should a session be created right away?
	 * @param string[optional] $serveraddress The DAM server IP address for later catalog access. e.g. localhost, 192.168.0.2
	 * @param string[optional] $user string The user name for login to the server for later catalog access.
	 * @param string[optional] $password string The password for login to the server. The user’s password to be used for later catalog access
	 * @param string[optional] $catalogname The DAM system catalog name e.g. Sample Catalog
	 * @param string[optional] $locale The two-letter language code (ISO 639-1) to be used for the metadata field values. This parameter affects the way language-dependent metadata values are parsed. For example you can specify “fr” to specify all values suitable for French users. The default is the default locale the CIP server is running in (may be controlled using the “user.language” Java VM parameter when starting the web application server).
	 */
	public function __construct($server, $autocreate_session = true, $dam_user = null, $dam_password = null, $dam_serveraddress = null) {
		// Remove any trailing / from the server.
		$server = trim($server, '/');
		$this->_server = $server;
		$this->setDAMCredentials($dam_user, $dam_password, $dam_serveraddress, $autocreate_session);
		// Load all the filters.
		$this->loadDefaultValueFilters();
	}
	
	public function __destruct() {
		/*
		if(isset($this->_jsessionid)) {
			$this->session()->close();
		}
		*/
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
	 * Should responses from the service be cached on this side?
	 * @var boolean
	 */
	protected $_cache_responses = false;
	
	public function cacheResponses($cache_responses = true) {
		if($cache_responses) {
			self::ensureAPCCache();
		}
		// TODO: Check that APC cache is installed.
		$this->_cache_responses = $cache_responses;
	}
	
	public function getCacheResponses() {
		return $this->_cache_responses;
	}
	
	/**
	 * Should responses from the service be cached on this side?
	 * @var boolean
	 */
	protected $_cache_next_response = false;
	
	public function cacheNextResponse() {
		self::ensureAPCCache();
		// TODO: Check that APC cache is installed.
		$this->_cache_next_response = true;
	}
	
	/**
	 * Ensure that the APC module is installed (used when caching).
	 * @throws \Exception If it's not.
	 */
	protected static function ensureAPCCache() {
		if(!function_exists('apc_add') || !function_exists('apc_fetch')) {
			throw new \Exception("The Alternative PHP Cache module is not loaded, please see http://php.net/manual/en/book.apc.php for more information.");
		}
	}
	
	
	/**
	 * Process a call to the CIP server.
	 * TODO: Implement the handling of a binary response from the webservice, images etc.
	 * @param string $service_name
	 * @param string $operation_name
	 * @param string[][optional] $path_parameters
	 * @param string[string][optional] $named_parameters
	 * @param string[optional] $http_method POST (default) or GET.
	 * @throws \Exception If the server fails to respond.
	 * @return mixed A json decoding of the servers response.
	 */
	public function call($service_name, $operation_name, $path_parameters = array(), $named_parameters = array(), $include_dam_credentials = false, $http_method = 'POST') {
		// First - strip off any variant prefix from the $operation_name.
		$operation_name_underscore_index = strpos($operation_name, '_');
		if($operation_name_underscore_index !== false) {
			$operation_name = substr($operation_name, 0, $operation_name_underscore_index);
		}
		
		$url = $this->_server . '/CIP/' . $service_name . '/' . $operation_name;
		
		if(!array_key_exists('apiversion', $named_parameters)) {
			$named_parameters['apiversion'] = self::API_VERSION;
		}
		
		if($include_dam_credentials && empty($named_parameters['serveraddress']) && $this->_dam_serveraddress !== null) {
			$named_parameters['serveraddress'] = $this->_dam_serveraddress;
		}
		if($include_dam_credentials && empty($named_parameters['user']) && $this->_dam_user !== null) {
			$named_parameters['user'] = $this->_dam_user;
		}
		if($include_dam_credentials && empty($named_parameters['password']) && $this->_dam_password !== null) {
			$named_parameters['password'] = $this->_dam_password;
		}
		
		if(count($path_parameters) > 0) {
			// Filter out any parameter without a value.
			$path_parameters = array_filter($path_parameters, function($path_parameter) {
				return $path_parameter !== null;
			});
			$url .= '/' . implode('/', $path_parameters);
		}
		
		if($this->_jsessionid !== null && is_string($this->_jsessionid)) {
			$url .= ';jsessionid=' . $this->_jsessionid;
		}
		
		if(self::is_debugging()) {
			echo "Calling CIP: $url?";
			echo http_build_query($named_parameters);
			echo "\n";
		}
		
		if(count($named_parameters) > 0) {
			$named_parameters = http_build_query($named_parameters);
		} else {
			$named_parameters = '';
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
		
		// Before we execute the request - let's check if we should load it from the cache.
		if($this->_cache_responses || $this->_cache_next_response) {
			$cache_key = md5($url . print_r($named_parameters, true));
			$success = false;
			// Check if we hace the response in the cache.
			$cached_response = apc_fetch($cache_key, &$success);
			// Reset the cached_response if we got a cache miss.
			if($success) {
				if($this->is_debugging()) {
					echo "Cache hit!\n";
				}
			} else {
				if($this->is_debugging()) {
					echo "Cache missed.\n";
				}
				$cached_response = null;
			}
		} else {
			$cached_response = null;
		}
		
		if($cached_response) {
			$response = $cached_response;
		} else {
			$response = curl_exec($this->_curl_handle);
			if($this->_cache_responses || $this->_cache_next_response) {
				// Save this response in the cache.
				apc_add($cache_key, $response, self::CACHE_TTL);
			}
		}
		
		$status_code = curl_getinfo($this->_curl_handle, CURLINFO_HTTP_CODE);
		
		if($this->_cache_next_response) {
			// Reset cache next response, if set.
			$this->_cache_next_response = false;
		}
		
		if($status_code >= 400 && $status_code <= 599) {
			throw new CIPServersideException( $response, $status_code );
		}
		
		if($response === false) {
			throw new \Exception('The cURL call to the service failed: ' . curl_error($this->_curl_handle));
		} elseif ($response === '') {
			// A void response should simply return true.
			return true;
		} else {
			$result = json_decode($response, true);
			// Apply filters.
			array_walk_recursive($result, function(&$value, &$key, $userdata) {
				$userdata['this']->applyValueFilters($userdata['service'], $userdata['operation'], $key, $value);
			}, array(
				'this' => &$this,
				'service' => $service_name,
				'operation' => $operation_name
			));
			return $result;
		}
	}
	
	/**
	 * Set the credentials for the DAM to be used.
	 * @param string $user The user of the Cumulus DAM server.
	 * @param string $password The password of the Cumulus DAM server.
	 * @param string $serveraddress The address of the Cumulus DAM server.
	 * @param boolean $via_session Should this be communicated by opening a session? (Default true)
	 */
	public function setDAMCredentials($user, $password, $serveraddress = null, $via_session = true) {
		if($via_session) {
			$this->session()->open($serveraddress, $user, $password, null, null, true);
		} else {
			$this->_dam_user = $user;
			$this->_dam_password = $password;
			$this->_dam_serveraddress = $serveraddress;
		}
	}
	
	/**
	 * Perform a compatibility check on the versions of this SDK against the versions of the software running serverside.
	 * @throws \RuntimeException If the server is running a different version than expected.
	 */
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
	 * @param string $jsessionid A session ID returned from a /session/open call to the service.
	 */
	public function setSessionID($jsessionid) {
		$this->_jsessionid = $jsessionid;
	}
	
	/**
	 * Make the client remember the jsessionid, set to null to reset.
	 * @param string $jsessionid A session ID returned from a /session/open call to the service.
	 */
	public function getSessionID() {
		return $this->_jsessionid;
	}

	protected $_valueFilters = array();
	
	public function addValueFilter($filter) {
		if($filter instanceof \CIP\filters\IValueFilter) {
			$this->_valueFilters[] = $filter;
		} else {
			throw new \InvalidArgumentException("The supplied argument is not implementing the \CIP\filters\IValueFilter interface!");
		}
	}
	
	public function applyValueFilters( $service, $action, &$key, &$value ) {
		foreach($this->_valueFilters as $filter) {
			// $key and $value are passed by referance.
			$filter->apply( $service, $action, $key, $value );
		}
	}
	
	protected function loadDefaultValueFilters() {
		if ($handle = opendir(__DIR__ . self::FILTERS_DIRECTORY)) {
			while (false !== ($entry = readdir($handle))) {
				if ($entry != "." && $entry != ".." && $entry != 'IValueFilter.php') {
					$class_name = '\\CIP\\filters\\' . substr($entry, 0, strlen($entry) - 4);
					$filter = new $class_name();
					$this->addValueFilter($filter);
				}
			}
			closedir($handle);
		}
	}
}

// Bootstrap the classloader.
set_include_path(get_include_path() . PATH_SEPARATOR . __DIR__ . DIRECTORY_SEPARATOR . "..");
require_once 'classloader.php';