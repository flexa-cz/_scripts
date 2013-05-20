<?php
require_once("celestialSphere.php");

$cs = new celestialSphere();

$img = ImageCreate(620, 620);
$black = ImageColorAllocate($img, 0, 0, 0);
ImageFill($img, 0, 0, $black);

drawMoonPhase($img, 310, 310, 300, $cs->get("Moon", "phase"));

Header("Content-type: image/png");
ImagePNG($img);
ImageDestroy($img);

/////////////////////////////////////////////////////////////////////////////
// $sx,$sy: souradnice stredu
// $r: polomer Mesice
// $k: faze (0,1) $k<0: couva
function drawMoonPhase($img, $sx, $sy, $r, $k)
{
     if ($k < 0)
     {
          $dorusta = false;
          $k = -$k;
     }
     else
          $dorusta = true;
     $white = ImageColorAllocate($img,255,255,255);
     ImageArc($img, $sx, $sy, 2*$r, 2*$r, 0, 360, $white);
     if ($k*$r < 1 and $dorusta)
          return;   // presne Novoluni
     elseif ($k <= 0.5 and $dorusta)
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
     ImageArc($img, $sx, $sy, 4*$r*abs($k-0.5), 2*$r, $a1, $a2, $white);
     ImageFill($img, $fx, $sy, $white);     
}
?>

