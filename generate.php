<?php

$csv = file_get_contents("csv.csv");
$lines = explode("\n", $csv);
$array = array();
$count = 0;
foreach ($lines as $line) {
    if ($count == 0 || empty($line)) {
        $count++;
        continue;
    }
    $parts = explode(",",$line);
    print_r($parts);
    $a = array();
    $a["name"] = $parts[0];
    $a["latlong"] = get_lat_long($parts[5]);
    $a["image"] = get_image($parts[7],$parts[8],$parts[9]);
    $a["level"] = $parts[10];
    $a["visible"] = false;
    $array[] = $a;
    $count++;
}

print_r($array);
echo json_encode($array);

function get_lat_long($address){

    $address = str_replace(" ", "+", $address);

    $json = file_get_contents("https://maps.google.com/maps/api/geocode/json?address=$address&sensor=false&region=Australia&key=");
    try {
        $json = json_decode($json,true);
        print_r($json);
        if (empty($json) || !isset($json['results'][0])) return;

        $lat = $json['results'][0]['geometry']['location']['lat'];
        $long = $json['results'][0]['geometry']['location']['lng'];
        return array($lat,$long);
    } catch (Exception $e) {
        return array();
    }
}

function get_image($color,$grey,$green) {
    $svg = '<svg height="220" version="1.1" width="220" xmlns="http://www.w3.org/2000/svg" style="overflow: hidden; position: relative; left: 0px;">
        <circle cx="110" cy="110" r="100" fill="'.($color != "black" ? "#FFFF00" : "#000000").'" stroke="" style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0); ;"></circle>
        <circle cx="110" cy="110" r="'.($grey*100).'" fill="#C8C8C8" stroke="" style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0); ;"></circle>
        <circle cx="110" cy="110" r="'.($green*100).'" fill="#80BD00" stroke="" style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0); ;"></circle>
    </svg>';

    $im = new Imagick();
    $im->setBackgroundColor(new ImagickPixel('transparent'));
    $im->readImageBlob('<?xml version="1.0" encoding="UTF-8" standalone="no"?>'.$svg);
    $im->setImageFormat("png32");
    $filename = "images1/powerspot-".time().".png";
    $im->writeImage($filename);
    return $filename;
}