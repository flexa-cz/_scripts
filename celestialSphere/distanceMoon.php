<?php
require_once("celestialSphere.php");

// pocet mereni
$max = 800;

$img = ImageCreateTrueColor($max, 570);
ImageAntialias($img, true);
$sky = ImageColorAllocate($img, 60, 60, 60);
$moon = ImageColorAllocate($img, 255, 255, 255);
ImageFill($img, 0, 0, $sky);

$cs = new celestialSphere();
$t = gmMkTime(0,0,0,1,1,2010);
$cs->setTime($t-1800);
$d1 = $cs->get("Moon", "distance");
$cs->setTime($t);
$dm = $cs->get("Moon", "distance");
if ($dm > $d1)
     $f = true; //vzdalenost stoupa
else
     $f = false;
     
for ($x = 1; $x < $max; $x++)
{
     $t += 21600;
     $cs->setTime($t);
     $d = $cs->get("Moon", "distance");
     if ($f and $d <= $dm)
     {
          ImageString($img, 3, $x-1, getY($dm)-25, date("d.m.Y", $t-21600), $moon);
          ImageString($img, 3, $x-1, getY($dm)-15, round($dm,-2), $moon);
          $f = false;
     }
     elseif (!$f and $d >= $dm)
     {
          ImageString($img, 3, $x-1, getY($dm)+2, date("d.m.Y", $t-21600), $moon);
          ImageString($img, 3, $x-1, getY($dm)+12, round($dm,-2), $moon);
          $f = true;
     }
     ImageLine($img, $x-1, getY($dm), $x, getY($d), $moon);
     $dm = $d;
}

Header("Content-type: image/png");
ImagePNG($img);
ImageDestroy($img);

/////////////////////////////////
function getY($distance)
{
     return((410000-$distance)/100);
}
?>

