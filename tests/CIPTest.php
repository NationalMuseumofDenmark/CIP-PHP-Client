<?php

abstract class CIPTest extends \PHPUnit_Framework_TestCase {
	
	protected $_client = null;
	
	public function setUp() {
		$this->assertArrayHasKey('CIP_SERVER', $_SERVER, 'The CIP_SERVER environment variable should be specified.');
		$this->_client = new \CIP\CIPClient($_SERVER['CIP_SERVER'], false);
	}
	
}