<?php
echo "Running CIP client tests - this should be run using the PHP CLI.\n";

// Loading the client library.
require_once '../src/CIP/CIPClient.php';

if(!array_key_exists('CIP_SERVER', $_SERVER)) {
	throw new RuntimeException('The CIP_SERVER environment variable should be specified.');
}

// Creating a CIP Client.
$client = new \CIP\CIPClient($_SERVER['CIP_SERVER']);

if ($handle = opendir('.')) {
	while (false !== ($entry = readdir($handle))) {
		if ($entry != "." && $entry != ".." && $entry != 'run-tests.php') {
			echo "Running $entry\n";
			include($entry);
		}
	}
	closedir($handle);
}
