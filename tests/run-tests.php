<?php
echo "Running CIP client tests - this should be run using the PHP CLI.\n";

// Loading the client library.
require_once '../vendor/autoload.php';

// Make sure the server is given as an environment variable.
if(!array_key_exists('CIP_SERVER', $_SERVER)) {
	throw new RuntimeException('The CIP_SERVER environment variable should be specified.');
}

function custom_assertion_handler( $script, $line, $message ) {
	if($message) {
		throw new Exception("Assertion failed! $message (line $line)");
	} else {
		throw new Exception("Assertion failed! (line $line)");
	}
}
assert_options(ASSERT_WARNING, false);
assert_options(ASSERT_CALLBACK, 'custom_assertion_handler');

// Creating a CIP Client.
$client = new \CIP\CIPClient($_SERVER['CIP_SERVER'], false);

// Loop throug the directory to collect tests.
$tests = array();
if ($handle = opendir('.')) {
	while (false !== ($entry = readdir($handle))) {
		if ($entry != "." && $entry != ".." && $entry != 'run-tests.php') {
			$tests[] = $entry;
		}
	}
	closedir($handle);
}

// Sort by name.
sort($tests);

// Loop throug the tests, including them and recording fails.
$fails = array();
foreach($tests as $test) {
	echo "====== Running $test ======\n";
	try {
		include $test;
	} catch (Exception $e) {
		$message = "Test $test failed, as an exception was thrown: " . $e->getMessage() . "\n" . $e->getTraceAsString();
		if($e instanceof \CIP\CIPServersideException) {
			$message .= "\n" . $e->getRemoteTraceAsString();
		}
		error_log($message);
		$fails[] = $test;
	}
}

if(count($fails) == 0) {
	echo "====== All OK ======\n";
} else {
	error_log(sprintf("====== Failed %u/%u ======\n", count($fails), count($tests)));
	exit -1;
}
