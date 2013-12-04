<?php
// Opening a session
$response = $client->session()->open();

// It must have a session id
assert('array_key_exists("jsessionid", $response)');
$jsessionid = $response['jsessionid'];

// The session id should be 32 chars long.
assert('strlen($jsessionid) == 32');

// Trying to close the session.
$response = $client->session()->close();

// Assert a void response.
assert('$response === true');
assert('$client->getSessionID() === null');

// The CIP_USER environment variable should be specified.
assert('array_key_exists("CIP_USER", $_SERVER)');
// The CIP_PASSWORD environment variable should be specified.
assert('array_key_exists("CIP_PASSWORD", $_SERVER)');
// The CIP_SERVERADDRESS environment variable should be specified.
assert('array_key_exists("CIP_SERVERADDRESS", $_SERVER)');

$client->setDAMCredentials($_SERVER['CIP_USER'], $_SERVER['CIP_PASSWORD'], $_SERVER['CIP_SERVERADDRESS']);
