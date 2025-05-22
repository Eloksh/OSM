<?php
header('Content-Type: text/html; charset=utf-8');
// <meta http-equiv="Content-type" content="text/html; charset=utf-8" />
$tag = "";
$tag = htmlspecialchars($_GET["tag"]);

$points = file_get_contents('points.geojson');

$filtered = json_decode($points, true);
$new = json_decode('{"type":"FeatureCollection","features":[]}', true);

$i = 0;
if ($tag !== "") {
    foreach ($filtered["features"] as $item) {
        if($item["properties"]["tag"] == $tag) {
            $new["features"][] = $item;
        }
        $i++;
    }
    
}

echo json_encode($new);


?>
