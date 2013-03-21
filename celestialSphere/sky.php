<?php
require_once("celestialSphere.php");
require_once("hip.php"); //+$stars,+$lines

define("RAD", 0.01745329252);
define("R", 400);

$planets = array("Venus"=>"Ve", "Mars"=>"Ma", "Jupiter"=>"Ju", "Saturn"=>"Sa");
     
$img = ImageCreateTrueColor(2*R+2, 2*R+2);
ImageAntialias($img, true);
$transparent = ImageColorAllocate($img,255,254,255);
ImageFill($img, 0, 0, $transparent);
ImageColorTransparent($img, $transparent);

$cs = new celestialSphere();
foreach ($stars as $a)
     $cs->setObject($a[0], $a[1], $a[2]);

// barva oblohy dle vysky Slunce
ImageFilledArc($img, R+1, R+1, 2*R, 2*R, 0, 360, getColorSky($cs->get("Sun", "height")), IMG_ARC_PIE);

// spojnice hvezd     
foreach ($lines as $s)
     if ($cs->get($s[0], "height") >= 0 and $cs->get($s[1], "height") >= 0)
          drawLine($cs->get($s[0], "azimuth"), $cs->get($s[0], "height"), $cs->get($s[1], "azimuth"), $cs->get($s[1], "height"));

// hvezdy     
foreach ($stars as $s)
     if ($cs->get($s[0], "height") > 0)
          drawStar($cs->get($s[0], "azimuth"), $cs->get($s[0], "height"), $s[3]);

// planety
foreach ($planets as $name=>$code)
     if ($cs->get($name, "height") > 0)
          drawPlanet($code, $cs->get($name, "azimuth"), $cs->get($name, "height"));

// Slunce
if ($cs->get("Sun", "height") > 0)
     drawSun($cs->get("Sun", "azimuth"), $cs->get("Sun", "height"));

// Mesic
if ($cs->get("Moon", "height") > 0)
     drawMoonPhaseR($cs->get("Moon", "azimuth"), $cs->get("Moon", "height"), $cs->get("Moon", "phase"));

// souradnice
drawCoordinates();

Header("Content-type: image/png");
ImagePNG($img);
ImageDestroy($img);

/////////////////////////////////////////////////////////
function drawLine($A0, $h0, $A1, $h1)
{
     global $img;

     ImageLine($img, getX($A0, $h0), getY($A0, $h0), getX($A1, $h1), getY($A1, $h1), ImageColorAllocate($img,255,128,0));
}

function drawStar($A, $h, $mag)
{
     global $img;
     
     if ($mag <= 1)
          ImageFilledArc($img, getX($A, $h), getY($A, $h), 7, 7, 0, 360, ImageColorAllocate($img,255,255,255), IMG_ARC_PIE);
     elseif ($mag <= 2.5)
          ImageFilledArc($img, getX($A, $h), getY($A, $h), 5, 5, 0, 360, ImageColorAllocate($img,255,255,255), IMG_ARC_PIE);     
     elseif ($mag <= 3.5)
          ImageFilledArc($img, getX($A, $h), getY($A, $h), 3, 3, 0, 360, ImageColorAllocate($img,255,255,255), IMG_ARC_PIE);     
     else
          ImageArc($img, getX($A, $h), getY($A, $h), 1, 1, 0, 360, ImageColorAllocate($img,255,255,255));     
}

function drawPlanet($code, $A, $h)
{
     global $img;

     ImageFilledArc($img, getX($A, $h), getY($A, $h), 8, 8, 0, 360, ImageColorAllocate($img,255,255,255), IMG_ARC_PIE);
     ImageString($img, 5, getX($A, $h)-5, getY($A, $h)+3, $code, ImageColorAllocate($img,255,255,255));
}

function drawSun($A, $h)
{
     global $img;

     $r = 7;
     ImageFilledArc($img, getX($A, $h), getY($A, $h), 2*$r, 2*$r, 0, 360, ImageColorAllocate($img,255,255,0), IMG_ARC_PIE);
     ImageString($img, 5, getX($A, $h)-3, getY($A, $h)-$r-15, "S", ImageColorAllocate($img,255,255,0));
}

