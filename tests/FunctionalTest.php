<?php
require_once 'CIPTest.php';

class FunctionalTest extends CIPTest {
	
	public function testVersionCompatibility() {
		// Request the version from the webservice.
		$response = $this->_client->system()->getversion();
		
		// Asset that the response has a version key.
		$this->assertArrayHasKey('version', $response);
		
		// Assert 5 values.
		$this->assertCount(5, array_keys($response["version"]));
		
		// This throws exceptions if compatibility fails.
		$this->_client->checkCompatibility();
	}
	
	public function testSession() {
		// Opening a session
		$response = $this->_client->session()->open();
		
		// It must have a session id
		$this->assertArrayHasKey('jsessionid', $response);
		$jsessionid = $response['jsessionid'];
		
		// The session id should be 32 chars long.
		$this->assertSame(32, strlen($jsessionid));
		
		// Trying to close the session.
		$response = $this->_client->session()->close();
		
		// Assert a void response.
		$this->assertTrue($response);
		$this->assertNull($this->_client->getSessionID());
		
		$this->openAuthenticatedSession();
	}
	
	protected function openAuthenticatedSession() {
		// The CIP_USER environment variable should be specified.
		$this->assertArrayHasKey('CIP_USER', $_SERVER);
		// The CIP_PASSWORD environment variable should be specified.
		$this->assertArrayHasKey('CIP_PASSWORD', $_SERVER);
		// The CIP_SERVERADDRESS environment variable should be specified.
		$this->assertArrayHasKey('CIP_SERVERADDRESS', $_SERVER);
		
		$this->_client->setDAMCredentials($_SERVER['CIP_USER'], $_SERVER['CIP_PASSWORD'], $_SERVER['CIP_SERVERADDRESS']);
	}

	public function testCategorySearch() {
		$category_name = 'Rotationsbilleder';
		$response = $this->_client->metadata()->search( 'ES', 'web', null, null, 'Categories is Rotationsbilleder' );
		foreach($response['items'] as $asset) {
			$found_rotationsbilleder_category = false;
			foreach($asset['{af4b2e0c-5f6a-11d2-8f20-0000c0e166dc}'] as $category) {
				if($category['name'] == 'Rotationsbilleder') {
					$found_rotationsbilleder_category = true;
				}
			}
			$this->assertTrue($found_rotationsbilleder_category);
		}
	}
	
	public function testMetadata1() {
		// Opening a session
		$this->openAuthenticatedSession();

		// Requesting catalogs.
		$response = $this->_client->metadata()->getcatalogs();
		
		// The environment variables should be specified.
		$this->assertArrayHasKey('TEST_CATALOG_NAME', $_SERVER);
		$this->assertArrayHasKey('TEST_CATALOG_ALIAS', $_SERVER);
		$this->assertArrayHasKey('TEST_CATALOG_VIEW', $_SERVER);
		
		$catalog_name = $_SERVER['TEST_CATALOG_NAME'];
		$catalog_alias = $_SERVER['TEST_CATALOG_ALIAS'];
		$catalog_view = $_SERVER['TEST_CATALOG_VIEW'];
		
		$this->assertArrayHasKey('catalogs', $response);
		
		$catalog_id = null;
		foreach($response['catalogs'] as $catalog) {
			if($catalog['name'] = $catalog_name) {
				$catalog_id = $catalog['id'];
			}
		}
		
		$this->assertNotNull($catalog_id);
		
		echo "Testing catalog is: $catalog_id\n";
		$catalog_name = urlencode($catalog_name);
		// $response = $client->metadata()->getlayout($catalog_name);
		// var_dump($response);
		
		$response = $this->_client->metadata()->search($catalog_alias, $catalog_view, 'a');
		// var_dump($response);
		$this->assertNotNull($catalog_id);
		$this->assertGreaterThan(0, $response["totalcount"]);
	}
	
