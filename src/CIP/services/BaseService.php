<?php
namespace CIP\services;
abstract class BaseService {
	
	protected $_client;
	
	public function __construct(\CIP\CIPClient $client) {
		$this->_client = $client;
	}
	
	public static function getServiceName() {
		$class = get_called_class();
		$class = explode('\\', $class);
		$class = $class[count($class)-1];
		$class = preg_replace('|Service|', '', $class);
		return strtolower($class);
	}
	
	public static function extractNamedParameters($arguments, $whitelist_names = null) {
		if($whitelist_names === null) {
			return $arguments;
		} else {
			$result = array();
			foreach($arguments as $argument_name => $argument_value) {
				if(in_array($argument_name, $whitelist_names)) {
					$result[$argument_name] = $argument_value;
				}
			}
			return $result;
		}
	}
}