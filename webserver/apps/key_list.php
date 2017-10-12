<?php

$keys = array(
"e10adc3949ba59abbe56e057f20f883e",
"test2"
);

/**
  * Check if API key parameter is in request
  **/
function has_api_key() {
    // check api key from key list
    if (isset($_GET['key'])) {
        return true;
    } else {
        return false;
    }
}


/**
  * Check if API key is valid
  **/
function valid_api_key() {
    global $keys; //FIX-ME
    $valid = false;
    if (has_api_key()) {
        if (in_array($_GET['key'], $keys)){
            $valid = true;
        }
    }
    return $valid;
}

?>
