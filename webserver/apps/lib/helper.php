<?php
/**
 * Copyright 2017-present, AAPICO ITS Co., Ltd. All rights reserved.
 *
 * 1. return_code
 * 2. response_error
 * 3. parse_query       common params  lat,lng,limit
 *
 **/

define("DEFAULT_LATITUDE", 13.74572);
define("DEFAULT_LONGITUDE", 100.53004);
 
$return_code = array(
    'Ok' => "OK",
    'Timeout' => "API call timed out",
    'NotAuthorized' => "something about security",
    'NotDefined' => "Something is wrong ..."
);

function response_error($code, $message, $extra = array()) {
    $response_json = array('code' => $code, 'message' => $message);
    $response_json = array_merge($response_json, $extra);
    // Echo and done.
    header("Content-type:application/json");
    echo json_encode($response_json, JSON_PRETTY_PRINT);
    exit;
}

function parse_query() {
    $input_params = array();
    // Latitude
    if (isset($_GET['lat'])) {
        $lat = $_GET['lat'];
    } else {
        $lat = DEFAULT_LATITUDE;
    }
    $input_params['lat'] = $lat;
    // Longitude
    if (isset($_GET['lat'])) {
        $lng = $_GET['lng'];
    } else {
        $lng = DEFAULT_LATITUDE;
    }
    $input_params['lng'] = $lng;
    // Defaut variables
    if (isset($_GET['limit'])){
        $limit = $_GET['limit'];
        // Force limit to maximum of 100
        if ($limit > 50) { $limit = 50; }
    } else {
        $limit = 10;
    }
    $input_params['limit'] = $limit;
    return $input_params;
}
