<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
// Include and bootstrap the client.
require("./vendor/autoload.php");
// Instantiate the CIP client.
$client = new \CIP\CIPClient('http://samlinger.natmus.dk/', false);
// Request the system version.
$response = $client->system()->getversion();
// Print the version returned from the service.
print_r($response['version']);
?>
