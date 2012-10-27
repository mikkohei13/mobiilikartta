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
		<img src="startMap.png" id="map" alt="" />
	</p>
		
	<p id="statusinfo">&nbsp;</p>
	

	<p id="map_links">
	
		<span id="map_links_1" class="map_link_group">
		<a href="#" id="updatemap">Hae uusi sijainti</a>
		</span>

		<span id="map_links_2" class="map_link_group">
		<a href="#" id="map_peruskartta">Perus</a>
		<a href="#" id="map_ortokuva">Orto</a>
		<a href="#" id="map_taustakartta">Tausta</a>
		</span>
		
		<span id="map_links_6" class="map_link_group">
		<a href="#" id="map_radius250">0,5 km</a>
		<a href="#" id="map_radius500">1 km</a>
		<a href="#" id="map_radius1000">2 km</a>
		<a href="#" id="map_radius2000">4 km</a>
		</span>
		
		<span id="map_links_7" class="map_link_group">
		<a href="#" id="map_compress">Matala laatu</a>
		<a href="#" id="map_uncompress">Hyvä laatu</a>
		</span>

	</p>
	
	
	<div id="debug">&nbsp;</div>

	<p id="license">Sovellus: <a href="http://www.biomi.org/">Mikko Heikkinen, biomi.org</a> (<a href="https://github.com/mikkohei13/mobiilikartta">Github</a>), kartat: <a href="https://github.com/mikkohei13/mobiilikartta/blob/master/LICENSE.md">Maanmittauslaitos 2012</a></p>


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
	
	mapUrl = 'map.php?STYLE='+window.style+'&WIDTH=1000&HEIGHT=1000&LAT='+window.lat+'&LON='+window.lon+'&RADIUS='+window.radius+'&CROSSHAIR';
	if (1 == window.compress)
	{
		mapUrl = mapUrl + "&COMPRESS"
	}	
	
	$('#debug').append("<p>Ladataan karttaa "+mapUrl+" "+getTimeText());

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
$('#map_taustakartta').click(function () {
	window.positionRequested = true;
	window.style = 'taustakartta';
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

</body>
</html>