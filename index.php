<!DOCTYPE html>
<html lang="fi">
<head>
	<meta charset="utf-8" />
	<title>Mobiilikartta</title>
	<meta name="description" content="HTML5 peruskarttaselain" />
	<link href="style.css" rel="stylesheet" />
	<script type="text/javascript" src="../include/jquery.js"></script>

</head>
<body>
	
	<p id="maininfo">Sijaintia ei voi päivittää tiheämmin kuin minuutin välein.</p>

	<p>
		<img src="/0/startmap.png" id="map" alt="" />
		<br />
		<span id="statusinfo"></span>
	</p>
	

	<p id="map_links">
	
		<span id="map_links_1" class="map_link_group">
		<a href="#" id="updatemap">Hae uusi sijainti</a>
		</span>

		<span id="map_links_2" class="map_link_group">
		<a href="#" id="map_peruskartta">Perus</a>
		<a href="#" id="map_ortokuva">Orto</a>
		<a href="#" id="map_vaaravari">Vääräväri</a>
		</span>
		
		<span id="map_links_3" class="map_link_group">
		<a href="#" id="map_korkeuskayrat">Kork</a>
		<a href="#" id="map_vesistot">Vesi</a>
		<a href="#" id="map_pellot">Pelto</a>
		<a href="#" id="map_suot">Suo</a>
		</span>

		<span id="map_links_4" class="map_link_group">
		<a href="#" id="map_kalliot">Kallio/hiekka</a>
		<a href="#" id="map_avohakkuut">Avohakk</a>
		<a href="#" id="map_liikenne">Liikenne</a>
		</span>

		<span id="map_links_5" class="map_link_group">
		<a href="#" id="map_rakennukset">Rakennus</a>
		<a href="#" id="map_kiinteistorajat">Kiinteistö</a>
		<a href="#" id="map_tienimet">Tienimet</a>
		</span>

		<span id="map_links_6" class="map_link_group">
		<a href="#" id="map_radius250">0,5 km</a>
		<a href="#" id="map_radius500">1 km</a>
		<a href="#" id="map_radius1000">2 km</a>
		<a href="#" id="map_radius2000">4 km</a>
		</span>
		
		<span id="map_links_7" class="map_link_group">
		<a href="#" id="map_compress">Low quality</a>
		<a href="#" id="map_uncompress">Full quality</a>
		</span>

	</p>
	
	
	<div id="debug">&nbsp;</div>



<script type="text/javascript">
// -------------------------------------------
// Main program

// Basic vars
window.style = 'peruskartta';
window.radius = '1000';
window.compress = '1';
//window.maximumAge = 60000; // milliseconds

window.positionRequested = false;


// Fetches coordinates from browser
function initGeolocation()
{
	$('#map').addClass("fade");

     if (navigator.geolocation)
     {
		// Call getCurrentPosition with success and failure callbacks
		// See: http://stackoverflow.com/questions/3397585/navigator-geolocation-getcurrentposition-sometimes-works-sometimes-doesnt
		$('#statusinfo').text("Hakee sijaintia selaimestasi, tämä voi kestää hetken...");
		$('#debug').append("<p>Hakee sijaintia selaimestasi, tämä voi kestää hetken..."+getTimeText());
				
		window.watchId = navigator.geolocation.watchPosition(success, fail, { enableHighAccuracy: true, timeout: 10000, maximumAge: 60000 } );
     }
     else
     {
		$('#statusinfo').text("Selaimesi ei tue sijaintitietoja, joten tämä kartta ei toimi siinä.");
     }
}
 
function success(position)
{
	if (true == window.positionRequested)
	{
		window.positionRequested = false;

		var currentTime = new Date();
		var h = currentTime.getHours();
		var m = currentTime.getMinutes();
		window.timeText = h + "." + m;

		// Set results to global vars
		window.lat = position.coords.latitude;
		window.lon = position.coords.longitude;
		window.acc = position.coords.accuracy;
		window.alt = position.coords.altitude;
		window.altAcc = position.coords.altitudeAccuracy;

		// Stop listening
	//	geolocation.clearWatch(window.watchId);

		var url;
		url = '/mobi/mobicoordmap?n=' + position.coords.latitude + '&e=' + position.coords.longitude + '&r=' + position.coords.accuracy + '&alt=' + position.coords.altitude + '&alta=' + position.coords.altitudeAccuracy;

	//	$('#statusinfo').text(url);
		
		setMap();

	}
}
function fail()
{
	if (true == window.positionRequested)
	{
		// Could not obtain location
		$('#statusinfo').text("Sijainnin päivitys epäonnistui. Näytetään vanha sijainti ajalta "+window.timeText+".");
		$('#debug').append("<p>Sijainnin päivitys epäonnistui. Näytetään vanha sijainti ajalta "+window.timeText+". "+getTimeText());

		setMap();
		
		//	$('#debug').text("Sorry, obtaining location from your browser failed. Please try again in a few minutes.");
	}
}

