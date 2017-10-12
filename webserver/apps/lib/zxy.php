<?php


class TilePoint {
    public $x;
    public $y;
    public $z;


    public function __construct($x,$y,$z) {
         $this->x = $x;
         $this->y = $y;
         $this->z = $z;
    }
}


/**
 *
 */
class ZXY
{

    function getMapTile($z, $x, $y)
    {
        /**
         * This returns open street tiles.
         */
//        return 1;
        $access_token = "pk.eyJ1IjoiY2hveWtpa2kiLCJhIjoiY2l3dmw1a3hrMDBvbjJvbWF2cXVyYjM2biJ9.xB6-YMvd3jXXv0yn_fsDAg";
//        $OPEN_STREET_MAP_URL_FORMAT = "http://b.tile.openstreetmap.org/%d/%d/%d.png";

        $MAP_BOX_URL_FORMAT = "https://api.mapbox.com/styles/v1/mapbox/streets-v9/tiles/256/%d/%d/%d?access_token=".$access_token;
        if (!$this->checkTileExists($x, $y, $z)) {
            $s = sprintf($MAP_BOX_URL_FORMAT, $z, $x, $y);
        } else {
            $s = $this->getTilesURL($this->adjustTilePoint($x, $y, $z));
            $file_headers = @get_headers($s);
            if(!$file_headers || $file_headers[0] == 'HTTP/1.1 404 Not Found') {
                $s = sprintf($MAP_BOX_URL_FORMAT, $z, $x, $y);
            }
        }
        return $s;
    }

    /**
     * Returns tilePoint array
     **/
    function adjustTilePoint($x, $y, $z)
    {
        $limit = $this->getWrapTileNum($z);
        $dx = ((($x % $limit) + $limit) % $limit);
        $dy = $limit - $y - 1;
        $dz = $z;
        return array($dx, $dy, $dz);
    }

    function getTileImagePath($z, $x, $y, $version="v2", $language="th")
    {
        $label = "PM_GeoMap_th";
        $TILE_VERSION = [
            "v1"."th" => "PM_GeoMap_th", 
            "v1"."en" => "PM_GeoMap_en", 
            "v2"."th" => "PM_Map_V2_T", 
        ];
        $label = $TILE_VERSION[$version.$language];

        $tilePoint = $this->adjustTilePoint($x, $y, $z);
        $X = 0;
        $Y = 1;
        $Z = 2;
        $z = $this->padZeros($tilePoint[$Z], 2);
        $dirX = $this->padZeros(floor($tilePoint[$X] / (pow(2, floor(1 + ($tilePoint[$Z] / 2))))), floor($tilePoint[$Z] / 6) + 1);
        $dirY = $this->padZeros(floor($tilePoint[$Y] / (pow(2, floor(1 + ($tilePoint[$Z] / 2))))), floor($tilePoint[$Z] / 6) + 1);
        $x = $this->padZeros($tilePoint[$X], 2 + (floor(floor($tilePoint[$Z] / 6) * 2)));
        $y = $this->padZeros($tilePoint[$Y], 2 + (floor(floor($tilePoint[$Z] / 6) * 2)));

        $TILE_PATH = "/map/powermap/$label/EPSG_900913_%s/%s_%s/%s_%s.png";
        $s = sprintf($TILE_PATH, $z, $dirX, $dirY, $x, $y);

        return $s;
    }


    function getTilesURL($tilePoint)
    {
        $POWER_MAP_URL_FORMAT = "http://tile.powermap.in.th/v1/tile/th/EPSG_900913_%s/%s_%s/%s_%s.png";
        $X = 0;
        $Y = 1;
        $Z = 2;
        $z = $this->padZeros($tilePoint[$Z], 2);

        $dirX = $this->padZeros(floor($tilePoint[$X] / (pow(2, floor(1 + ($tilePoint[$Z] / 2))))), floor($tilePoint[$Z] / 6) + 1);
        $dirY = $this->padZeros(floor($tilePoint[$Y] / (pow(2, floor(1 + ($tilePoint[$Z] / 2))))), floor($tilePoint[$Z] / 6) + 1);
        $x = $this->padZeros($tilePoint[$X], 2 + (floor(floor($tilePoint[$Z] / 6) * 2)));
        $y = $this->padZeros($tilePoint[$Y], 2 + (floor(floor($tilePoint[$Z] / 6) * 2)));
        $s = sprintf($POWER_MAP_URL_FORMAT, $z, $dirX, $dirY, $x, $y);
        return $s;
    }

    function padZeros($unPaddedInt, $padReq)
    {
        $padded = (String)$unPaddedInt;

        while (strlen($padded) < $padReq) {
            $padded = '0'.$padded;
        }

        return $padded;
    }


    function checkTileExists($x, $y, $zoom)
    {
        $minZoom = 6;
        $maxZoom = 18;

        if (($zoom < $minZoom || $zoom > $maxZoom)) {
            return false;
        }

        return true;
    }

    function getWrapTileNum($z)
    {
        return pow(2, $z);
    }
}
