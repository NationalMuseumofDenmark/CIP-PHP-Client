<?php
/**
 * @see http://crc.canto.com/CIP/doc/CIP.html#system
 */
namespace CIP\services\system;
class SystemService extends \CIP\services\BaseService {
	
	public function getversion() {
		return $this->_client->call(self::getServiceName(), __FUNCTION__);
	}
	
}