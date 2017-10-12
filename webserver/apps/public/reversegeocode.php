<?php
/**
 * Copyright 2017-present, AAPICO ITS Co., Ltd. All rights reserved.
 *
 * 1. use nearest API from OSRM
 * 2. use POI database from PostgreSQL
 * Full documnets of OSRM api: http://project-osrm.org/docs/v5.5.1/api/ 
 *
 **/

require ('../reversegeocode_config.php');
require ('../db_config.php');
require ('../lib/functions.php');
include ('../lib/category.php');
include ('../lib/helper.php');
include ('../key_list.php');

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

function find_route($params) {
    // $server defined in config.
    $server = OSRM_SERVICE;
    $latlng = $params['lng'].",".$params['lat'];
    $url =  $server. $latlng . '?number=3';
    // echo $url;
    $ch = curl_init();
    curl_setopt($ch,CURLOPT_URL,$url);
    curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
    //  curl_setopt($ch,CURLOPT_HEADER, false);
    $output=curl_exec($ch);
    // parse into standard class
    $json = json_decode($output);
    // var_dump($json->waypoints);
    curl_close($ch);
    $results = array();
    // Extract each returned location.
    foreach ($json->waypoints as $point) {
        // road name
        if (strlen($point->name) < 1) {
          $name_t = "ถนนไม่มีชื่อ";
          $name_e = "Unnamed Road";
        } else {
          $name_t = $point->name;
          $name_e = $point->name;
        }
        $location = array("road_e" => $name_e, "road_t" => $name_t, "distance" => $point->distance, "lat" => $point->location[1], "lng" => $point->location[0]);
        $results[] = $location;
    }
    // var_dump($results); exit;
    $routes = array("route" => $results);
    return $routes;
}
// echo "Getting Road from ORSM";
$routes = find_route($params);

function find_address($params) {
    global $category_en;
    global $category_th;
    $conn = connect_database();
    $lat = $params['lat'];
    $lng = $params['lng'];
    $limit = $params['limit'];
    $result = pg_query($conn, "SELECT name_t, name_e, ST_X (ST_Transform (geom, 4326)) as lng, ST_Y (ST_Transform (geom, 4326)) as lat, addr_t, addr_e, new_type as type_e, new_type as type_t, tambon_t, tambon_e, amphoe_t, amphoe_e, province_t, province_e FROM poi_score WHERE geom
        @ -- contained by, gets fewer rows -- ONE YOU NEED!
        ST_MakeEnvelope (
            $lng+0.1, $lat+0.1, -- bounding 
            $lng-0.1, $lat-0.1,  -- box limits
            4326) ORDER BY ST_Distance(geom, ST_SetSRID(ST_MakePoint($lng, $lat), 4326)) limit $limit");
    if (!$result) {
      response_error('Error', 'Broken trying to hit database.');
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
    $addresses = array("address" => $final_result);
    return $addresses;
}
// echo "Getting POI from Database";
$addresses = find_address($params);

// Combind route and address results.
$final_result = array_merge($routes, $addresses);

// Format output JSON
$response_code = "Ok";
$input = array("lat" => $params['lat'], "lng" => $params['lng'], "limit" => $params['limit'] );
$output = array("code" => $response_code, "input" => $input );
$output["results"] = $final_result;

// Finish PHP
header("Content-type:application/json");
print json_encode($output, JSON_PRETTY_PRINT);
exit;
?>
