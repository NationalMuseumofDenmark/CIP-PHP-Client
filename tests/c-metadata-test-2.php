<?php

$response = $client->metadata()->search_with_layout($catalog_alias, $catalog_view, "a");
var_dump($response);
//assert('$response["totalcount"] > 0');
