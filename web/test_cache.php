<?php
require './includes/rediscache.php';

$data = Array("test data" => Array("More Test Data" => "Yes"));

if(!cache_exists('test'))
	cache_obj_set('test', $data);

echo print_r(cache_obj_get('test'), true) . "<br />Cache valid for another " . $rediscache->ttl('test') . " secs";


