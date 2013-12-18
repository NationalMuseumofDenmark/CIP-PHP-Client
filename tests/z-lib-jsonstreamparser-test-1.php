<?php

$opts = array(
		'http' => array(
				'method'=>"POST",
				'header'=>"Accept-language: en\r\n",
				'content' => 'querystring=%22Record+Name%22+%2A+or+%22Record+Name%22+%21%2A'
		)
);

$context = stream_context_create($opts);

/* Sends an http request to www.example.com
 with additional headers shown above */
//$fp = fopen('http://www.example.com', 'r', false, $context);
$t1 = microtime(true);
$stream = fopen('http://samlinger.natmus.dk/CIP/metadata/search/FHM/web;jsessionid=C55078FEF0D6CE65FA547D8CEA60DDEB?apiversion=4', 'r', false, $context);
$listener = new \json\ArrayMaker();
try {
	$parser = new \JsonStreamingParser_Parser($stream, $listener);
	$t2 = microtime(true);
	$parser->parse();
	$t3 = microtime(true);
	var_dump($listener->get_json());
	$t4 = microtime(true);
} catch (Exception $e) {
	fclose($stream);
	throw $e;
}

echo '$t2 - $t1 = ' . ($t2 - $t1) . "\n";
echo '$t3 - $t2 = ' . ($t3 - $t2) . "\n";
echo '$t4 - $t3 = ' . ($t4 - $t3) . "\n";

/*
$result = $client->metadata()->search_with_layout('FHM', 'web', null, null, '"Record Name" * or "Record Name" !*');
var_dump($result);
*/