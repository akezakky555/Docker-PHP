<?php
$DB_USER = "powermap";
$DB_PASSWORD = "L2eAwztskJHSgb";
$DB_DATABASE = "powermap_core";
$DB_HOST = "127.0.0.1";

function connect_database() {
    global $DB_HOST; // kinda iffy...
    global $DB_DATABASE;
    global $DB_USER;
    global $DB_PASSWORD;
    $conn_string = "host=$DB_HOST port=5432 dbname=$DB_DATABASE user=$DB_USER password=$DB_PASSWORD";
    $conn = pg_pconnect($conn_string);
    return $conn;
}

?>
