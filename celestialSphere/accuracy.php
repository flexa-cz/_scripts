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
<table>
<tr><th rowspan=3>Sluneční zatmění<br>(GMT)</th><th colspan=8>Rovníkové souřadnice</th></tr>
<tr><th colspan=2>Slunce: rektascenze</th><th colspan=2>Slunce: deklinace</th><th colspan=2>Měsíc: rektascenze</th><th colspan=2>Měsíc: deklinace</th></tr>
<tr><th>NASA</th><th>celestialSphere</th><th>NASA</th><th>celestialSphere</th><th>NASA</th><th>celestialSphere</th><th>NASA</th><th>celestialSphere</th><tr>

<?php
require_once("celestialSphere.php");
require_once("csFunctions.php");

$cs = new celestialSphere(0, 0, "GMT");

$eclipse = array(
     array("timestamp"=>"18.5.1901 5:33:50", "raSun"=>"3h37m03.3s", "deSun"=>"19&deg;23'52.0''", "raMoon"=>"3h37m16.0s", "deMoon"=>"19&deg;02'00.9''", "url"=>"http://eclipse.gsfc.nasa.gov/SEplot/SEplot1901/SE1901May18T.GIF"),
     array("timestamp"=>"21.8.1914 12:34:09", "raSun"=>"9h59m08.5s", "deSun"=>"12&deg;18'56.9''", "raMoon"=>"10h00m29.2s", "deMoon"=>"12&deg;59'43.7''", "url"=>"http://eclipse.gsfc.nasa.gov/SEplot/SEplot1901/SE1914Aug21T.GIF"),
     array("timestamp"=>"11.8.1999 11:03:08", "raSun"=>"9h23m08.3s", "deSun"=>"15&deg;19'39.9''", "raMoon"=>"9h23m34.5s", "deMoon"=>"15&deg;48'38.9''", "url"=>"http://eclipse.gsfc.nasa.gov/SEplot/SEplot1951/SE1999Aug11T.GIF"),
     array("timestamp"=>"20.3.2015 9:45:38", "raSun"=>"23h58m01.5s", "deSun"=>"-0&deg;12'50.6''", "raMoon"=>"23h56m50.5s", "deMoon"=>"0&deg;42'08.6''", "url"=>"http://eclipse.gsfc.nasa.gov/SEplot/SEplot2001/SE2015Mar20T.GIF"),
     array("timestamp"=>"25.10.2022 11:00:00", "raSun"=>"13h59m20.4s", "deSun"=>"-12&deg;10'16.6''", "raMoon"=>"14h01m10.8s", "deMoon"=>"-11&deg;14'16.1''", "url"=>"http://eclipse.gsfc.nasa.gov/SEplot/SEplot2001/SE2022Oct25P.GIF"),
     array("timestamp"=>"23.7.2093 12:29:11", "raSun"=>"8h14m45.3s", "deSun"=>"19&deg;49'29.8''", "raMoon"=>"8h15m01.3s", "deMoon"=>"20&deg;20'04.2''", "url"=>"http://eclipse.gsfc.nasa.gov/SEplot/SEplot2051/SE2093Jul23A.GIF")
//     array("timestamp"=>"", "raSun"=>"", "deSun"=>"", "raMoon"=>"", "deMoon"=>"", "url"=>""),
     );     

foreach ($eclipse as $e)
     if ($t = string2time($e['timestamp']))
     {
          $cs->setTime($t);
          echo "<tr><td><a class='link' href='{$e['url']}'>{$e['timestamp']}</a></td>";
          echo "<td>{$e['raSun']}</td><td>".deg2hms($cs->get("Sun", "right_ascension"))."</td>";
          echo "<td>{$e['deSun']}</td><td>".deg2dms($cs->get("Sun", "declination"))."</td>";
          echo "<td>{$e['raMoon']}</td><td>".deg2hms($cs->get("Moon", "right_ascension"))."</td>";
          echo "<td>{$e['deMoon']}</td><td>".deg2dms($cs->get("Moon", "declination"))."</td></tr>";
     }
?>
</table>
</div>
</body>
</html>
