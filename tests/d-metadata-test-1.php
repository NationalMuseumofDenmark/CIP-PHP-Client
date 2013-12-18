<?php
// Opening a session
$response = $client->metadata()->getcatalogs();
// var_dump($response);

// The environment variables should be specified.
assert('array_key_exists("TEST_CATALOG_NAME", $_SERVER)');
assert('array_key_exists("TEST_CATALOG_ALIAS", $_SERVER)');
assert('array_key_exists("TEST_CATALOG_VIEW", $_SERVER)');

$catalog_name = $_SERVER['TEST_CATALOG_NAME'];
$catalog_alias = $_SERVER['TEST_CATALOG_ALIAS'];
$catalog_view = $_SERVER['TEST_CATALOG_VIEW'];

assert('array_key_exists("catalogs", $response)');

$catalog_id = null;
foreach($response['catalogs'] as $catalog) {
	if($catalog['name'] = $catalog_name) {
		$catalog_id = $catalog['id'];
	}
}

assert('$catalog_id !== null');
echo "Testing catalog is: $catalog_id\n";
$catalog_name = urlencode($catalog_name);
// $response = $client->metadata()->getlayout($catalog_name);
// var_dump($response);

$response = $client->metadata()->search($catalog_alias, $catalog_view, 'a');
// var_dump($response);
assert('$response["totalcount"] > 0');
