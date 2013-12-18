<?php
/**
 * A way to call the service in a streamed way, parsing the response as it comes, instead of buffering it in a string.
 * @author Kræn Hansen <kh@bitblueprint.com> for the National Museum of Denmark.
 */
namespace CIP\callers;
class StreamCaller extends ACaller {
	
	public function __construct() {
		$this->_array_maker = new \json\ArrayMaker();
	}
	
	protected $_array_maker;
	
	public function call($url, $http_method, $data, $content_type = 'application/x-www-form-urlencoded') {
		$context = stream_context_create(array(
			'http' => array(
				'method' => $http_method,
				'header' => "Accept-language: en\r\n".
							"Content-type: $content_type",
				'content' => 'querystring=%22Record+Name%22+%2A+or+%22Record+Name%22+%21%2A'
			)
		));

		try {
			$stream = fopen($url, 'r', false, $context);
		
			$parser = new \JsonStreamingParser_Parser($stream, $this->_array_maker);
			$parser->parse();
			
			return $this->_array_maker->get_json();
		} catch (Exception $e) {
			fclose($stream);
			throw $e;
		}
	}
	
}