function getX($A, $h)
{
     $x = sin($A*RAD)*(90-$h)*R/90 + R+1;
     return($x);
}

function getY($A, $h)
{
     $y = cos($A*RAD)*(90-$h)*R/90 + R+1;
     return($y);
}

function drawMoonPhaseR($A, $h, $k)
{
     global $img;

     $r = 8;
     $sx = $sy = 2*$r; // obrazek je kvuli rotaci o 100% vetsi
     
     $img2 = ImageCreate(4*$r,4*$r);    // 2*2
     $transparent = ImageColorAllocate($img2,255,254,255);
     ImageFill($img2, 0, 0, $transparent);
     ImageColorTransparent($img2, $transparent);     
  
     if ($k < 0)
     {
          $dorusta = false;
          $k = -$k;
     }
     else
          $dorusta = true;
     $white = ImageColorAllocate($img2,255,255,255);
     ImageArc($img2, $sx, $sy, 2*$r, 2*$r, 0, 360, $white);
     ImageFilledArc($img2, $sx, $sy, 2*$r-1, 2*$r-1, 0, 360, ImageColorAllocate($img2,128,128,128), IMG_ARC_PIE);
     
     if ($k*$r >= 1 or !$dorusta)
     {
          if ($k <= 0.5 and $dorusta)
          {
               $a1 = 270; $a2 = 90;
               $fx = $sx+$r-1;
          }
          elseif ($k > 0.5 and $dorusta)
          {
               $a1 = 90; $a2 = 270;
               $fx = $sx+$r-1;
          }
          elseif ($k <= 0.5 and !$dorusta)
          {
               $a1 = 90; $a2 = 270;
               $fx = $sx-$r+1;     
          }
          elseif ($k > 0.5 and !$dorusta)
          {
                $a1 = 270; $a2 = 90;
                $fx = $sx-$r+1;
          }
          ImageArc($img2, $sx, $sy, 4*$r*abs($k-0.5), 2*$r, $a1, $a2, $white);
          ImageFill($img2, $fx, $sy, $white);
     }
     //rotate
     $img2 = ImageRotate($img2, $A, $transparent, 0);
     //copy
     $dstX = getX($A, $h) - $r;
     $dstY = getY($A, $h) - $r;
     $Ai = fmod($A,90);
     $xy = $r*(2.8*sin((45+$Ai)*RAD)-1);  //2.8 = 2*sqrt(2)
     ImageCopy($img, $img2, $dstX, $dstY, $xy, $xy, 2*$r+2, 2*$r+2);
     ImageString($img, 5, $dstX+$r-3, $dstY-15, "M", ImageColorAllocate($img,255,255,255));
}

function getColorSky($h)
{
     global $img;

     if ($h < -18)
          return(ImageColorAllocate($img,60,70,90));
     elseif ($h < -12)
          return(ImageColorAllocate($img,80,90,120));
     elseif ($h < -6)
          return(ImageColorAllocate($img,100,110,150));
     elseif ($h < -0.83)   // refrakce
          return(ImageColorAllocate($img,120,130,180));
     else
          return(ImageColorAllocate($img,140,150,210));
}

function drawCoordinates()
{
     global $img;

     $black = ImageColorAllocate($img,0,0,0);
     // Zenit
     ImageLine($img, R+1-10, R+1, R+1+10, R+1, $black);
     ImageLine($img, R+1, R+1-10, R+1, R+1+10, $black);
     // J
     ImageLine($img, R+1, 2*R-10, R+1, 2*R, $black);
     ImageString($img, 5, R-3, 2*R-25, "J", $black);
     // V
     ImageLine($img, 1, R+1, 11, R+1, $black);
     ImageString($img, 5, 15, R-7, "V", $black);
     // S
     ImageLine($img, R+1, 1, R+1, 11, $black);
     ImageString($img, 5, R-3, 11, "S", $black);
     // Z
     ImageLine($img, 2*R-10, R+1, 2*R, R+1, $black);
     ImageString($img, 5, 2*R-20, R-7, "Z", $black);
}
?>
