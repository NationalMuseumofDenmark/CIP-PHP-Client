<?php
// Opening a session
$response = $client->session()->open();

// It must have a session id
assert(array_key_exists('jsessionid', $response));
$jsessionid = $response['jsessionid'];

// The session id should be 32 chars long.
assert(strlen($jsessionid) == 32);

// Trying to close the session.
$response = $client->session()->close();
// Assert a void response.
assert($response === true);