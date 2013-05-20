<?php
require_once("celestialSphere.php");

// levy horni okraj (stupne)
$x0 = 353;
$y0 = 48;
// pravy dolni okraj (stupne)
$x1 = 367;
$y1 = 0;
// zvetseni
$zx = 8;
$zy = 8;

$img = ImageCreateTrueColor($zx*14, $zy*50);
ImageAntialias($img, true);
$sky = ImageColorAllocate($img,128,128,255);
$sun = ImageColorAllocate($img,255,255,0);
ImageFill($img, 0, 0, $sky);

// Severni polarni kruh
$cs = new celestialSphere(66.5, 0.0, "GMT");
$t = mktime(12, 0, 0, 8, 1, 2009);
for ($i = 0; $i < 52; $i++)
{
     $cs->setTime($t);
     if ($cs->get("Sun", "azimuth") < 180)
          $x = $zx*($cs->get("Sun", "azimuth")+360-$x0);
     else     
          $x = $zx*($cs->get("Sun", "azimuth")-$x0);
     
     $y = $zy*($y0-$cs->get("Sun", "height"));
     ImageFilledArc($img, $x, $y, 6, 6, 0, 360, $sun, IMG_ARC_PIE);
     $t = strtotime("+1 week", $t);
}

Header("Content-type: image/png");
ImagePNG($img);
ImageDestroy($img);
?>
