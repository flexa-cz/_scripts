<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Celestial Sphere - PHP Class</title>
<meta name="description" content="PHP třída pro nalezení pozice planet a Měsíce">
<style type='text/css'>
table {
     background-color: white;
     border-collapse: collapse;
}
td,th {
     border: solid 1px black;
     text-align: center; 
     padding: 0.5ex 1ex 0.5ex 1ex;
}
a.link {
     text-decoration: none;
     color: black;
} 
</style>
</head>
<body>
<div align='center'>
<?php
require_once("celestialSphere.php");
require_once("csFunctions.php");

$cs = new celestialSphere();

echo "<div align='center'>\n";
echo deg2dm($cs->latitude)."severní zeměpisné šířky a ".deg2dm($cs->longitude)." východní zeměpisné délky, časové pásmo: ".$cs->timezone."<br><br>\n";

echo "<table>\n";
echo "<tr><th>Čas</th><th>Světový čas</th><th>Julianské datum</th><th>Místní hvězdný čas</th><th>&Delta;T</th></tr>\n";
echo "<tr><td>".date("H:i:s d.m.Y", $cs->time)."</td><td>".gmdate("H:i:s d.m.Y", $cs->time)."</td><td>".$cs->julianDate."</td><td>".deg2hms($cs->siderealTime)."</td><td>".round($cs->deltaT,1)."s</td>";
echo "</table>\n";

echo "<br>\n";

echo "<table>\n";
echo "<tr><th rowspan=2></th><th rowspan=2>Vzdálenost<br>od Země</th><th colspan=2>Obzorníkové souřadnice</th><th colspan=2>Rovníkové souřadnice</th><th colspan=2>Ekliptikální souřadnice</th></tr>\n";
echo "<tr><th>Azimut</th><th>Výška</th><th>Rektascenze</th><th>Deklinace</th><th>Délka</th><th>Šířka</th></tr>\n";
echo "<tr><td><strong>Slunce</strong></td><td>".fround($cs->get("Earth", "heliocentric_radius"),3)."AU</td><td>".fround($cs->get("Sun", "azimuth"),1)."&deg;</td><td>".fround($cs->get("Sun", "height"),1)."&deg;</td>";
echo "<td>".deg2hms($cs->get("Sun", "right_ascension"))."</td><td>".deg2dms($cs->get("Sun", "declination"))."</td>";
echo "<td>".deg2dms($cs->get("Sun", "longitude"))."</td><td>".deg2dms($cs->get("Sun", "latitude"))."</td></tr>";
echo "</table>\n";

echo "<br>\n";

echo "<table>\n";
echo "<tr><th rowspan=2></th><th rowspan=2>Fáze</th><th rowspan=2>Vzdálenost<br>od Země</th><th colspan=2>Obzorníkové souřadnice</th><th colspan=2>Topocentrické rovníkové souřadnice</th><th colspan=2>Rovníkové souřadnice</th><th colspan=2>Ekliptikální souřadnice</th></tr>\n";
echo "<tr><th>Azimut</th><th>Výška</th><th>Rektascenze</th><th>Deklinace</th><th>Rektascenze</th><th>Deklinace</th><th>Délka</th><th>Šířka</th></tr>\n";
echo "<tr><td><strong>Měsíc</strong></td><td>".fround($cs->get("Moon", "phase"),3)."</td><td>".fround($cs->get("Moon", "distance"),-2)."km</td>";
echo "<td>".fround($cs->get("Moon", "azimuth"),1)."&deg;</td><td>".fround($cs->get("Moon", "height"),1)."&deg;</td>";
echo "<td>".deg2hm($cs->get("Moon", "topocentric_right_ascension"))."</td><td>".deg2dm($cs->get("Moon", "topocentric_declination"))."</td>";
echo "<td>".deg2hm($cs->get("Moon", "right_ascension"))."</td><td>".deg2dm($cs->get("Moon", "declination"))."</td>";
echo "<td>".deg2dm($cs->get("Moon", "longitude"))."</td><td>".deg2dm($cs->get("Moon", "latitude"))."</td></tr>";
echo "</table>\n";

echo "<br>\n";

echo "<table>\n";
echo "<tr><th rowspan=2>Planeta</th><th rowspan=2>Vzdálenost<br>od Slunce</th><th rowspan=2>Vzdálenost<br>od Země</th><th colspan=2>Obzorníkové souřadnice</th><th colspan=2>Rovníkové souřadnice</th><th colspan=2>Ekliptikální souřadnice</th><th colspan=2>Heliocentrické souřadnice</th></tr>\n";
echo "<tr><th>Azimut</th><th>Výška</th><th>Rektascenze</th><th>Deklinace</th><th>Délka</th><th>Šířka</th><th>Délka</th><th>Šířka</th></tr>\n";

$planets = array("Mercury" => "Merkur", "Venus" => "Venuše", "Mars" => "Mars", "Jupiter" => "Jupiter", "Saturn" => "Saturn", "Uranus" => "Uran", "Neptune" => "Neptun");

foreach ($planets as $name => $name_cs)
{
     echo "<tr><td>{$name_cs}</td><td>".fround($cs->get($name, "heliocentric_radius"),3)."AU</td><td>".fround($cs->get($name, "distance"),3)."AU</td>";
     echo "<td>".fround($cs->get($name, "azimuth"),1)."&deg;</td><td>".fround($cs->get($name, "height"),1)."&deg;</td>";
     echo "<td>".deg2hm($cs->get($name, "right_ascension"))."</td><td>".deg2dm($cs->get($name, "declination"))."</td>";
     echo "<td>".deg2dm($cs->get($name, "longitude"))."</td><td>".deg2dm($cs->get($name, "latitude"))."</td>";
     echo "<td>".deg2dm($cs->get($name, "heliocentric_longitude"))."</td><td>".deg2dm($cs->get($name, "heliocentric_latitude"))."</td></tr>\n";
}
echo "</table>\n";

?>
</div>
</body>
</html>
