<?php
// Testing the seach with layout functionality.
$response = $client->metadata()->search_with_layout($catalog_alias, $catalog_view, "a");
assert('$response["totalcount"] > 0');

// Asserting that the Date response filter kicked in, while "asset_creation_date" would only be advailable if the field was mapped.
assert('$response["items"][0]["asset_creation_date"] instanceof \DateTime');

