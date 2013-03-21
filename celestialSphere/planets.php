<?php
require_once("celestialSphere.php");

define("RAD", 0.01745329252);

$planets = array("Venus"=>"Ve", "Earth"=>"Ze", "Mars"=>"Ma", "Jupiter"=>"Ju", "Saturn"=>"Sa");
$rau = 10;    // polomer v AU
$au2px = 40;  // 1AU = ?px

$img = ImageCreate(2*$rau*$au2px, 2*$rau*$au2px);
$sky = ImageColorAllocate($img,60,60,60);
$white = ImageColorAllocate($img,255,255,255);
$sun = ImageColorAllocate($img,255,255,0);
$earth = ImageColorAllocate($img,96,96,255);
ImageFill($img, 0, 0, $sky);
ImageFilledArc($img, $rau*$au2px, $rau*$au2px, 10, 10, 0, 360, $sun, IMG_ARC_PIE);
$font = "arial.ttf";
     
$cs = new celestialSphere();
$t = Time();
$cs->setTime($t);
foreach ($planets as $name=>$code)
{
     if ($name == "Earth")
          $planet = $earth;
     else
          $planet = $white;
     $r = $cs->get($name, "heliocentric_radius")*$au2px;
     ImageArc($img, $rau*$au2px, $rau*$au2px, 2*$r, 2*$r, 0, 360, $planet);
     $u = $cs->get($name, "heliocentric_longitude");
     $x = $rau*$au2px + cos($u*RAD)*$r;
     $y = $rau*$au2px - sin($u*RAD)*$r;
     ImageFilledArc($img, $x, $y, 6, 6, 0, 360, $planet, IMG_ARC_PIE);
     ImageFilledRectangle($img, $x-8, $y-16, $x+12, $y-5, $sky);
     ImageString($img, 5, $x-6, $y-18, $code, $planet);
}

Header("Content-type: image/png");
ImagePNG($img);
ImageDestroy($img);
?>
