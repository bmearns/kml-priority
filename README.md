kml-priority
============

KML LOD Render Priority (Nested Regions) with PHP/Postgres


This example uses code from https://developers.google.com/kml/articles/phpmysqlkml to produce KML from a postgres database.  Furthermore, two (identical) regions are provided with the <Lod> tag, to provide render priority.  In my case I had a field in the database that I used as my criteria for render priority, and included in condition1/2.  

This example could easily be modified, with the postgis extension, to produce glat and glng dynamically, with spatial functions.

For my region extent, I'm using the University of Delaware campus: 
Top: 39.697763884 
Left: -75.776270889
Right: -75.728799092
Bottom: 39.656921439

To use:
1. set table name and conditions in those parameters, inline in kml-priority.php
2. add database connection information to kml-priority-dbinfo.php, with proper permissions ... you could also provide this inline in kml-priority.php, but that would be less secure
