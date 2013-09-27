<?php
echo "Simple Test #1 started\n";

// Loading the client library.
require_once '../src/CIP/CIPClient.php';

// Creating a CIP Client.
$client = new \CIP\CIPClient('http://samlinger.natmus.dk/', 80);

// Listing the operations
$reponse = $client->system()->getversion();
assert(array_key_exists('version', $reponse));
assert(count(array_keys($reponse['version'])) == 5);

$reponse = $client->metadata()->getcatalogs(null, 'user', 'password', 'sa');
var_dump($reponse);