# What is this?

The National Museum of Denmark keeps all of its assets and metadata in an asset management system called Cumulus. It is used to keep track of the digitization effort, licensing and many other things pertaining to keeping a digital collection. Cumulus exposes its data for internal and external products, programmers and hobbyists in a [RESTful API called CIP](http://samlinger.natmus.dk/CIP/doc/CIP.html). The SDK described in this document wraps the CIP API for use in a PHP project.

CIP PHP Client is an independent implementation of a PHP Client for the Canto Integration Platform, developped for [the National Museum of Denmark](http://digital.natmus.dk/).

# Getting started!

## Clone the code onto your machine, running

	git clone https://github.com/NationalMuseumofDenmark/CIP-PHP-Client.git

Or simply download a pre-bundled ZIP-package from http://natmus.demo.bitblueprint.com/cip-php-client/dist/cip-php-client.zip
	
## Require the CIPClient.php and start experimenting

Ones downloaded, you can start experimenting, for inspiration look into example.php

	<?php
	// Include and bootstrap the client.
	require("./vendor/autoload.php");
	
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

Make sure you have the phpunit CLI tool downloaded: http://phpunit.de/getting-started.html

To run the tests first defining the environment variables, then invoke the phpunit tool as follows.

	export CIP_SERVER="http://samlinger.natmus.dk/";
	export CIP_USER="...";
	export CIP_PASSWORD="...";
	export CIP_SERVERADDRESS="localhost";
	export TEST_CATALOG_ALIAS="...";
	export TEST_CATALOG_NAME="...";
	export TEST_CATALOG_VIEW="web";
	export DEBUGGING="true";
	phpunit --debug --bootstrap vendor/autoload.php .

The runtime option --bootstrap tells PHPUnit to use the composer class-loader.
