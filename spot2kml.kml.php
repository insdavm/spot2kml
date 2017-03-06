<?php

/**
 * spot2kml
 *
 * Converts JSON data from SPOT API to KML 
 * for import into Google Earth as a network
 * link.
 *
 * @author austin
 * @email insdavm@gmail.com
 *
 * SETUP NOTES:
 *     Server must be setup so that PHP will execute KML files as
 *     PHP (both in the Nginx conf and in php.ini).
 *     
 *     Then this file (example.com/spot2kml.kml) is added as a 
 *     network link in Google Earth.
 */

/*
 * Prep the browser
 */
header('Content-type: application/vnd.google-earth.kml+xml');

/*
 * Here we have two options.  Option one is to use 
 *     example.com/spot2kml.kml?latest
 * and the other is to use
 *     example.com/spot2kml.kml
 * 
 * One will give us just the last update for each SPOT, while
 * the other will give us ALL of the SPOT messages. We're taking
 * advantage of the SPOT API (notice the last part of the two URIs
 * below).
 * 
 * @link https://faq.findmespot.com/index.php?action=showEntry&data=69
 */
if (strtolower($_SERVER['QUERY_STRING']) == 'latest') {
    $json = shell_exec('curl https://api.findmespot.com/spot-main-web/consumer/rest-api/2.0/public/feed/0nSP2d9EFFqKgGrkQuM4q6RvUch30hnHb/latest.json');
} else {
    $json = shell_exec('curl https://api.findmespot.com/spot-main-web/consumer/rest-api/2.0/public/feed/0nSP2d9EFFqKgGrkQuM4q6RvUch30hnHb/message.json');
}

/*
 * Convert the data from the SPOT API from JSON to a PHP array
 */
$data = json_decode($json, TRUE);

/*
 * Dig into our multi-dimensional array to just the data
 * that we need for our KML file
 */
$messages = $data['response']['feedMessageResponse']['messages']['message'];

/*
 * Start KML file, putting each line as an array value
 * so we can join with a newline (\n) later.
 */
$kml = array('<?xml version="1.0" encoding="UTF-8"?>');
$kml[] = '<kml xmlns="http://earth.google.com/kml/2.1">';
$kml[] = '<Document>';
$kml[] = '<Style id="gps">';
$kml[] = '<IconStyle>';
$kml[] = '<color>ffff5100</color>';
$kml[] = '<scale>0.7</scale>'; //default 0.7
$kml[] = '<Icon>';
$kml[] = '<href>http://maps.google.com/mapfiles/kml/shapes/shaded_dot.png</href>';
$kml[] = '</Icon>';
$kml[] = '</IconStyle>';
$kml[] = '<LabelStyle>';
$kml[] = '<color>ffffa300</color>';
$kml[] = '<scale>0.7</scale>'; //default 0.7
$kml[] = '</LabelStyle>';
$kml[] = '<ListStyle>';
$kml[] = '</ListStyle>';
$kml[] = '</Style>';

/*
 * Populate each message as a KML waypoint
 */
foreach($messages as $key=>$msg) {

    $montanaTime = gmdate('Hi', ($msg['unixTime']-21600));
    $id = $msg['messengerName'] . ' [' . $montanaTime . ']';
    
    $description = <<<EOD
<font size="5" face="Tahoma">
<b>ID:</b> {$msg['messengerName']}<br>
<font color="red"><b>Montana Time:</b> {$montanaTime}<br></font>
<b>Messenger ID:</b> {$msg['messengerId']}<br>
<b>Type:</b> {$msg['messageType']}<br>
<b>Date:</b> {$msg['dateTime']}<br>
<b>Battery:</b> {$msg['batteryState']}<br>
</font>
EOD;

    $kml[] = '<Placemark id="placemark' . $msg['id'] . '">';
    $kml[] = '<name>' . $id . '</name>';
    $kml[] = '<description>' . htmlentities($description) . '</description>';
    $kml[] = '<styleUrl>#gps</styleUrl>';
    $kml[] = '<Point>';
    $kml[] = '<coordinates>' . $msg['longitude'] . ','  . $msg['latitude'] . '</coordinates>';
    $kml[] = '</Point>';
    $kml[] = '</Placemark>';
}

/*
 * End KML file
 */
$kml[] = ' </Document>';
$kml[] = '</kml>';

/*
 * Take the $kml[] array and turn it into one big
 * string, with literal newlines so that it's
 * not gross looking if we edit the KML directly
 */
$kmlOutput = join("\n", $kml);

/* 
 * Have the server return the KML!
 *
 * (This is KML and not text/html because
 * we explicitly sent the content-type header
 * in line 24)
 */
echo $kmlOutput;

?>
