<?php

/* 
This example uses code from https://developers.google.com/kml/articles/phpmysqlkml to produce KML from a postgres database.  Furthermore, two (identical) regions are provided with the <Lod> tag, to provide render priority.  In my case I had a field in the database that I used as my criteria for render priority, and included in condition1/2.  

This example could easily be modified, with the postgis extension, to produce glat and glng dynamically, with spatial functions.

For my region extent, I'm using the University of Delaware campus: 
Top: 39.697763884 
Left: -75.776270889
Right: -75.728799092
Bottom: 39.656921439

To use:
#1. set table name and conditions in those parameters, inline
#2. add database connection information to kml-priority-dbinfo.php, with proper permissions ... you could also provide this inline, but that would be less secure
*/

$TABLE = 'YOUR TABLE HERE';
$CONDITION1 = 'YOUR FIRST CONDITION HERE';
$CONDITION2 = 'YOUR SECOND CONDITION HERE';

include('kml-priority-dbinfo.php');


$dbconn = pg_connect("host=$DBHOST dbname=$DBNAME user=$DBUSER password=$DBPASS")
  or die('Could not connect: ' . pg_last_error());

$query = "SELECT * FROM TABLE WHERE $CONDITION1;";

$result = pg_query($query) or die('Query failed: ' . pg_last_error());  

$query = "SELECT * FROM TABLE WHERE $CONDITION2;";

$result2 = pg_query($query) or die('Query failed: ' . pg_last_error());	


// Creates an array of strings to hold the lines of the KML file.
$kml = array('<?xml version="1.0" encoding="UTF-8"?>');
$kml[] = '<kml xmlns="http://earth.google.com/kml/2.1">';
$kml[] = ' <Document>';
$kml[] = ' <Style id="style1">';
$kml[] = ' <IconStyle id="styleIcon">';
$kml[] = ' <Icon>';
$kml[] = ' <href>http://maps.google.com/mapfiles/kml/pal2/icon10.png</href>';
$kml[] = ' </Icon>';
$kml[] = ' </IconStyle>';
$kml[] = ' </Style>';

$kml[] = ' <Region>';
$kml[] = '     <LatLonAltBox>';
$kml[] = '       <north>39.697763884</north>';
$kml[] = '       <south>39.656921439</south>';
$kml[] = '       <east>-75.728799092</east>';
$kml[] = '       <west>-75.776270889</west>';
$kml[] = '     </LatLonAltBox>';
$kml[] = '     <Lod>';
$kml[] = '       <minLodPixels>128</minLodPixels>';
$kml[] = '       <maxLodPixels>256</maxLodPixels>';
$kml[] = '     </Lod>';
$kml[] = '   </Region>';
  
// Iterates through the rows, printing a node for each row.
while ($row = @pg_fetch_assoc($result)) 
{
  $kml[] = ' <Placemark id="placemark' . $row['udcode'] . '">';
  $kml[] = ' <name>' . htmlentities($row['name']) . '</name>';
  $kml[] = ' <description>' . htmlentities($row['name']) . '</description>';
  $kml[] = ' <styleUrl>#style1</styleUrl>';
  // $kml[] = ' <styleUrl>#' . ($row['type']) .'Style</styleUrl>';
  $kml[] = ' <Point>';
  $kml[] = ' <coordinates>' . $row['glng'] . ','  . $row['glat'] . '</coordinates>';
  $kml[] = ' </Point>';
  $kml[] = ' </Placemark>';
 
} 
 
$kml[] = ' <Folder>'; 
$kml[] = ' <Region>';
$kml[] = '     <LatLonAltBox>';
$kml[] = '       <north>39.697763884</north>';
$kml[] = '       <south>39.656921439</south>';
$kml[] = '       <east>-75.728799092</east>';
$kml[] = '       <west>-75.776270889</west>';
$kml[] = '     </LatLonAltBox>';
$kml[] = '     <Lod>';
$kml[] = '       <minLodPixels>256</minLodPixels>';
$kml[] = '       <maxLodPixels>8192</maxLodPixels>';
$kml[] = '     </Lod>';
$kml[] = '   </Region>';
  
// Iterates through the rows, printing a node for each row.
while ($row = @pg_fetch_assoc($result2)) 
{
  $kml[] = ' <Placemark id="placemark' . $row['udcode'] . '">';
  $kml[] = ' <name>' . htmlentities($row['name']) . '</name>';
  $kml[] = ' <description>' . htmlentities($row['name']) . '</description>';
  $kml[] = ' <styleUrl>#style1</styleUrl>';
  // $kml[] = ' <styleUrl>#' . ($row['type']) .'Style</styleUrl>';
  $kml[] = ' <Point>';
  $kml[] = ' <coordinates>' . $row['glng'] . ','  . $row['glat'] . '</coordinates>';
  $kml[] = ' </Point>';
  $kml[] = ' </Placemark>';
 
} 

$kml[] = '  </Folder>';

// End XML file
$kml[] = ' </Document>';
$kml[] = '</kml>';
$kmlOutput = join("\n", $kml);
header('Content-type: application/vnd.google-earth.kml+xml');
echo $kmlOutput;
?>
