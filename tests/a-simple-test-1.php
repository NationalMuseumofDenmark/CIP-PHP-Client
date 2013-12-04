<?php
// Getting the version.
$response = $client->system()->getversion();
assert('array_key_exists("version", $response)');
assert('count(array_keys($response["version"])) == 5');

// Check for compatibility (expected versions, etc).
$client->checkCompatibility();