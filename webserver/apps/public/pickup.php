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
function parse_pickup_event() {
    $input_params = array();
    // check query exists, length of text more than zero but not exceed 250 chars
    if (isset($_GET['ride_id'])) {
        $ride_id = $_GET['ride_id'];
        if (strlen($ride_id) == 0 || strlen($ride_id) > 250) {
            $error_msg = "Ride ID length must not be zero and cannot exceed 250. length=".strlen($ride_id);
            response_error('BadEvent', $error_msg);
        } 
    } else {
        $error_msg = "no ride_id.";
        response_error('BadEvent', $error_msg);
    };
    $input_params['ride_id'] = $ride_id;

    if (isset($_GET['pickup_time'])) {
        $pickup_time = $_GET['pickup_time'];
        if (strlen($pickup_time) == 0 || strlen($pickup_time) > 250) {
            $error_msg = "Pickup time length must not be zero and cannot exceed 250. length=".strlen($pickup_time);
            response_error('BadEvent', $error_msg);
        } 
    } else {
        $error_msg = "no pickup_time.";
        response_error('BadEvent', $error_msg);
    };

    $input_params['pickup_time'] = $pickup_time;

    return $input_params;
}
$params = parse_pickup_event();

// Format output JSON
$response_code = "Ok";
$inputs = $params;
$output = array("code" => $response_code, "event" => $inputs);

// Finish PHP
header("Content-type:application/json");
print json_encode($output, JSON_PRETTY_PRINT);
exit;
?>
