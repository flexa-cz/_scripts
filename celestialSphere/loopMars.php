<?php
// http://celestialdelights.info/mars2003_Loop.html
require_once("celestialSphere.php");

// levy horni okraj (stupne)
$x0 = 330;
$y0 = -9;
// pravy dolni okraj (stupne)
$x1 = 345;
$y1 = -17;
// zvetseni
$zx = 40;
$zy = 40;
// zacatek
$start = gmMkTime(0,0,0,6,12,2003);
// konec po X dnech
$maxDays = 150;

$img = ImageCreateTrueColor($zx*abs($x0 - $x1), $zy*abs($y0 - $y1));
ImageAntialias($img, true);
$sky = ImageColorAllocate($img,60,60,60);
$mars = ImageColorAllocate($img,255,96,96);
ImageFill($img, 0, 0, $sky);

$cs = new celestialSphere();
$minDistance = 1e9;
$t = $start;
for ($i = 0; $i < $maxDays; $i++)
{
     $cs->setTime($t);
     if ($cs->get("Mars", "distance") < $minDistance)
     {
          $minDistance = $cs->get("Mars", "distance");
          $timestamp = $t;
     }
     $x = $zx*($cs->get("Mars", "right_ascension")-$x0);
     $y = $zy*($cs->get("Mars", "declination")-$y1);
     ImageFilledArc($img, $x, $y, 4, 4, 0, 360, $mars, IMG_ARC_PIE);
     if ($i == 0 or $i == $maxDays-1)
          ImageString($img, 4, $x, $y-16, date("d.m.Y", $t), $mars);
     $t = strtotime("+1 day", $t);
}

$cs->setTime($timestamp);
$x = $zx*($cs->get("Mars", "right_ascension")-$x0);
$y = $zy*($cs->get("Mars", "declination")-$y1);
ImageString($img, 4, $x, $y-16, date("d.m.Y", $timestamp)." : ".round($cs->get("Mars", "distance"),3)." AU", $mars);

Header("Content-type: image/png");
ImagePNG($img);
ImageDestroy($img);
?>
