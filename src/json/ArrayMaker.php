<?php
namespace json;

class ArrayMaker implements \JsonStreamingParser_Listener {
  private $_json;

  private $_stack;
  private $_key;

  public function file_position($line, $char) {

  }

  public function get_json() {
    return $this->_json;
  }

  public function start_document() {
    $this->_stack = array();

    $this->_key = null;
  }

  public function end_document() {
    // w00t!
  }

  public function start_object() {
    array_push($this->_stack, array());
  }

  public function end_object() {
    $obj = array_pop($this->_stack);
    if (empty($this->_stack)) {
      // doc is DONE!
      $this->_json = $obj;
    } else {
      $this->value($obj);
    }
  }

  public function start_array() {
    $this->start_object();
  }

  public function end_array() {
    $this->end_object();
  }

  // Key will always be a string
  public function key($key) {
    $this->_key = $key;
  }

  // Note that value may be a string, integer, boolean, null
  public function value($value) {
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
