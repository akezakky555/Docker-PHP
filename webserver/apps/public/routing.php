<?php
/**
 * Copyright 2017-present, AAPICO ITS Co., Ltd. All rights reserved.
 *
 * 1. use route API with driving profile from OSRM
 *
 **/
 
include('../routing_config.php');
header('Access-Control-Allow-Origin: *'); 

if (isset($_GET['waypoint'])){
	$waypoint = $_GET['waypoint'];
} else {
	echo "no input origin or destination";
	exit;
};

$geometries = 'geojson';

if (isset($_GET['steps'])){
	$steps = $_GET['steps'];
} else {
	$steps = 'false';
};

if (isset($_GET['alternatives'])){
	$alternatives = $_GET['alternatives'];
} else {
	$alternatives = 'true';
};

// check api key from key list
if (isset($_GET['key'])){
	if (in_array($_GET['key'], $keys)==0){
		echo "api-key is not correct";
		exit;
	};
} else {
	echo "api-key is not given.";
	exit;
};

/* Redirect browser */
$url =  $server. $waypoint . '?steps='.$steps.'&geometries='. $geometries.'&alternatives='.$alternatives;


// echo httpGet($url);
$output = httpGet($url);
// Finish PHP
header("Content-type:application/json");
print json_encode(json_decode($output), JSON_PRETTY_PRINT);

/* Make sure that code below does not get executed when we redirect. */
//exit;

function httpGet($url)
{
    $ch = curl_init();  
 
    curl_setopt($ch,CURLOPT_URL,$url);
    curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
//  curl_setopt($ch,CURLOPT_HEADER, false); 
 
    $output=curl_exec($ch);
 
    curl_close($ch);
    return $output;
}
 
?>
