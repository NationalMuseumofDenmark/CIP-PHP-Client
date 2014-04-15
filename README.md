# CIP PHP Client

An independent implementation of a PHP Client for the Canto Integration Platform, developped for [the National Museum of Denmark](http://digital.natmus.dk/).

# Getting started!


	<?php
	// Include and bootstrap the client.
	require("./src/CIP/CIPClient.php");
	
	// Instantiate the CIP client.
	$client = new \CIP\CIPClient('http://samlinger.natmus.dk/', false);
	
	// Request the system version.
	$response = $client->system()->getversion();
	
	// Print the version returned from the service.
	print_r($response['version']);
	?>

## Running the tests

To run the tests first defining the environment variables:

	export CIP_SERVER="http://samlinger.natmus.dk/";
	export CIP_USER="...";
	export CIP_PASSWORD="...";
	export CIP_SERVERADDRESS="localhost";
	export TEST_CATALOG_ALIAS="...";
	export TEST_CATALOG_NAME="...";
	export TEST_CATALOG_VIEW="web";
	export DEBUGGING="true";

then change directory into the tests folder and running the run-tests.php script using the command-line PHP interpreter.

## Buidling the documentation

First, make sure you have installed the PhpDocumentor following the installation guide at http://manual.phpdoc.org/HTMLSmartyConverter/HandS/ric_INSTALL.html, essentially telling you to run

    pear upgrade PhpDocumentor

