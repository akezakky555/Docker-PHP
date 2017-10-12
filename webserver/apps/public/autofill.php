<?php
/**
 * Copyright 2017-present, AAPICO ITS Co., Ltd. All rights reserved.
 **/

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
// Yields $params associative array.
function parse_autofill_query() {
    $input_params = array();
    // check query exists, length of text more than zero but not exceed 250 chars
    if (isset($_GET['query'])) {
        $query = $_GET['query'];
        if (strlen($query) == 0 || strlen($query) > 250) {
            $error_msg = "Query length must not be zero and cannot exceed 250. length=".strlen($query);
            response_error('BadQuery', $error_msg);
        } 
    } else {
        $error_msg = "no query.";
        response_error('BadQuery', $error_msg);
    };
    $input_params['query'] = $query;
    $input_params['size'] = strlen($query);

    if (isset($_GET['limit'])){
        $limit = $_GET['limit'];
    } else {
        $limit = 10;
    };
    $input_params['limit'] = $limit;
    return $input_params;
}
$params = parse_autofill_query();

function autofill_name($params) {
    global $category_en;
    global $category_th;
    $conn = connect_database();
    $limit = $params['limit'];
    $query = $params['query'];
    if (preg_match('/[\x{0E00}-\x{0E7F}]/u', $query) === 1) {
        // Thai
//var_dump("Thai".$query);
        $result = pg_query($conn, "SELECT name_t as name_th, name_e as name_en, ST_X (ST_Transform (geom, 4326)) as lng, ST_Y (ST_Transform (geom, 4326)) as lat, addr_t, addr_e, new_type as type_e, new_type as type_t, tambon_t, tambon_e, amphoe_t, amphoe_e, province_t, province_e from poi_score WHERE name_t ILIKE '%$query%' limit $limit");
    } else {
        // English
        $result = pg_query($conn, "SELECT name_e as name_en, name_t as name_th, ST_X (ST_Transform (geom, 4326)) as lng, ST_Y (ST_Transform (geom, 4326)) as lat, addr_e, addr_t, new_type as type_e, new_type as type_t, tambon_e, tambon_t, amphoe_e, amphoe_t, province_e, province_t from poi_score WHERE name_e ILIKE '%$query%' limit $limit");
    }

    if (!$result) {
      response_error('Error', 'Broken trying to hit database: ' . pg_last_error());
    }

    $results = pg_fetch_all($result);

    $final_result = array();
    foreach($results as $item){
        $item = replace_category('type_e', $category_en, $item);
        $item = replace_category('type_t', $category_th, $item);
        $item = replace_address_en('addr_e', $item);
        $item = replace_address_th('addr_t', $item);
        $final_result[]=$item;
    };
    pg_close($conn);
    return $final_result;
}
// echo "Getting POI from Database";
$final_result = autofill_name($params);

// Format output JSON
$response_code = "Ok";
$inputs = $params;
$output = array("code" => $response_code, "input" => $inputs, "results" => $final_result);

// Finish PHP
header("Content-type:application/json");
print json_encode($output, JSON_PRETTY_PRINT);
exit;
?>
