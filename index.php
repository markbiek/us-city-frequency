<?php

require_once __DIR__ . '/vendor/autoload.php';

use League\Csv\Reader;

$csv = Reader::createFromPath(__DIR__ . '/uscities.csv', 'r');
$csv->setHeaderOffset(0);
$header_offset = $csv->getHeaderOffset();
$header = $csv->getHeader(); 

$results = [];

$records = $csv->getRecords();
foreach ($records as $offset => $record) {
	$clean_name = preg_replace('/[^A-Za-z0-9]/', '', $record['city_ascii']);

	if ( ! isset($results[$clean_name])) {
		$results[$clean_name] = [
			'count' => 0,
			'name' => $record['city_ascii'],
		];
	}

	$results[$clean_name]['count']++;
}

echo '<pre>' . print_r($results) . '</pre>';
