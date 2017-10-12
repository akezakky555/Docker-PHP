<?php
/**
 * Copyright 2017-present, AAPICO ITS Co., Ltd. All rights reserved.
 **/

require ('../db_config.php');
require ('../lib/functions.php');
include ('../lib/category.php');
include ('../lib/helper.php');
include( '../key_list.php');

header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Credentials: true");
header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
header('Access-Control-Max-Age: 1000');
header('Access-Control-Allow-Headers: Origin, Content-Type, X-Auth-Token , Authorization');

// Check API access.
if (!has_api_key()) { response_error('NotAuthorized', 'api-key is not given.'); }
if (!valid_api_key()) { response_error('NotAuthorized', 'api-key is not correct.'); }

// Parse input parameters and return error response if input is invalid.
// Yields $params associative array. Use parse_query() in helper.php
$params = parse_query();

var_dump($params);
$conn = connect_database();

$lat = $params['lat'];
$lng = $params['lng'];
$limit = $params['limit'];
$distance_calc = " ST_Distance(geom, ST_SetSRID(ST_MakePoint($lng, $lat), 4326)) as distance ";

$result = pg_query($conn, "SELECT name_t, name_e, ST_X (ST_Transform (geom, 4326)) as longitude, ST_Y (ST_Transform (geom, 4326)) as latitude, addr_t, addr_e, new_type as type_e, new_type as type_t, tambon_t, tambon_e, amphoe_t, amphoe_e, province_t, province_e, $distance_calc FROM poi_score WHERE geom
    @ -- contained by, gets fewer rows -- ONE YOU NEED!
    ST_MakeEnvelope (
        $lng+0.1, $lat+0.1, -- bounding 
        $lng-0.1, $lat-0.1,  -- box limits
        4326) ORDER BY ST_Distance(geom, ST_SetSRID(ST_MakePoint($lng, $lat), 4326)) limit $limit");

if (!$result) {
    response_error('Error', 'Broken trying to hit database: ' . pg_last_error());
}

$final_result = array();
if (pg_num_rows($result) === 0){
	$final_result[] = 'no result found.';
} else { 
	$results = pg_fetch_all($result);
	foreach($results as $item){
		$item = replace_category('type_e', $category_en, $item);
		$item = replace_category('type_t', $category_th, $item);
		$item = replace_address_en('addr_e', $item);
		$item = replace_address_th('addr_t', $item);

		$final_result[]=$item;
	};
};
pg_close($conn);

// Format output JSON
$response_code = "Ok";
$input_coordinate = array("latitude" => $lat, "longitude" => $lng );
$output = array("code" => $response_code, "input" => $input_coordinate );
$output["results"] = $final_result;

// Finish PHP
header("Content-type:application/json");
print json_encode($output, JSON_PRETTY_PRINT);
exit;
?>
