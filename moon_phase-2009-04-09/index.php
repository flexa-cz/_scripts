<?
require_once("moonphase.php");

$day="10";
$month="04";
$year="2009";

$myMoon=new MoonPhase($day,$month,$year);
echo "picture: ".$myMoon->showPicture()."<br>";
echo "age: ".$myMoon->showAge()."<br>";
echo "phase: ".$myMoon->showPhase()."<br>";
echo "distance: ".$myMoon->showDistance()."<br>";
echo "latitude: ".$myMoon->showLatitude()."<br>";
echo "longitude: ".$myMoon->showLongitude()."<br>";

?>