	public function testMetadata2() {
		// Opening a session
		$this->openAuthenticatedSession();
		
		// The environment variables should be specified.
		$this->assertArrayHasKey('TEST_CATALOG_NAME', $_SERVER);
		$this->assertArrayHasKey('TEST_CATALOG_ALIAS', $_SERVER);
		$this->assertArrayHasKey('TEST_CATALOG_VIEW', $_SERVER);
		
		$catalog_name = $_SERVER['TEST_CATALOG_NAME'];
		$catalog_alias = $_SERVER['TEST_CATALOG_ALIAS'];
		$catalog_view = $_SERVER['TEST_CATALOG_VIEW'];
		
		// Testing the seach with layout functionality.
		$response = $this->_client->metadata()->search_with_layout($catalog_alias, $catalog_view, "a");
		$this->assertGreaterThan(0, $response["totalcount"]);
		
		// Asserting that the Date response filter kicked in, while "asset_creation_date" would only be advailable if the field was mapped.
		$this->assertInstanceOf('DateTime', $response["items"][0]["asset_creation_date"]);
	}
	
	public function testMetadata3() {
		// Opening a session
		$this->openAuthenticatedSession();

		// Testing a search with a layout mapping applied.
		$response = $this->_client->metadata()->search_with_layout('FHM', 'web', null, null, '"Record Name" * or "Record Name" !*');

		$this->assertGreaterThan(0, $response["totalcount"]);
		echo 'Found ' .$response["totalcount"]. ' records!\n';
		
		$this->assertArrayHasKey('record_creation_date', $response["items"][0]);
	}

	/**
	 * This test shows that a streaming parser doesn't help much when trying to optimize json deserialization.
	 * The following are results from an execution:
	 *    stream_initialization_time = 2.0528049468994
	 *    stream_parse_time = 4.9804320335388
	 *    string_initialization_time = 2.3881509304047
	 *    string_parse_time = 1.6055541038513
	 *    
	 * @throws Exception
	 * @return boolean
	 */
	public function testJSONStreamParser() {
		// Simply skip this test if the jsonstreamingparser library is not installed.
		// Run: 'composer require "salsify/json-streaming-parser"="v2.0"' to enable this test.
		if(!class_exists('JsonStreamingParser_Parser', true)) {
			return true;
		}
		
		$opts = array(
				'http' => array(
						'method' => "POST",
						'header' => "Accept-language: en\r\n".
									"Content-type: application/x-www-form-urlencoded\r\n",
						'content' => 'querystring=%22Record+Name%22+%2A+or+%22Record+Name%22+%21%2A'
				)
		);
		$context = stream_context_create($opts);
		
		/* Sends an http request to www.example.com
		 with additional headers shown above */
		//$fp = fopen('http://www.example.com', 'r', false, $context);
		$t1 = microtime(true);
		$stream = fopen('http://samlinger.natmus.dk/CIP/metadata/search/FHM/web;jsessionid=C55078FEF0D6CE65FA547D8CEA60DDEB?apiversion=4', 'r', false, $context);
		$listener = new \json\ArrayMaker();
		try {
			$parser = new JsonStreamingParser_Parser($stream, $listener);
			$t2 = microtime(true);
			$parser->parse();
			$t3 = microtime(true);
		} catch (Exception $e) {
			fclose($stream);
			throw $e;
		}
		// echo '$t2 - $t1 = ' . ($t2 - $t1) . "\n";
		// echo '$t3 - $t2 = ' . ($t3 - $t2) . "\n";
		$stream_initialization_time = $t2 - $t1;
		$stream_parse_time = $t3 - $t2;
		
		// Try the old fassion way.
		$t1 = microtime(true);
		$stream = fopen('http://samlinger.natmus.dk/CIP/metadata/search/FHM/web;jsessionid=C55078FEF0D6CE65FA547D8CEA60DDEB?apiversion=4', 'r', false, $context);
		try {
			$t2 = microtime(true);
			$contents = stream_get_contents($stream);
			// Parse from string.
			$contents = json_decode($contents, true);
			$t3 = microtime(true);
		} catch (Exception $e) {
			fclose($stream);
			throw $e;
		}
		$string_initialization_time = $t2 - $t1;
		$string_parse_time = $t3 - $t2;
		
		echo "stream_initialization_time = $stream_initialization_time\n";
		echo "stream_parse_time = $stream_parse_time\n";
		echo "string_initialization_time = $string_initialization_time\n";
		echo "string_parse_time = $string_parse_time\n";
	}
}