function setMap()
{
	$('#statusinfo').text("Ladataan karttaa...");
	$('#debug').append("<p>Ladataan karttaa..."+getTimeText());
	
	mapUrl = 'http://www.luomus.fi/projects/mmlmap/?L_STYLE='+window.style+'&WIDTH=1000&HEIGHT=1000&L_CSYSTEM=decimal&L_CENTER_LAT='+window.lat+'&L_CENTER_LON='+window.lon+'&L_RADIUS='+window.radius+'&L_FEATURES=crosshair&L_COMPRESS='+window.compress;
//	$('#debug').text("debug: "+mapUrl);

	$('#map').attr("src", mapUrl).load(function() {
		$('#map').removeClass("fade");

		$('#statusinfo').text("Sijainti päivitetty kartalle klo "+window.timeText+". Tarkkuus "+window.acc+" metriä");
		$('#debug').append("<p>Sijainti päivitetty kartalle klo "+window.timeText+". Tarkkuus "+window.acc+" metriä "+getTimeText());
	});

}

function getTimeText()
{
	var currentTime = new Date();
	var h = currentTime.getHours();
	var m = currentTime.getMinutes();
	var s = currentTime.getSeconds();
	var ms = currentTime.getMilliseconds();
	timeText = h + "." + m + " " + s + "." + ms;
	return timeText
}

</script>


<script>
// -------------------------------------------
// Map buttons

$('#updatemap').click(function () {
	window.positionRequested = true;
	$('#debug').append("<p>Klikattu "+getTimeText());
	initGeolocation();
});



$('#map_peruskartta').click(function () {
	window.positionRequested = true;
	window.style = 'peruskartta';
	$('#debug').append("<p>Klikattu "+getTimeText());
	initGeolocation();
});
$('#map_ortokuva').click(function () {
	window.positionRequested = true;
	window.style = 'ortokuva';
	$('#debug').append("<p>Klikattu "+getTimeText());
	initGeolocation();
});
$('#map_vaaravari').click(function () {
	window.positionRequested = true;
	window.style = 'vaaravari';
	$('#debug').append("<p>Klikattu "+getTimeText());
	initGeolocation();
});


$('#map_vesistot').click(function () {
	window.positionRequested = true;
	window.style = 'vesistot';
	$('#debug').append("<p>Klikattu "+getTimeText());
	initGeolocation();
});
$('#map_korkeuskayrat').click(function () {
	window.positionRequested = true;
	window.style = 'korkeuskayrat';
	$('#debug').append("<p>Klikattu "+getTimeText());
	initGeolocation();
});
$('#map_pellot').click(function () {
	window.positionRequested = true;
	window.style = 'pellot';
	$('#debug').append("<p>Klikattu "+getTimeText());
	initGeolocation();
});
$('#map_suot').click(function () {
	window.positionRequested = true;
	window.style = 'suot';
	$('#debug').append("<p>Klikattu "+getTimeText());
	initGeolocation();
});
$('#map_kalliot').click(function () {
	window.positionRequested = true;
	window.style = 'kalliot';
	$('#debug').append("<p>Klikattu "+getTimeText());
	initGeolocation();
});
$('#map_avohakkuut').click(function () {
	window.positionRequested = true;
	window.style = 'avohakkuut';
	$('#debug').append("<p>Klikattu "+getTimeText());
	initGeolocation();
});
$('#map_liikenne').click(function () {
	window.positionRequested = true;
	window.style = 'liikenne';
	$('#debug').append("<p>Klikattu "+getTimeText());
	initGeolocation();
});
$('#map_rakennukset').click(function () {
	window.positionRequested = true;
	window.style = 'rakennukset';
	$('#debug').append("<p>Klikattu "+getTimeText());
	initGeolocation();
});
$('#map_kiinteistorajat').click(function () {
	window.positionRequested = true;
	window.style = 'kiinteistorajat';
	$('#debug').append("<p>Klikattu "+getTimeText());
	initGeolocation();
});
$('#map_tienimet').click(function () {
	window.positionRequested = true;
	window.style = 'tienimet';
	$('#debug').append("<p>Klikattu "+getTimeText());
	initGeolocation();
});

$('#map_radius250').click(function () {
	window.positionRequested = true;
	window.radius = 250;
	$('#debug').append("<p>Klikattu "+getTimeText());
	initGeolocation();
});
$('#map_radius500').click(function () {
	window.positionRequested = true;
	window.radius = 500;
	$('#debug').append("<p>Klikattu "+getTimeText());
	initGeolocation();
});
$('#map_radius1000').click(function () {
	window.positionRequested = true;
	window.radius = 1000;
	$('#debug').append("<p>Klikattu "+getTimeText());
	initGeolocation();
});
$('#map_radius2000').click(function () {
	window.positionRequested = true;
	window.radius = 2000;
	$('#debug').append("<p>Klikattu "+getTimeText());
	initGeolocation();
});

$('#map_compress').click(function () {
	window.positionRequested = true;
	window.compress = 1;
	$('#debug').append("<p>Klikattu "+getTimeText());
	initGeolocation();
});
$('#map_uncompress').click(function () {
	window.positionRequested = true;
	window.compress = 0;
	$('#debug').append("<p>Klikattu "+getTimeText());
	initGeolocation();
});

</script>

<?php

?>
</body>
</html>