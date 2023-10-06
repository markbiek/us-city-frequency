<?php

// Test

require_once __DIR__ . '/vendor/autoload.php';

use League\Csv\Reader;

$csv = Reader::createFromPath(__DIR__ . '/uscities.csv', 'r');
$csv->setHeaderOffset(0);
$header_offset = $csv->getHeaderOffset();
$header = $csv->getHeader(); 

$results = [];

$records = $csv->getRecords();
$cities = [];
$min_pop = null;
$max_pop = null;
foreach ($records as $offset => $record) {
	$cities[] = $record['city_ascii'];
	$clean_name = preg_replace('/[^A-Za-z0-9]/', '', $record['city_ascii']);

	if ( ! isset($results[$clean_name])) {
		$results[$clean_name] = [
			'count' => 0,
			'name' => $record['city_ascii'],
		];
	}

	$results[$clean_name]['count']++;

	if (! $min_pop || ($record['population'] < $min_pop['population'] && $record['population'] > 0)) {
		$min_pop = $record;
	}

	if (! $max_pop || $record['population'] > $max_pop['population']) {
		$max_pop = $record;
	}
}

uasort($results, function($a, $b) {
	if ($a['count'] == $b['count']) {
		return 0;
	}

	return $a['count'] > $b['count'] ? -1 : 1;
});

$common_cities = array_filter($results, function($city) {
	return $city['count'] >= 15;
});

$unique_cities = array_filter($results, function($city) {
	return $city['count'] === 1;
});

$unique_city_count = count($unique_cities);
$total_city_count = count($cities);

echo "Total cities: $total_city_count<br />";
echo "Unique cities: $unique_city_count<br />";
echo "Percent unique: " . round($unique_city_count / $total_city_count * 100, 2) . "%<br /><br />";

echo "Largest population: {$max_pop['city_ascii']}, {$max_pop['state_id']} &mdash; " . number_format($max_pop['population']) . "<br />";
echo "Smallest population: {$min_pop['city_ascii']}, {$min_pop['state_id']} &mdash; " . number_format($min_pop['population']) . "<br />";

echo "<br />Common cities:<br />";
foreach ($common_cities as $city) {
	echo $city['name'] . ' (' . $city['count'] . ')<br />';
}
