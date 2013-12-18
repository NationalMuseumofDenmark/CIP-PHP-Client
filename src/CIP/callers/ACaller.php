<?php
namespace CIP\callers;
abstract class ACaller {
	
	public abstract function call($url, $http_method, $data, $content_type);
	
}