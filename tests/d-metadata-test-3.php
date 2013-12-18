<?php
$result = $client->metadata()->search_with_layout('FHM', 'web', null, null, '"Record Name" * or "Record Name" !*');
//var_dump($result);
