<?php
namespace CIP\services;

abstract class BaseService {
	
	protected $_client;
	
	/**
	 * Creates an instance of the service.
	 * @param \CIP\CIPClient $client
	 */
	public function __construct($client) {
		if($client) {
			$this->_client = $client;
		} else {
			throw new \InvalidArgumentException("The argument supplied should be a \CIP\CIPClient");
		}
	}
	
	protected function getServiceName() {
		$class = get_called_class();
		$class_name_position = strrpos($class, '\\');
		$class_name = substr($class, $class_name_position + 1);
		$class_name = substr($class_name, 0, strlen($class_name) - 7);
		$class_name = strtolower($class_name);
		return $class_name;
	}
	
}