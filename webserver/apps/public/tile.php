<?php
include "../lib/zxy.php";
$path = $_REQUEST['path'];

if (isset($_REQUEST['debug'])) {
  $debug = true;
} else {
  $debug = false;
}

function check_api_key($key) {
  $installed_keys = explode("\n",file_get_contents("../apikeys.txt"));
  // var_dump($installed_keys);
  foreach ($installed_keys as $k => $line) {
    $a = explode(" ",$line);
    $tok = $a[0];
    $aid = $a[1];
    // echo "KEY:$tok , $aid";
    if ($key == $tok) {
        // echo "Match token. allow access $key";
        return true;
    }
  }
  return false;
}

if (isset($_REQUEST['key'])) {    // echo "key:".$_REQUEST['key'];
  $key = $_REQUEST['key'];
  $key_ok = check_api_key($key);
  if (!$key_ok) {
    echo "Bad API Token: key=$key";
  exit;
  }
} else {
  $key = false;
  echo "No API Key"; exit;
}

$chunk = explode('/',$path);
$ver = $chunk[0];
$lang = $chunk[1];
$z = intval($chunk[2]);
$x = intval($chunk[3]);
$y = intval($chunk[4]);

$osm_tile = new ZXY();
$url = $osm_tile->getMapTile($z, $x, $y);

// Redirect
// header('Location: '.$url);

$imgfile = $osm_tile->getTileImagePath($z,$x,$y);

//var_dump(file_exists($imgfile), $imgfile);
//exit;
header('Content-Type: image/png');
readfile($imgfile);
die();
?>


<h1>POWERMAP Tile (OSM-Compatible)</h1>
<pre>
Reference:
http://wiki.openstreetmap.org/wiki/Tiles


Example:

http://c.tile.openstreetmap.org/11/1597/943.png

https://tile.powermap.in.th/osm/v2/th/11/1597/943.png
</pre>
<hr>


<?php

echo "<pre>X=$x Y=$y Z=$z VERSION=$ver LANGUAGE=$lang</pre>";



echo "<pre>URL: $url</pre>";

    var_dump($osm_tile->getTileImagePath(array($z,$x,$y)));


if ( $debug ) {

    var_dump($ver);
    var_dump($lang);
    var_dump($z);

    var_dump($key);
    var_dump($chunk);
    var_dump($url);

} else {
    header('Location: '.$url);
    // $t->getTileImage();

    // file_exists($filepath);  // boolean


}
