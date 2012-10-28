<?php

/*

Esimerkki:
.../map.php?STYLE=peruskartta&LAT=61.000&LON=25.000&RADIUS=1500&WIDTH=500&HEIGHT=500&CROSSHAIR

*/

// ----------------------------------------------
// Functions

function getParameter($name)
{
	if (@$_GET[$name])
	{
		if (is_numeric($_GET[$name]))
		{
			return $_GET[$name];
		}
		else
		{
			return FALSE;
		}
	}
	else
	{
		return FALSE;
	}
}

// ----------------------------------------------
// Parameters

$service = "WMS";
$version = "1.1.1";
$request = "GetMap";
$bgcolor = "0xffffff";
$srs = "EPSG:3067";
$transparent = "false";
$format = "image/png";
$bbox = "";

$width = getParameter('WIDTH');
$height = getParameter('HEIGHT');
$lat = getParameter('LAT');
$lon = getParameter('LON');
$radius = getParameter('RADIUS');

if ("peruskartta" == $_GET['STYLE'] || "ortokuva" == $_GET['STYLE'] || "taustakartta" == $_GET['STYLE'])
{
	$style = $_GET['STYLE'];
}
else
{
	$style = "peruskartta";
}

// ----------------------------------------------
// Coordinates

// Muunnos wgs84 -> trs-tm35fin, käyttää Luomuksen avointa muunnospalvelua
// http://www.luomus.fi/projects/coordinateservice/info/
$url = "http://www.luomus.fi/projects/coordinateservice/json/?from=WGS84&to=ETRS-TM35FIN&n=$lat&e=$lon";
$json = file_get_contents($url);
$coord = json_decode($json, TRUE);
	
// Generate bounding box
$bbox = ($coord['E'] - $radius) . "," . ($coord['N'] - $radius) . "," . ($coord['E'] + $radius) . "," . ($coord['N'] + $radius);

// ----------------------------------------------
// Get map & send to UA


// KAPSI
$url = "http://tiles.kartat.kapsi.fi/$style?SERVICE=WMS&VERSION=1.1.1&REQUEST=GetMap&WIDTH=$width&HEIGHT=$height&SRS=EPSG:3067&BBOX=$bbox&FORMAT=image/png";

//echo "<p>"; print_r ($url); exit(); // debug


$mapData = file_get_contents($url);
$im = imagecreatefromstring($mapData);

if (isset($_GET['CROSSHAIR']))
{
	if ("peruskartta" == $style)
	{
		$lineColor = imagecolorallocate($im, 0, 255, 0);
	}
	elseif ("ortokuva" == $style)
	{
		$lineColor = imagecolorallocate($im, 255, 255, 255);
	}
	else
	{
		$lineColor = imagecolorallocate($im, 0, 0, 0);
	}
	
	imagesetthickness($im, 2);
	imageline($im, 0, 0, $width, $height, $lineColor);
	imageline($im, $width, 0, 0, $height, $lineColor);
}


if ($im !== false) {
	if (isset($_GET['COMPRESS']))
	{
		header('Content-Type: image/jpg');
		imagejpeg($im, NULL, 30);
	}
	else
	{
		header('Content-Type: image/png');
		imagepng($im);
	}


    imagedestroy($im);
}
else {
    echo 'An error occurred.';
}

?>