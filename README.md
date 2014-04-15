# CIP PHP Client

An independent implementation of a PHP Client for the Canto Integration Platform, developped for [the National Museum of Denmark](http://digital.natmus.dk/).

# Getting started!
## Clone the code onto your machine, running

	git clone https://github.com/NationalMuseumofDenmark/CIP-PHP-Client.git

Or simply download a pre-bundled ZIP-package from http://natmus.demo.bitblueprint.com/cip-php-client/dist/cip-php-client.zip
	
## Require the CIPClient.php and start experimenting

Ones downloaded, you can start experimenting, for inspiration look into example.php

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
	
## Documentation
The source code of the CIP-PHP-Client is annotated with documentation, which is published on http://natmus.demo.bitblueprint.com/cip-php-client/doc/

### Buidling the documentation
If you make changes to the sourcecode and want to build the documentation, you need a tool such as phpdoc.

First, make sure you have installed the PhpDocumentor following the installation guide at http://manual.phpdoc.org/HTMLSmartyConverter/HandS/ric_INSTALL.html, essentially telling you to run

    pear upgrade PhpDocumentor
    
Run the command to generate the HTML documentation from the source-code, which looks something like

    phpdoc --title "Natmus CIP-PHP-Client" --defaultpackagename "Natmus CIP-PHP-Client" -t doc/ -d src/

## Running the tests

To run the tests first defining the environment variables, then change directory into the tests folder and running the run-tests.php script using the command-line PHP interpreter.

	export CIP_SERVER="http://samlinger.natmus.dk/";
	export CIP_USER="...";
	export CIP_PASSWORD="...";
	export CIP_SERVERADDRESS="localhost";
	export TEST_CATALOG_ALIAS="...";
	export TEST_CATALOG_NAME="...";
	export TEST_CATALOG_VIEW="web";
	export DEBUGGING="true";
	cd tests;
	php run-tests.php;

