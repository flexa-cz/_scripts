<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Celestial Sphere - PHP Class</title>
</head>
<body>
<h2>Slunce je nyní ve znamení&nbsp;<?php
require_once("celestialSphere.php");

$cs = new celestialSphere();
$cs->setTime();

switch ((int)($cs->get("Sun", "longitude")/30)) {
case 0: echo "Berana"; break;
case 1: echo "Býka"; break;
case 2: echo "Blíženců"; break;
case 3: echo "Raka"; break;
case 4: echo "Lva"; break;
case 5: echo "Panny"; break;
case 6: echo "Vah"; break;
case 7: echo "Štíra"; break;
case 8: echo "Střelce"; break;
case 9: echo "Kozoroha"; break;
case 10: echo "Vodnáře"; break;
case 11: echo "Ryb"; break;
default : echo "zcela mimo!"; break;
}
?>
</h2>
</body>
</html>
