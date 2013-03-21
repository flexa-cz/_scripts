<?php
define("FORMAT", "%d.%m.%Y %H:%M:%S");

// casova znacka do stringu
function time2string($t)
{
     return(strftime(FORMAT, $t));
}

// z string casova znacka (local)
function string2time($s)
{
     /* nektere platformy nemaji funkci strptime()
     $x = strptime($s, FORMAT);
     return(mktime($x["tm_hour"], $x["tm_min"], $x["tm_sec"], $x["tm_mon"]+1, $x["tm_mday"], $x["tm_year"]+1900));
     */
     if (preg_match_all("/(\d{1,2})\.(\d{1,2})\.(\d{4})\s+(\d{1,2}):(\d{1,2}):(\d{1,2})/", $s, $m))
          return(mktime($m[4][0], $m[5][0], $m[6][0], $m[2][0], $m[1][0], $m[3][0]));
     else
          return(false);
}

function deg2hm($x)
{
     $x /= 15;
     $x = round($x*60)/60;    //zaokrouhleni na minuty
     if ($x == 24)
          $x = 0;
     $h = (int)($x);
     $m = round(60*($x-$h));
     return(sprintf("%dh%02dm", $h, $m));
}
function deg2hms($x)
{
     $x /= 15;
     $x = round($x*3600)/3600;    //zaokrouhleni na vteriny
     if ($x == 24)
          $x = 0;
     $h = (int)($x);
     $m = (int)(60*($x-$h));
     $s = round(60*(60*($x-$h)-$m));
     return(sprintf("%dh%02dm%02ds", $h, $m, $s));
}
function deg2dm($x)
{
     $x = round($x*60)/60;    //zaokrouhleni na uhlove minuty
     if ($x == 360)
          $x = 0;
     $d = (int)abs($x);
     $m = round(60*(abs($x)-$d));
     $sgn = ($x < 0 ? "-" : "");
     return(sprintf("%s%d&deg;%02d'", $sgn, $d, $m));
}
function deg2dms($x)
{
     $x = round($x*3600)/3600;    //zaokrouhleni na uhlove vteriny
     if ($x == 360)
          $x = 0;
     $d = (int)abs($x);
     $m = (int)(60*(abs($x)-$d));
     $s = round(60*(60*(abs($x)-$d)-$m));
     $sgn = ($x < 0 ? "-" : "");
     return(sprintf("%s%d&deg;%02d'%02d''", $sgn, $d, $m, $s));
}
function dms2deg($s)
{
     $sign = 1;
     $degree = 0;
     $minute = 0;
     $second = 0;
     if (preg_match_all("/([-+]?)(\d+)d/", $s, $m))
     {
          $sign = ($m[1][0] == "-" ? -1 : 1);
          $degree = $m[2][0];
     }
     if (preg_match_all("/(\d+)m/", $s, $m))
          $minute = $m[1][0];
     if (preg_match_all("/(\d+\.?\d*)s/", $s, $m))
          $second = $m[1][0];
     return($sign*($degree+$minute/60+$second/3600));
}

function hms2deg($s)
{
     $hour = 0;
     $minute = 0;
     $second = 0;
     if (preg_match_all("/(\d+)h/", $s, $m))
          $hour = $m[1][0];
     if (preg_match_all("/(\d+)m/", $s, $m))
          $minute = $m[1][0];
     if (preg_match_all("/(\d+\.?\d*)s/", $s, $m))
          $second = $m[1][0];
     return($hour*15+$minute/4+$second/240);
}    

function fround($x, $n)
{
     if ($n < 0)
          return(round($x,$n));
     else
          return(sprintf("%.{$n}f", round($x,$n)));
}
?>
