<?php
namespace json;

/**
 * This basic implementation of a listener simply constructs an in-memory
* representation of the JSON document, which is a little silly since the whole
* point of a streaming parser is to avoid doing just that. However, it gets
* the point across.
*/
class ArrayMaker implements \JsonStreamingParser_Listener {
	private $_json;

	private $_stack;
	private $_key;

	public function file_position($line, $char) {

	}

	public function get_json() {
		echo "get_json called\n";
		exit;
		return $this->_json;
	}

	public function start_document() {
		echo "start_document called\n";
		$this->_stack = array();
		$this->_key = null;
	}

	public function end_document() {
		echo "end_document called\n";
		// w00t!
	}

	public function start_object() {
		echo "start_object called\n";
		array_push( $this->_stack, array() );
	}

	public function end_object() {
		echo "end_object called\n";
		$obj = array_pop( $this->_stack );
		if ( empty($this->_stack) ) {
			// doc is DONE!
			$this->_json = $obj;
		} else {
			$this->value($obj);
		}
	}

	public function start_array() {
		echo "start_array called\n";
		$this->start_object();
	}

	public function end_array() {
		echo "end_array called\n";
		$this->end_object();
	}

	// Key will always be a string
	public function key( $key ) {
		echo "key($key) called\n";
		$this->_key = $key;
	}

	// Note that value may be a string, integer, boolean, null
	public function value( $value ) {
		echo "value($value) called\n";
		$obj = array_pop($this->_stack);
		if ($this->_key) {
			$obj[$this->_key] = $value;
			$this->_key = null;
		} else {
			array_push($obj, $value);
		}
		array_push($this->_stack, $obj);
	}
}