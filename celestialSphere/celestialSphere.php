<?php
////////////////////////////////////////
// 6.2.2010 Jiri Jozif jirkaj@beda.cz
////////////////////////////////////////

class celestialSphere
{
     const RAD = 0.01745329252;
     const DEG = 57.2957795131;
     private $object = array();
     private $objectInit = array();

     public function __construct($latitude = 50.0, $longitude = 15.0, $timezone = "Europe/Prague") 
     {
          date_default_timezone_set($timezone);
          $this->timezone = date_default_timezone_get();
          $this->latitude = $latitude;         
          $this->longitude = $longitude;
          $this->setTime();
     }     
     public function setObject($name, $right_ascension, $declination, $precession_add = true)
     {
          $this->objectInit[$name] = array("right_ascension"=>$right_ascension, "declination"=>$declination, "precession_add"=>$precession_add);
     }
     public function setTime($time = false, $DT = true)
     {
          unset($this->object);
          if ($time === false)
               $this->time = time();
          else
               $this->time = $time;
                  
          $this->julianDate = $this->getJulianDate();
          // Dynamical Time
          $this->julianDateDT = $this->julianDate;
          if ($DT)
               $this->deltaT = $this->getDeltaT();
          else
               $this->deltaT = 0;
          $this->julianDateDT += $this->deltaT/86400;

          $this->j100_1 = ($this->julianDateDT - 2451545.0) / 36525;
          $this->j100_2 = $this->j100_1*$this->j100_1;
          $this->j100_3 = $this->j100_2*$this->j100_1;
          $this->j100_4 = $this->j100_3*$this->j100_1;

          $this->getCorrection($this->obliquityEcliptic, $this->nutation, $this->excentricity);
          $this->siderealTime = $this->getSiderealTime();
          
          // VSOP87
          $this->j1000_1 = ($this->julianDateDT - 2451545.0) / 365250;
          $this->j1000_2 = $this->j1000_1*$this->j1000_1;
          $this->j1000_3 = $this->j1000_2*$this->j1000_1;
          $this->j1000_4 = $this->j1000_3*$this->j1000_1;
     }
     public function get($name, $property)
     {
          if (!isset($this->object[$name]))
               $this->compute($name);
          if (isset($this->object[$name][$property]))
               return($this->object[$name][$property]);
          echo "CS_ERROR: property '{$property}' for object '{$name}' is not exist ";
     }
     // PRIVATE //////////////////////////////////////////////////////////////////////////////////////////////////////////////////
     private function compute($name)
     {
          switch ($name) 
          {
          case "Earth":
               $this->computeEarth();
               break;
          case "Sun":
               $this->computeSun();
               break;
          case "Moon":
               $this->computeMoon();
               break;
          case "Mercury":
               $this->computeMercury();
               break;
          case "Venus":
               $this->computeVenus();
               break;
          case "Mars":
               $this->computeMars();
               break;
          case "Jupiter":
               $this->computeJupiter();
               break;
          case "Saturn":
               $this->computeSaturn();
               break;
          case "Uranus":
               $this->computeUranus();
               break;
          case "Neptune":
               $this->computeNeptune();
               break;
          default:
               if (isset($this->objectInit[$name]))
                    $this->computeObject($name);
               else
                    echo "CS_ERROR: object '{$name}' is not exist ";
          }
     }
     // AA61
     private function getJulianDate()
     {
          $year = gmdate("Y", $this->time);
          $month = gmdate("n", $this->time);
          $day = gmdate("j", $this->time);
          $day += gmdate("H", $this->time)/24 + date("i", $this->time)/1440 + date("s", $this->time)/86400;
     
          if ($month < 3)
          {
               $year -= 1;
               $month += 12;
          }
          $a = (int)($year/100);
          if ($year < 1582 or ($year == 1582 and $month < 10) or ($year == 1582 and $month == 10 and $day < 15))
               $b = 0;
          else
               $b = 2 - $a + (int)($a/4);
          return((int)(365.25*($year+4716)) + (int)(30.6001*($month+1)) + $day + $b - 1524.5);    
     }
     // AA88
     private function getSiderealTime()
     {
          $T = ($this->julianDate - 2451545.0) / 36525;
          $st = 280.46061837 + 360.98564736629*($this->julianDate - 2451545.0) + 0.000387933*$T*$T - 0.0000000258*$T*$T*$T;
          $st += $this->longitude;
          return($this->fixed360($st));
     }
     // http://eclipse.gsfc.nasa.gov/SEcat5/deltatpoly.html
     private function getDeltaT()
     {
          $year = Date("Y", $this->time);
          $month = Date("n", $this->time);
          $y = $year + ($month - 0.5) / 12.0;
          if ($year < -500)
          {
               $u = ($year - 1820) / 100.0;
               $deltaT = -20 + 32*$u*$u;
          }
          elseif ($year < 500)
          {
               $u = $y / 100.0;
               $deltaT = 10583.6 - 1014.41*$u + 33.78311*$u*$u - 5.952053*pow($u,3) - 0.1798452*pow($u,4) + 0.022174192*pow($u,5) + 0.0090316521*pow($u,6);
          }
          elseif ($year < 1600)
          {
               $u = ($y - 1000) / 100.0;
               $deltaT = 1574.2 - 556.01*$u + 71.23472*$u*$u + 0.319781*pow($u,3) - 0.8503463*pow($u,4) - 0.005050998*pow($u,5) + 0.0083572073*pow($u,6);
          }
          elseif ($year < 1700)
          {
               $t = $y - 1600;
               $deltaT = 120 - 0.9808*$t - 0.01532*$t*$t + pow($t,3)/7129;
          }
          elseif ($year < 1800)
          {
               $t = $y - 1700;
               $deltaT = 8.83 + 0.1603*$t - 0.0059285*$t*$t + 0.00013336*pow($t,3) - pow($t,4)/1174000;
          }
          elseif ($year < 1860)
          {
               $t = $y - 1800;
               $deltaT = 13.72 - 0.332447*$t + 0.0068612*$t*$t + 0.0041116*pow($t,3) - 0.00037436*pow($t,4) + 0.0000121272*pow($t,5) - 0.0000001699*pow($t,6) + 0.000000000875*pow($t,7);
          }
          elseif ($year < 1900)
          {
               $t = $y - 1860;
               $deltaT = 7.62 + 0.5737*$t - 0.251754*$t*$t + 0.01680668*pow($t,3) - 0.0004473624*pow($t,4) + pow($t,5)/233174;
          }
          elseif ($year < 1920)
          {
               $t = $y - 1900;
               $deltaT = -2.79 + 1.494119*$t - 0.0598939*$t*$t + 0.0061966*pow($t,3) - 0.000197*pow($t,4);
          }
          elseif ($year < 1941)
          {
               $t = $y - 1920;
               $deltaT = 21.20 + 0.84493*$t - 0.076100*$t*$t + 0.0020936*pow($t,3);
          }
          elseif ($year < 1961)
          {
               $t = $y - 1950;
               $deltaT = 29.07 + 0.407*$t - $t*$t/233 + pow($t,3)/2547;
          }
          elseif ($year < 1986)
          {
               $t = $y - 1975;
               $deltaT = 45.45 + 1.067*$t - $t*$t/260 - pow($t,3)/718;
          }
          elseif ($year < 2005)
          {
               $t = $y - 2000;
               $deltaT = 63.86 + 0.3345*$t - 0.060374*$t*$t + 0.0017275*pow($t,3) + 0.000651814*pow($t,4) + 0.00002373599*pow($t,5);
          }
          elseif ($year < 2050)
          {
               $t = $y - 2000;
               $deltaT = 62.92 + 0.32217*$t + 0.005589*$t*$t;
          }
          elseif ($year < 2150)
          {
               $u = ($y - 1820) / 100.0;
               $deltaT = -20 + 32*$u*$u - 0.5628 * (2150 - $y);
          }
          else
          {
               $u = ($year - 1820) / 100.0;
               $deltaT = -20 + 32*$u*$u;
          }
          return($deltaT);
     }
     // AA144,147
     private function getCorrection(&$eps, &$psi, &$exc)
     {
          $Omega = 125.04452 - 1934.136261*$this->j100_1 + 0.0020708*$this->j100_2 + 0.000002222*$this->j100_3;
          $L = 280.4665 + 36000.7698*$this->j100_1;
          $Li = 218.3165 + 481267.8813*$this->j100_1;
          // obliquity ecliptic
          $delta_eps = 9.20*$this->dcos($Omega) + 0.57*$this->dcos(2*$L) + 0.10*$this->dcos(2*$Li) - 0.09*$this->dcos(2*$Omega);
          $eps = 23.439291111 - 0.013004167*$this->j100_1 - 0.0000001639*$this->j100_2 + 0.0000005036*$this->j100_3;
          $eps += $delta_eps/3600;
          // nutation
          $psi = -17.20*$this->dsin($Omega) - 1.32*$this->dsin(2*$L) - 0.23*$this->dsin(2*$Li) + 0.21*$this->dsin(2*$Omega);
          $psi /= 3600;       
          // excentricity
          $exc = 0.016708634 - 0.000042037*$this->j100_1 - 0.0000001267*$this->j100_2;
     }
     private function computeEarth()
     {
          // heliocentric longitude
          $l0 = array(
               array(175347046,0.0000000,0.0000000),
               array(3341656,4.6692568,6283.0758500),
               array(34894,4.6261024,12566.1517000),
               array(3497,2.7441178,5753.3848849),
               array(3418,2.8288658,3.5231183),
               array(3136,3.6276704,77713.7714681),
               array(2676,4.4180835,7860.4193924),
               array(2343,6.1351621,3930.2096962),
               array(1324,0.7424634,11506.7697698),
               array(1273,2.0370966,529.6909651),
               array(1199,1.1096295,1577.3435424),
               array(990,5.2326807,5884.9268466),
               array(902,2.0450545,26.2983198),
               array(857,3.5084915,398.1490034),
               array(780,1.1788268,5223.6939198),
               array(753,2.5333905,5507.5532387),
               array(505,4.5829260,18849.2275500),
               array(492,4.2050571,775.5226113),
               array(357,2.9195411,0.0673103),
               array(317,5.8490195,11790.6290887),
               array(284,1.8986924,796.2980068),
               array(271,0.3148626,10977.0788047),
               array(243,0.3448145,5486.7778432),
               array(206,4.8064663,2544.3144199),
               array(205,1.8695377,5573.1428014),
               array(202,2.4576779,6069.7767546),
               array(156,0.8330608,213.2990954),
               array(132,3.4111829,2942.4634233),
               array(126,1.0829546,20.7753955),
               array(115,0.6454491,0.9803211),
               array(103,0.6359985,4694.0029547),
               array(102,0.9756928,15720.8387849),
               array(102,4.2667980,7.1135470)
               );
          $l1 = array(
               array(628331966747,0,0),
               array(206059,2.678234558,6283.07585),
               array(4303,2.635122335,12566.1517),
               array(425,1.59046982,3.523118349),
               array(119,5.795557656,26.2983198),
               array(109,2.966310107,1577.343542),
               );
          $l2 = array(
               array(52919,0,0),
               array(8720,1.0721,6283.0758),
               array(309,0.867,12566.152),
               );
          $l3 = array(
               array(289,5.844,6283.076)
               );
          $l4 = array(
               array(114,3.142,0),
               );
          $L0 = $this->VSOP87term($l0);
          $L1 = $this->VSOP87term($l1);
          $L2 = $this->VSOP87term($l2);
          $L3 = $this->VSOP87term($l3);
          $L4 = $this->VSOP87term($l4);
          $L = ($L0 + $L1*$this->j1000_1 + $L2*$this->j1000_2 + $L3*$this->j1000_3 + $L4*$this->j1000_4)/1e8;
          $this->object["Earth"]["heliocentric_longitude"] = $this->fixed360($L*self::DEG);
          // heliocentric latitude = 0
          /*
          $b0 = array(
               array(280,3.19870156,84334.66158),
               array(102,5.422486193,5507.553239)
               );
          $B0 = $this->VSOP87term($b0);
          $B = ($B0)/1e8;
          $this->object["Earth"]["heliocentric_latitude"] = $B*self::DEG;
          */
          $this->object["Earth"]["heliocentric_latitude"] = 0;
          // heliocentric radius               
          $r0 = array(
               array(100013989,0,0),
               array(1670700,3.098463508,6283.07585),
               array(13956,3.055246096,12566.1517)
               );
          $r1 = array(
               array(103019,1.107490,6283.075850)
               );
          $R0 = $this->VSOP87term($r0);
          $R1 = $this->VSOP87term($r1);
          $R = ($R0 + $R1*$this->j1000_1)/1e8;
          $this->object["Earth"]["heliocentric_radius"] = $R;
     }     
     private function computeSun()
     {
          // AA167
          $aberation = (-20.4898/$this->get("Earth", "heliocentric_radius"))/3600;
          //
          $this->object["Sun"]["longitude"] = $this->fixed360($this->get("Earth", "heliocentric_longitude")+180 + $this->nutation + $aberation);
          $this->object["Sun"]["latitude"] = -$this->get("Earth", "heliocentric_latitude");
          $this->ecliptical2equatorial($this->object["Sun"]["longitude"], 0, $this->object["Sun"]["right_ascension"], $this->object["Sun"]["declination"]);
          $this->equatorial2horizontal($this->object["Sun"]["right_ascension"], $this->object["Sun"]["declination"], $this->object["Sun"]["azimuth"], $this->object["Sun"]["height"]);
     }
     // AA338
     private function computeMoon()
     {
          $Li = $this->fixed360(218.3164477 + 481267.88123421*$this->j100_1 - 0.0015786*$this->j100_2 + 0.000008319*$this->j100_3 - 0.0000000153*$this->j100_4);
          $D = $this->fixed360(297.8501921 + 445267.1114034*$this->j100_1 - 0.0018819*$this->j100_2 + 0.0000018319*$this->j100_3 - 0.0000000088*$this->j100_4);
          $M = $this->fixed360(357.5291092 + 35999.0502909*$this->j100_1 - 0.0001536*$this->j100_2 + 0.0000000408*$this->j100_3);
          $Mi = $this->fixed360(134.9633964 + 477198.8675055*$this->j100_1 + 0.0087414*$this->j100_2 + 0.000014347*$this->j100_3 - 0.00000006797*$this->j100_4);
          $F = $this->fixed360(93.2720950 + 483202.0175233*$this->j100_1 - 0.0036539*$this->j100_2 - 0.0000002836*$this->j100_3 - 0.00000000116*$this->j100_4);
          $A1 = $this->fixed360(119.75 + 131.849*$this->j100_1);
          $A2 = $this->fixed360(53.09 + 479264.290*$this->j100_1);
          $A3 = $this->fixed360(313.45 + 481266.484*$this->j100_1);
          $E = 1 - 0.002516*$this->j100_1 - 0.0000074*$this->j100_2;

          $l = array(
               array(0,0,1,0,6288774,-20905355),
               array(2,0,-1,0,1274027,-3699111),
               array(2,0,0,0,658314,-2955968),
               array(0,0,2,0,213618,-569925),
               array(0,1,0,0,-185116,48888),
               array(0,0,0,2,-114332,-3149),
               array(2,0,-2,0,58793,246158),
               array(2,-1,-1,0,57066,-152138),
               array(2,0,1,0,53322,-170733),
               array(2,-1,0,0,45758,-204586),
               array(0,1,-1,0,-40923,-129620),
               array(1,0,0,0,-34720,108743),
               array(0,1,1,0,-30383,104755),
               array(2,0,0,-2,15327,10321),
               array(0,0,1,2,-12528,0),
               array(0,0,1,-2,10980,79661),
               array(4,0,-1,0,10675,-34782),
               array(0,0,3,0,10034,-23210),
               array(4,0,-2,0,8548,-21636),
               array(2,1,-1,0,-7888,24208),
               array(2,1,0,0,-6766,30824),
               array(1,0,-1,0,-5163,-8379),
               array(1,1,0,0,4987,-16675),
               array(2,-1,1,0,4036,-12831),
               array(2,0,2,0,3994,-10445),
               array(4,0,0,0,3861,-11650),
               array(2,0,-3,0,3665,14403),
               array(0,1,-2,0,-2689,-7003),
               array(2,0,-1,2,-2602,0),
               array(2,-1,-2,0,2390,10056),
               array(1,0,1,0,-2348,6322),
               array(2,-2,0,0,2236,-9884),
               array(0,1,2,0,-2120,5751),
               array(0,2,0,0,-2069,0),
               array(2,-2,-1,0,2048,-4950),
               array(2,0,1,-2,-1773,4130),
               array(2,0,0,2,-1595,0),
               array(4,-1,-1,0,1215,-3958),
               array(0,0,2,2,-1110,0)
               );
          $sl = 0;
          $sr = 0;
          foreach ($l as $i => $a)
          {
               $sl += $a[4]*$E*$this->dsin($a[0]*$D + $a[1]*$M + $a[2]*$Mi + $a[3]*$F);
               $sr += $a[5]*$E*$this->dcos($a[0]*$D + $a[1]*$M + $a[2]*$Mi + $a[3]*$F);
          }
          $sl += 3958*$this->dsin($A1) + 1962*$this->dsin($Li-$F) + 318*$this->dsin($A2);
          $this->object["Moon"]["longitude"] = $this->fixed360($Li + $sl/1000000);
          $this->object["Moon"]["distance"] = 385000 + $sr/1000;
          
          $b = array(
               array(0,0,0,1,5128122),
               array(0,0,1,1,280602),
               array(0,0,1,-1,277693),
               array(2,0,0,-1,173237),
               array(2,0,-1,1,55413),
               array(2,0,-1,-1,46271),
               array(2,0,0,1,32573),
               array(0,0,2,1,17198),
               array(2,0,1,-1,9266),
               array(0,0,2,-1,8822),
               array(2,-1,0,-1,8216),
               array(2,0,-2,-1,4324),
               array(2,0,1,1,4200),
               array(2,1,0,-1,-3359),
               array(2,-1,-1,1,2463),
               array(2,-1,0,1,2211),
               array(2,-1,-1,-1,2065),
               array(0,1,-1,-1,-1870),
               array(4,0,-1,-1,1828),
               array(0,1,0,1,-1794),
               array(0,0,0,3,-1749),
               array(0,1,-1,1,-1565),
               array(1,0,0,1,-1491),
               array(0,1,1,1,-1475),
               array(0,1,1,-1,-1410),
               array(0,1,0,-1,-1344),
               array(1,0,0,-1,-1335),
               array(0,0,3,1,1107),
               array(4,0,0,-1,1021)
               );
          $sb = 0;
          foreach ($b as $i => $a)
               $sb += $a[4]*$E*$this->dsin($a[0]*$D + $a[1]*$M + $a[2]*$Mi + $a[3]*$F);
          $sb += -2235*$this->dsin($Li) + 382*$this->dsin($A3) + 175*$this->dsin($A1-$F) + 175*$this->dsin($A1+$F) + 127*$this->dsin($Li-$Mi) - 115*$this->dsin($Li+$Mi);            
          $this->object["Moon"]["latitude"] = $sb/1000000;

          $this->ecliptical2equatorial($this->object["Moon"]["longitude"], $this->object["Moon"]["latitude"], $this->object["Moon"]["right_ascension"], $this->object["Moon"]["declination"]);
          $this->geocentric2topocentric($this->object["Moon"]["right_ascension"], $this->object["Moon"]["declination"], $this->object["Moon"]["distance"], $this->object["Moon"]["topocentric_right_ascension"], $this->object["Moon"]["topocentric_declination"]);
          $this->equatorial2horizontal($this->object["Moon"]["topocentric_right_ascension"], $this->object["Moon"]["topocentric_declination"], $this->object["Moon"]["azimuth"], $this->object["Moon"]["height"]);

          // AA345 (Faze Mesice)
          $i = 180 - $D - 6.289*$this->dsin($Mi) + 2.1*$this->dsin($M) - 1.274*$this->dsin(2*$D-$Mi) - 0.658*$this->dsin(2*$D) - 0.214*$this->dsin(2*$Mi) - 0.110*$this->dsin($D);
          $this->object["Moon"]["phase"] = (1 + $this->dcos($i))/2;
          if ($this->dcos($this->get("Sun","declination"))*$this->dsin($this->get("Sun", "right_ascension")-$this->object["Moon"]["right_ascension"]) > 0)
               $this->object["Moon"]["phase"] *= -1;    //couva
     }
     private function computeMercury()
     {
          // heliocentric longitude
          $l0 = array(
               array(440250710,0.00000000000,0.0000000000),
               array(40989415,1.48302034194,26087.9031415742),
               array(5046294,4.47785489540,52175.8062831484),
               array(855347,1.16520322351,78263.7094247225),
               array(165590,4.11969163181,104351.6125662960),
               array(34562,0.77930765817,130439.5157078700),
               array(7583,3.71348400510,156527.4188494450),
               array(3560,1.51202669419,1109.3785520934),
               array(1726,0.35832239908,182615.3219910190),
               array(1803,4.10333178410,5661.3320491522),
               array(1365,4.59918318745,27197.2816936676),
               array(1590,2.99510417815,25028.5212113850),
               array(1017,0.88031439040,31749.2351907264)
               );
          $l1 = array(
               array(2608814706223,0.00000000000,0.0000000000),
               array(1126008,6.21703970996,26087.9031415742),
               array(303471,3.05565472363,52175.8062831484),
               array(80538,6.10454743366,78263.7094247225),
               array(21245,2.83531934452,104351.6125662960),
               array(5592,5.82675673328,130439.5157078700),
               array(1472,2.51845458395,156527.4188494450)
               );
          $l2 = array(
               array(53050,0.00000000000,0.0000000000),
               array(16904,4.69072300649,26087.9031415742),
               array(7397,1.34735624669,52175.8062831484),
               array(3018,4.45643539705,78263.7094247225),
               array(1107,1.26226537554,104351.6125662960)
               );
          $L0 = $this->VSOP87term($l0);
          $L1 = $this->VSOP87term($l1);
          $L2 = $this->VSOP87term($l2);
          $L = ($L0 + $L1*$this->j1000_1 + $L2*$this->j1000_2)/1e8;
          $this->object["Mercury"]["heliocentric_longitude"] = $this->fixed360($L*self::DEG);
          // heliocentric latitude
          $b0 = array(
               array(11737529,1.98357498767,26087.9031415742),
               array(2388077,5.03738959685,52175.8062831484),
               array(1222840,3.14159265359,0.0000000000),
               array(543252,1.79644363963,78263.7094247225),
               array(129779,4.83232503961,104351.6125662960),
               array(31867,1.58088495667,130439.5157078700),
               array(7963,4.60972126348,156527.4188494450),
               array(2014,1.35324164694,182615.3219910190)
               );
          $b1 = array(
               array(429151,3.50169780393,26087.9031415742),
               array(146234,3.14159265359,0.0000000000),
               array(22675,0.01515366880,52175.8062831484),
               array(10895,0.48540174006,78263.7094247225),
               array(6353,3.42943919982,104351.6125662960),
               array(2496,0.16051210665,130439.5157078700)
               );
          $b2 = array(
               array(11831,4.79065585784,26087.9031415742),
               array(1914,0.00000000000,0.0000000000),
               array(1045,1.21216540536,52175.8062831484)
               );
          $B0 = $this->VSOP87term($b0);
          $B1 = $this->VSOP87term($b1);
          $B2 = $this->VSOP87term($b2);
          $B = ($B0 + $B1*$this->j1000_1 + $B2*$this->j1000_2)/1e8;
          $this->object["Mercury"]["heliocentric_latitude"] = $B*self::DEG;
          // heliocentric radius
          $r0 = array(
               array(39528272,0.00000000000,0.0000000000),
               array(7834132,6.19233722599,26087.9031415742),
               array(795526,2.95989690096,52175.8062831484),
               array(121282,6.01064153805,78263.7094247225),
               array(21922,2.77820093975,104351.6125662960)
               );
          $r1 = array(
               array(217348,4.65617158663,26087.9031415742),
               array(44142,1.42385543975,52175.8062831484),
               array(10094,4.47466326316,78263.7094247225)
               );
          $R0 = $this->VSOP87term($r0);
          $R1 = $this->VSOP87term($r1);
          $R = ($R0 + $R1*$this->j1000_1)/1e8;
          $this->object["Mercury"]["heliocentric_radius"] = $R;
          
          $this->heliocentric2ecliptical($this->object["Mercury"]["heliocentric_longitude"], $this->object["Mercury"]["heliocentric_latitude"], $this->object["Mercury"]["heliocentric_radius"], $this->object["Mercury"]["longitude"], $this->object["Mercury"]["latitude"], $this->object["Mercury"]["distance"]);
          $this->ecliptical2equatorial($this->object["Mercury"]["longitude"], $this->object["Mercury"]["latitude"], $this->object["Mercury"]["right_ascension"], $this->object["Mercury"]["declination"]);
          $this->equatorial2horizontal($this->object["Mercury"]["right_ascension"], $this->object["Mercury"]["declination"], $this->object["Mercury"]["azimuth"], $this->object["Mercury"]["height"]);
     }

     private function computeVenus()
     {
          // heliocentric longitude
          $l0 = array(
               array(317614667,0,0),
               array(1353968,5.593133196,10213.28555),
               array(89892,5.306500485,20426.57109),
               array(5477,4.416306525,7860.419392),
               array(3456,2.699644708,11790.62909),
               array(2372,2.993775396,3930.209696),
               array(1664,4.25018935,1577.343542),
               array(1438,4.15745044,9683.594581),
               array(1317,5.186682191,26.2983198),
               array(1201,6.153571153,30639.85664)
               );
          $l1 = array(
               array(1021352943053,0,0),
               array(95708,2.46424449,10213.28555),
               array(14445,0.516245647,20426.57109)
               );
          $l2 = array(
               array(54127,0,0),
               array(3891,0.3451436,10213.28555),
               array(1338,2.020112861,20426.57109)
               );
          $L0 = $this->VSOP87term($l0);
          $L1 = $this->VSOP87term($l1);
          $L2 = $this->VSOP87term($l2);
          $L = ($L0 + $L1*$this->j1000_1 + $L2*$this->j1000_2)/1e8;
          $this->object["Venus"]["heliocentric_longitude"] = $this->fixed360($L*self::DEG);
          // heliocentric latitude
          $b0 = array(
               array(5923638,0.2670278,10213.2855462),
               array(40108,1.14737,20426.57109),
               array(32815,3.14159,0),
               array(1011,1.0895,30639.8566)
               );
          $b1 = array(
               array(513348,1.803643,10213.285546),
               array(4380,3.3862,20426.5711)
               );
          $b2 = array(
               array(22378,3.38509,10213.28555)
               );
          $B0 = $this->VSOP87term($b0);
          $B1 = $this->VSOP87term($b1);
          $B2 = $this->VSOP87term($b2);
          $B = ($B0 + $B1*$this->j1000_1 + $B2*$this->j1000_2)/1e8;
          $this->object["Venus"]["heliocentric_latitude"] = $B*self::DEG;
          // heliocentric radius
          $r0 = array(
               array(72334821,0,0),
               array(489824,4.021518,10213.285546)
               );
          $r1 = array(
               array(34551,0.89199,10213.28555)
               );
          $R0 = $this->VSOP87term($r0);
          $R1 = $this->VSOP87term($r1);
          $R = ($R0 + $R1*$this->j1000_1)/1e8;
          $this->object["Venus"]["heliocentric_radius"] = $R;
          
          $this->heliocentric2ecliptical($this->object["Venus"]["heliocentric_longitude"], $this->object["Venus"]["heliocentric_latitude"], $this->object["Venus"]["heliocentric_radius"], $this->object["Venus"]["longitude"], $this->object["Venus"]["latitude"], $this->object["Venus"]["distance"]);
          $this->ecliptical2equatorial($this->object["Venus"]["longitude"], $this->object["Venus"]["latitude"], $this->object["Venus"]["right_ascension"], $this->object["Venus"]["declination"]);
          $this->equatorial2horizontal($this->object["Venus"]["right_ascension"], $this->object["Venus"]["declination"], $this->object["Venus"]["azimuth"], $this->object["Venus"]["height"]);
     }
     private function computeMars()
     {
          // heliocentric longitude
          $l0 = array(
               array(620347712,0.000000000000,0.000000000000),
               array(18656368,5.050371003030,3340.612426699800),
               array(1108217,5.400998369580,6681.224853399600),
               array(91798,5.754787451110,10021.837280099400),
               array(27745,5.970495129420,3.523118349000),
               array(12316,0.849560812380,2810.921461605200),
               array(10610,2.939585249730,2281.230496510600),
               array(8927,4.156978459390,0.017253652200),
               array(8716,6.110051597920,13362.449706799200),
               array(7775,3.339686550740,5621.842923210400),
               array(6798,0.364622436260,398.149003408200),
               array(4161,0.228149753300,2942.463423291600),
               array(3575,1.661865401410,2544.314419883400),
               array(3075,0.856965970820,191.448266111600),
               array(2938,6.078937114080,0.067310302800),
               array(2628,0.648061435700,3337.089308350800),
               array(2580,0.029967061970,3344.135545048800),
               array(2389,5.038964013490,796.298006816400),
               array(1799,0.656340268440,529.690965094600),
               array(1546,2.915796333920,1751.539531416000),
               array(1528,1.149793062280,6151.533888305000),
               array(1286,3.067959246260,2146.165416475200),
               array(1264,3.622750922310,5092.151958115800),
               array(1025,3.693342935550,8962.455349910200)
               );
          $l1 = array(
               array(334085627474,0.00000000000,0.00000000000),
               array(1458227,3.60426053609,3340.61242669980),
               array(164901,3.92631250962,6681.22485339960),
               array(19963,4.26594061030,10021.83728009940),
               array(3452,4.73210386365,3.52311834900),
               array(2485,4.61277567318,13362.44970679920)
               );
          $l2 = array(
               array(58016,2.04979463279,3340.61242669980),
               array(54188,0.00000000000,0.00000000000),
               array(13908,2.45742359888,6681.22485339960),
               array(2465,2.80000020929,10021.83728009940)
               );
          $l3 = array(
               array(1482,0.44434694876,3340.61242669980)
               );
          $L0 = $this->VSOP87term($l0);
          $L1 = $this->VSOP87term($l1);
          $L2 = $this->VSOP87term($l2);
          $L3 = $this->VSOP87term($l3);
          $L = ($L0 + $L1*$this->j1000_1 + $L2*$this->j1000_2 + $L3*$this->j1000_3)/1e8;
          $this->object["Mars"]["heliocentric_longitude"] = $this->fixed360($L*self::DEG);
          // heliocentric latitude
          $b0 = array(
               array(3197135,3.76832042432,3340.61242669980),
               array(298033,4.10616996243,6681.22485339960),
               array(289105,0.00000000000,0.00000000000),
               array(31366,4.44651052853,10021.83728009940),
               array(3484,4.78812547889,13362.44970679920)
               );
          $b1 = array(
               array(350069,5.36847836211,3340.61242669980),
               array(14116,3.14159265359,0.00000000000),
               array(9671,5.47877786506,6681.22485339960),
               array(1472,3.20205766795,10021.83728009940)
               );
          $b2 = array(
               array(16727,0.60221392419,3340.61242669980),
               array(4987,3.14159265359,0.00000000000)
               );
          $B0 = $this->VSOP87term($b0);
          $B1 = $this->VSOP87term($b1);
          $B2 = $this->VSOP87term($b2);
          $B = ($B0 + $B1*$this->j1000_1 + $B2*$this->j1000_2)/1e8;
          $this->object["Mars"]["heliocentric_latitude"] = $B*self::DEG;
          // heliocentric radius
          $r0 = array(
               array(153033488,0.00000000000,0.00000000000),
               array(14184953,3.47971283519,3340.61242669980),
               array(660776,3.81783442097,6681.22485339960),
               array(46179,4.15595316284,10021.83728009940),
               array(8110,5.55958460165,2810.92146160520),
               array(7485,1.77238998069,5621.84292321040),
               array(5523,1.36436318880,2281.23049651060),
               array(3825,4.49407182408,13362.44970679920),
               array(2484,4.92545577893,2942.46342329160),
               array(2307,0.09081742493,2544.31441988340),
               array(1999,5.36059605227,3337.08930835080),
               array(1960,4.74249386323,3344.13554504880),
               array(1167,2.11261501155,5092.15195811580),
               array(1103,5.00908264160,398.14900340820)
               );
          $r1 = array(
               array(1107433,2.03250524950,3340.61242669980),
               array(103176,2.37071845682,6681.22485339960),
               array(12877,0.00000000000,0.00000000000),
               array(10816,2.70888093803,10021.83728009940),
               array(1195,3.04702182503,13362.44970679920)
               );
          $r2 = array(
               array(44242,0.47930603943,3340.61242669980),
               array(8138,0.86998398093,6681.22485339960),
               array(1275,1.22594050809,10021.83728009940)
               );
          $r3 = array(
               array(1113,5.14987350142,3340.61242669980)
               );
          $R0 = $this->VSOP87term($r0);
          $R1 = $this->VSOP87term($r1);
          $R2 = $this->VSOP87term($r2);
          $R3 = $this->VSOP87term($r3);
          $R = ($R0 + $R1*$this->j1000_1 + $R2*$this->j1000_2 + $R3*$this->j1000_3)/1e8;
          $this->object["Mars"]["heliocentric_radius"] = $R;
          
          $this->heliocentric2ecliptical($this->object["Mars"]["heliocentric_longitude"], $this->object["Mars"]["heliocentric_latitude"], $this->object["Mars"]["heliocentric_radius"], $this->object["Mars"]["longitude"], $this->object["Mars"]["latitude"], $this->object["Mars"]["distance"]);
          $this->ecliptical2equatorial($this->object["Mars"]["longitude"], $this->object["Mars"]["latitude"], $this->object["Mars"]["right_ascension"], $this->object["Mars"]["declination"]);
          $this->equatorial2horizontal($this->object["Mars"]["right_ascension"], $this->object["Mars"]["declination"], $this->object["Mars"]["azimuth"], $this->object["Mars"]["height"]);
     }
     private function computeJupiter()
     {
          // heliocentric longitude
          $l0 = array(
               array(59954691,0,0),
               array(9695899,5.061917931,529.6909651),
               array(573610,1.44406206,7.113547001),
               array(306389,5.4173473,1059.38193),
               array(97178,4.142647088,632.7837393),
               array(72903,3.640429093,522.5774181),
               array(64264,3.411451852,103.0927742),
               array(39806,2.293767449,419.4846439),
               array(38858,1.272317249,316.3918697),
               array(27965,1.784545895,536.8045121),
               array(13590,5.774810316,1589.072895),
               array(8769,3.630003244,949.175609),
               array(8246,3.582279617,206.1855484),
               array(7368,5.081011256,735.8765135),
               array(6263,0.024976437,213.2990954),
               array(6114,4.513195317,1162.474704),
               array(5305,4.186250535,1052.268383),
               array(5305,1.306712368,14.227094),
               array(4905,1.320846317,110.2063212),
               array(4647,4.699581095,3.932153263),
               array(3045,4.316759603,426.5981909),
               array(2610,1.566675949,846.0828348),
               array(2028,1.063765474,3.181393738),
               array(1921,0.971689288,639.8972863),
               array(1765,2.141480778,1066.495477),
               array(1723,3.880360089,1265.567479),
               array(1633,3.582010898,515.4638711),
               array(1432,4.296836903,625.6701923)
               );
          $l1 = array(
               array(52993480757,0,0),
               array(489741,4.220666899,529.6909651),
               array(228919,6.02647464,7.113547001),
               array(27655,4.572659568,1059.38193),
               array(20721,5.459389363,522.5774181),
               array(12106,0.16985765,536.8045121),
               array(6068,4.42419502,103.0927742),
               array(5434,3.984783826,419.4846439),
               array(4238,5.890093513,14.227094),
               array(2212,5.267714466,206.1855484),
               array(1746,4.926693785,1589.072895),
               array(1296,5.551327651,3.181393738),
               array(1173,5.856473044,1052.268383),
               array(1163,0.514508953,3.932153263),
               array(1099,5.307049816,515.4638711),
               array(1007,0.464783986,735.8765135),
               array(1004,3.150403018,426.5981909)
               );
          $l2 = array(
               array(47234,4.321483236,7.113547001),
               array(38966,0,0),
               array(30629,2.930214402,529.6909651),
               array(3189,1.055046156,522.5774181),
               array(2729,4.845454814,536.8045121),
               array(2723,3.414115266,1059.38193),
               array(1721,4.187343852,14.227094)
               );
          $l3 = array(
               array(6502,2.598628805,7.113547001),
               array(1357,1.346358864,529.6909651)
               );
          $L0 = $this->VSOP87term($l0);
          $L1 = $this->VSOP87term($l1);
          $L2 = $this->VSOP87term($l2);
          $L3 = $this->VSOP87term($l3);
          $L = ($L0 + $L1*$this->j1000_1 + $L2*$this->j1000_2 + $L3*$this->j1000_3)/1e8;
          $this->object["Jupiter"]["heliocentric_longitude"] = $this->fixed360($L*self::DEG);
          // heliocentric latitude
          $b0 = array(
               array(2268616,3.558526067,529.6909651),
               array(110090,0,0),
               array(109972,3.908093474,1059.38193),
               array(8101,3.605095734,522.5774181),
               array(6438,0.306271214,536.8045121),
               array(6044,4.258831088,1589.072895),
               array(1107,2.985344219,1162.474704)
               );
          $b1 = array(
               array(177352,5.701664885,529.6909651),
               array(3230,5.779416193,1059.38193),
               array(3081,5.474642965,522.5774181),
               array(2212,4.734774802,536.8045121),
               array(1694,3.141592654,0)
               );
          $b2 = array(
               array(8094,1.463228437,529.6909651)
               );
          $B0 = $this->VSOP87term($b0);
          $B1 = $this->VSOP87term($b1);
          $B2 = $this->VSOP87term($b2);
          $B = ($B0 + $B1*$this->j1000_1 + $B2*$this->j1000_2)/1e8;
          $this->object["Jupiter"]["heliocentric_latitude"] = $B*self::DEG;
          // heliocentric radius
          $r0 = array(
               array(520887429,0,0),
               array(25209327,3.4910864,529.6909651),
               array(610600,3.841153656,1059.38193),
               array(282029,2.574198799,632.7837393),
               array(187647,2.075903801,522.5774181),
               array(86793,0.710010906,419.4846439),
               array(72063,0.214656947,536.8045121),
               array(65517,5.979958508,316.3918697),
               array(30135,2.161320584,949.175609),
               array(29135,1.677592437,103.0927742),
               array(23947,0.274578549,7.113547001),
               array(23453,3.540231473,735.8765135),
               array(22284,4.193627735,1589.072895),
               array(13033,2.960430557,1162.474704),
               array(12749,2.715501029,1052.268383)
               );
          $r1 = array(
               array(1271802,2.649375111,529.6909651),
               array(61662,3.00076251,1059.38193),
               array(53444,3.897176442,522.5774181),
               array(41390,0,0),
               array(31185,4.882766635,536.8045121),
               array(11847,2.413295882,419.4846439)
               );
          $r2 = array(
               array(79645,1.358658966,529.6909651)
               );
          $R0 = $this->VSOP87term($r0);
          $R1 = $this->VSOP87term($r1);
          $R2 = $this->VSOP87term($r2);
          $R = ($R0 + $R1*$this->j1000_1 + $R2*$this->j1000_2)/1e8;
          $this->object["Jupiter"]["heliocentric_radius"] = $R;
          
          $this->heliocentric2ecliptical($this->object["Jupiter"]["heliocentric_longitude"], $this->object["Jupiter"]["heliocentric_latitude"], $this->object["Jupiter"]["heliocentric_radius"], $this->object["Jupiter"]["longitude"], $this->object["Jupiter"]["latitude"], $this->object["Jupiter"]["distance"]);
          $this->ecliptical2equatorial($this->object["Jupiter"]["longitude"], $this->object["Jupiter"]["latitude"], $this->object["Jupiter"]["right_ascension"], $this->object["Jupiter"]["declination"]);
          $this->equatorial2horizontal($this->object["Jupiter"]["right_ascension"], $this->object["Jupiter"]["declination"], $this->object["Jupiter"]["azimuth"], $this->object["Jupiter"]["height"]);
     }
     private function computeSaturn()
     {
          // heliocentric longitude
          $l0 = array(
               array(87401354,0,0.0000000000),
               array(11107660,3.96205090194,213.2990954380),
               array(1414151,4.58581515873,7.1135470008),
               array(398379,0.52112025957,206.1855484372),
               array(350769,3.30329903015,426.5981908760),
               array(206816,0.24658366938,103.0927742186),
               array(79271,3.84007078530,220.4126424388),
               array(23990,4.66976934860,110.2063212194),
               array(16574,0.43719123541,419.4846438752),
               array(14907,5.76903283845,316.3918696566),
               array(15820,0.93808953760,632.7837393132),
               array(14610,1.56518573691,3.9321532631),
               array(13160,4.44891180176,14.2270940016),
               array(15054,2.71670027883,639.8972863140),
               array(13005,5.98119067061,11.0457002639),
               array(10725,3.12939596466,202.2533951741),
               array(5863,0.23657028777,529.6909650946),
               array(5228,4.20783162380,3.1813937377),
               array(6126,1.76328499656,277.0349937414),
               array(5020,3.17787919533,433.7117378768),
               array(4593,0.61976424374,199.0720014364),
               array(4006,2.24479893937,63.7358983034),
               array(2954,0.98280385206,95.9792272178),
               array(3874,3.22282692566,138.5174968707),
               array(2461,2.03163631205,735.8765135318),
               array(3269,0.77491895787,949.1756089698),
               array(1758,3.26580514774,522.5774180938),
               array(1640,5.50504966218,846.0828347512),
               array(1391,4.02331978116,323.5054166574),
               array(1581,4.37266314120,309.2783226558),
               array(1124,2.83726793572,415.5524906121),
               array(1017,3.71698151814,227.5261894396)
               );
          $l1 = array(
               array(21354295596,0.00000000000,0.0000000000),
               array(1296855,1.82820544701,213.2990954380),
               array(564348,2.88500136429,7.1135470008),
               array(98323,1.08070061328,426.5981908760),
               array(107679,2.27769911872,206.1855484372),
               array(40255,2.04128257090,220.4126424388),
               array(19942,1.27954662736,103.0927742186),
               array(10512,2.74880392800,14.2270940016),
               array(6939,0.40493079985,639.8972863140),
               array(4803,2.44194097666,419.4846438752),
               array(4056,2.92166618776,110.2063212194),
               array(3769,3.64965631460,3.9321532631),
               array(3385,2.41694251653,3.1813937377),
               array(3302,1.26256486715,433.7117378768),
               array(3071,2.32739317750,199.0720014364),
               array(1953,3.56394683300,11.0457002639),
               array(1249,2.62803737519,95.9792272178)
               );
          $l2 = array(
               array(116441,1.17987850633,7.1135470008),
               array(91921,0.07425261094,213.2990954380),
               array(90592,0.00000000000,0.0000000000),
               array(15277,4.06492007503,206.1855484372),
               array(10631,0.25778277414,220.4126424388),
               array(10605,5.40963595885,426.5981908760),
               array(4265,1.04595556630,14.2270940016),
               array(1216,2.91860042123,103.0927742186),
               array(1165,4.60942128971,639.8972863140),
               array(1082,5.69130351670,433.7117378768),
               array(1020,0.63369182642,3.1813937377),
               array(1045,4.04206453611,199.0720014364)
               );
          $l3 = array(
               array(16039,5.73945377424,7.1135470008),
               array(4250,4.58539675603,213.2990954380),
               array(1907,4.76082050205,220.4126424388),
               array(1466,5.91326678323,206.1855484372),
               array(1162,5.61973132428,14.2270940016),
               array(1067,3.60816533142,426.5981908760)
               );
          $l4 = array(
               array(1662,3.99826248978,7.1135470008)
               );
          $L0 = $this->VSOP87term($l0);
          $L1 = $this->VSOP87term($l1);
          $L2 = $this->VSOP87term($l2);
          $L3 = $this->VSOP87term($l3);
          $L4 = $this->VSOP87term($l4);
          $L = ($L0 + $L1*$this->j1000_1 + $L2*$this->j1000_2 + $L3*$this->j1000_3 + $L4*$this->j1000_4)/1e8;
          $this->object["Saturn"]["heliocentric_longitude"] = $this->fixed360($L*self::DEG);
          // heliocentric latitude
          $b0 = array(
               array(4330678,3.60284428399,213.2990954380),
               array(240348,2.85238489390,426.5981908760),
               array(84746,0.00000000000,0.0000000000),
               array(30863,3.48441504465,220.4126424388),
               array(34116,0.57297307844,206.1855484372),
               array(14734,2.11846597870,639.8972863140),
               array(9917,5.79003189405,419.4846438752),
               array(6994,4.73604689179,7.1135470008),
               array(4808,5.43305315602,316.3918696566),
               array(4788,4.96512927420,110.2063212194),
               array(3432,2.73255752123,433.7117378768),
               array(1506,6.01304536144,103.0927742186),
               array(1060,5.63099292414,529.6909650946)
               );
          $b1 = array(
               array(397555,5.33289992556,213.2990954380),
               array(49479,3.14159265359,0.0000000000),
               array(18572,6.09919206378,426.5981908760),
               array(14801,2.30586060520,206.1855484372),
               array(9644,1.69674660120,220.4126424388),
               array(3757,1.25429514018,419.4846438752),
               array(2717,5.91166664787,639.8972863140),
               array(1455,0.85161616532,433.7117378768),
               array(1291,2.91770857090,7.1135470008)
               );
          $b2 = array(
               array(20630,0.50482422817,213.2990954380),
               array(3720,3.99833475829,206.1855484372),
               array(1627,6.18189939500,220.4126424388),
               array(1346,0.00000000000,0.0000000000)
               );
          $B0 = $this->VSOP87term($b0);
          $B1 = $this->VSOP87term($b1);
          $B2 = $this->VSOP87term($b2);
          $B = ($B0 + $B1*$this->j1000_1 + $B2*$this->j1000_2)/1e8;
          $this->object["Saturn"]["heliocentric_latitude"] = $B*self::DEG;
          // heliocentric radius
          $r0 = array(
               array(955758136,0.00000000000,0.0000000000),
               array(52921382,2.39226219733,213.2990954380),
               array(1873680,5.23549605091,206.1855484372),
               array(1464664,1.64763045468,426.5981908760),
               array(821891,5.93520025371,316.3918696566),
               array(547507,5.01532628454,103.0927742186),
               array(371684,2.27114833428,220.4126424388),
               array(361778,3.13904303264,7.1135470008),
               array(140618,5.70406652991,632.7837393132),
               array(108975,3.29313595577,110.2063212194),
               array(69007,5.94099622447,419.4846438752),
               array(61053,0.94037761156,639.8972863140),
               array(48913,1.55733388472,202.2533951741),
               array(34144,0.19518550682,277.0349937414),
               array(32402,5.47084606947,949.1756089698),
               array(20937,0.46349163993,735.8765135318),
               array(20839,1.52102590640,433.7117378768),
               array(20747,5.33255667599,199.0720014364),
               array(15298,3.05943652881,529.6909650946),
               array(14296,2.60433537909,323.5054166574),
               array(11993,5.98051421881,846.0828347512),
               array(11380,1.73105746566,522.5774180938),
               array(12884,1.64892310393,138.5174968707)
               );
          $r1 = array(
               array(6182981,0.25843515034,213.2990954380),
               array(506578,0.71114650941,206.1855484372),
               array(341394,5.79635773960,426.5981908760),
               array(188491,0.47215719444,220.4126424388),
               array(186262,3.14159265359,0.0000000000),
               array(143891,1.40744864239,7.1135470008),
               array(49621,6.01744469580,103.0927742186),
               array(20928,5.09245654470,639.8972863140),
               array(19953,1.17560125007,419.4846438752),
               array(18840,1.60819563173,110.2063212194),
               array(12893,5.94330258435,433.7117378768),
               array(13877,0.75886204364,199.0720014364)
               );
          $r2 = array(
               array(436902,4.78671673044,213.2990954380),
               array(71923,2.50069994874,206.1855484372),
               array(49767,4.97168150870,220.4126424388),
               array(43221,3.86940443794,426.5981908760),
               array(29646,5.96310264282,7.1135470008)
               );
          $r3 = array(
               array(20315,3.02186626038,213.2990954380)
               );
          $R0 = $this->VSOP87term($r0);
          $R1 = $this->VSOP87term($r1);
          $R2 = $this->VSOP87term($r2);
          $R3 = $this->VSOP87term($r3);
          $R = ($R0 + $R1*$this->j1000_1 + $R2*$this->j1000_2 + $R3*$this->j1000_3)/1e8;
          $this->object["Saturn"]["heliocentric_radius"] = $R;
          
          $this->heliocentric2ecliptical($this->object["Saturn"]["heliocentric_longitude"], $this->object["Saturn"]["heliocentric_latitude"], $this->object["Saturn"]["heliocentric_radius"], $this->object["Saturn"]["longitude"], $this->object["Saturn"]["latitude"], $this->object["Saturn"]["distance"]);
          $this->ecliptical2equatorial($this->object["Saturn"]["longitude"], $this->object["Saturn"]["latitude"], $this->object["Saturn"]["right_ascension"], $this->object["Saturn"]["declination"]);
          $this->equatorial2horizontal($this->object["Saturn"]["right_ascension"], $this->object["Saturn"]["declination"], $this->object["Saturn"]["azimuth"], $this->object["Saturn"]["height"]);
     }
     private function computeUranus()
     {
          // heliocentric longitude
          $l0 = array(
               array(548129294,0,0),
               array(9260408,0.8910642,74.7815986),
               array(1504248,3.6271926,1.4844727),
               array(365982,1.899622,73.297126),
               array(272328,3.358237,149.563197),
               array(70328,5.39254,63.73590),
               array(68893,6.09292,76.26607),
               array(61999,2.26952,2.96895),
               array(61951,2.85099,11.04570),
               array(26469,3.14152,71.81265),
               array(25711,6.11380,454.90937),
               array(21079,4.36059,148.07872),
               array(17819,1.74437,36.64856),
               array(14613,4.73732,3.93215),
               array(11163,5.82682,224.34480),
               array(10998,0.48865,138.51750),
               array(9527,2.9552,35.1641),
               array(7546,5.2363,109.9457),
               array(4220,3.2333,70.8494),
               array(4052,2.2775,151.0477),
               array(3490,5.4831,146.5943),
               array(3355,1.0655,4.4534),
               array(3144,4.7520,77.7505),
               array(2927,4.6290,9.5612),
               array(2922,5.3524,85.8273),
               array(2273,4.3660,70.3282),
               array(2149,0.6075,38.1330),
               array(2051,1.5177,0.1119),
               array(1992,4.9244,277.0350),
               array(1667,3.6274,380.1278),
               array(1533,2.5859,52.6902),
               array(1376,2.0428,65.2204),
               array(1372,4.1964,111.4302),
               array(1284,3.1135,202.2534),
               array(1282,0.5427,222.8603),
               array(1244,0.9161,2.4477),
               array(1221,0.1990,108.4612),
               array(1151,4.1790,33.6796),
               array(1150,0.9334,3.1814),
               array(1090,1.7750,12.5302),
               array(1072,0.2356,62.2514)
               );
          $l1 = array(
               array(7502543122,0,0),
               array(154458,5.242017,74.781599),
               array(24456,1.71256,1.48447),
               array(9258,0.4284,11.0457),
               array(8266,1.5022,63.7359),
               array(7842,1.3198,149.5632),
               array(3899,0.4648,3.9322),
               array(2284,4.1737,76.2661),
               array(1927,0.5301,2.9690),
               array(1233,1.5863,70.8494)
               );
          $l2 = array(
               array(53033,0.,0.),
               array(2358,2.2601,74.7816)
               );
          $L0 = $this->VSOP87term($l0);
          $L1 = $this->VSOP87term($l1);
          $L2 = $this->VSOP87term($l2);
          $L = ($L0 + $L1*$this->j1000_1 + $L2*$this->j1000_2)/1e8;
          $this->object["Uranus"]["heliocentric_longitude"] = $this->fixed360($L*self::DEG);
          // heliocentric latitude
          $b0 = array(
               array(1346278,2.6187781,74.7815986),
               array(62341,5.08111,149.56320),
               array(61601,3.14159,0.),
               array(9964,1.6160,76.2661),
               array(9926,0.5763,73.2971),
               array(3259,1.2612,224.3448),
               array(2972,2.2437,1.4845),
               array(2010,6.0555,148.0787),
               array(1522,0.2796,63.7359)
               );
          $b1 = array(
               array(206366,4.123943,74.781599),
               array(8563,0.3382,149.5632),
               array(1726,2.1219,73.2971),
               array(1374,0.,0.),
               array(1369,3.0686,76.2661)
               );
          $b2 = array(
               array(9212,5.8004,74.7816)
               );
          $B0 = $this->VSOP87term($b0);
          $B1 = $this->VSOP87term($b1);
          $B2 = $this->VSOP87term($b2);
          $B = ($B0 + $B1*$this->j1000_1 + $B2*$this->j1000_2)/1e8;
          $this->object["Uranus"]["heliocentric_latitude"] = $B*self::DEG;
          // heliocentric radius
          $r0 = array(
               array(1921264848,0,0),
               array(88784984,5.60377527,74.78159857),
               array(3440836,0.3283610,73.2971259),
               array(2055653,1.7829517,149.5631971),
               array(649322,4.522473,76.266071),
               array(602248,3.860038,63.735898),
               array(496404,1.401399,454.909367),
               array(338526,1.580027,138.517497),
               array(243508,1.570866,71.812653),
               array(190522,1.998094,1.484473),
               array(161858,2.791379,148.078724),
               array(143706,1.383686,11.045700),
               array(93192,0.17437,36.64856),
               array(89806,3.66105,109.94569),
               array(71424,4.24509,224.34480),
               array(46677,1.39977,35.16409),
               array(39026,3.36235,277.03499),
               array(39010,1.66971,70.84945),
               array(36755,3.88649,146.59425),
               array(30349,0.70100,151.04767),
               array(29156,3.18056,77.75054),
               array(25786,3.78538,85.82730),
               array(25620,5.25656,380.12777),
               array(22637,0.72519,529.69097),
               array(20473,2.79640,70.32818),
               array(20472,1.55589,202.25340),
               array(17901,0.55455,2.96895),
               array(15503,5.35405,38.13304),
               array(14702,4.90434,108.46122),
               array(12897,2.62154,111.43016),
               array(12328,5.96039,127.47180),
               array(11959,1.75044,984.60033),
               array(11853,0.99343,52.69020),
               array(11696,3.29826,3.93215),
               array(11495,0.43774,65.22037),
               array(10793,1.42105,213.29910)
               );
          $r1 = array(
               array(1479896,3.6720571,74.7815986),
               array(71212,6.22601,63.73590),
               array(68627,6.13411,149.56320),
               array(24060,3.14159,0.),
               array(21468,2.60177,76.26607),
               array(20857,5.24625,11.04570),
               array(11405,0.01848,70.84945)
               );
          $r2 = array(
               array(22440,0.69953,74.78160)
               );
          $R0 = $this->VSOP87term($r0);
          $R1 = $this->VSOP87term($r1);
          $R2 = $this->VSOP87term($r2);
          $R = ($R0 + $R1*$this->j1000_1 + $R2*$this->j1000_2)/1e8;
          $this->object["Uranus"]["heliocentric_radius"] = $R;
          
          $this->heliocentric2ecliptical($this->object["Uranus"]["heliocentric_longitude"], $this->object["Uranus"]["heliocentric_latitude"], $this->object["Uranus"]["heliocentric_radius"], $this->object["Uranus"]["longitude"], $this->object["Uranus"]["latitude"], $this->object["Uranus"]["distance"]);
          $this->ecliptical2equatorial($this->object["Uranus"]["longitude"], $this->object["Uranus"]["latitude"], $this->object["Uranus"]["right_ascension"], $this->object["Uranus"]["declination"]);
          $this->equatorial2horizontal($this->object["Uranus"]["right_ascension"], $this->object["Uranus"]["declination"], $this->object["Uranus"]["azimuth"], $this->object["Uranus"]["height"]);
     }
     private function computeNeptune()
     {
          // heliocentric longitude
          $l0 = array(
               array(531188633,0.,0.),
               array(1798476,2.9010127,38.1330356),
               array(1019728,0.4858092,1.4844727),
               array(124532,4.830081,36.648563),
               array(42064,5.41055,2.96895),
               array(37715,6.09222,35.16409),
               array(33785,1.24489,76.26607),
               array(16483,0.00008,491.55793),
               array(9199,4.9375,39.6175),
               array(8994,0.2746,175.1661),
               array(4216,1.9871,73.2971),
               array(3365,1.0359,33.6796),
               array(2285,4.2061,4.4534),
               array(1434,2.7834,74.7816)
               );
          $l1 = array(
               array(3837687717,0.,0.),
               array(16604,4.86319,1.48447),
               array(15807,2.27923,38.13304),
               array(3335,3.6820,76.2661),
               array(1306,3.6732,2.9689)
               );
          $l2 = array(
               array(53893,0.,0.)
               );
          $L0 = $this->VSOP87term($l0);
          $L1 = $this->VSOP87term($l1);
          $L2 = $this->VSOP87term($l2);
          $L = ($L0 + $L1*$this->j1000_1 + $L2*$this->j1000_2)/1e8;
          $this->object["Neptune"]["heliocentric_longitude"] = $this->fixed360($L*self::DEG);
          // heliocentric latitude
          $b0 = array(
               array(3088623,1.4410437,38.1330356),
               array(27780,5.91272,76.26607),
               array(27624,0.,0.),
               array(15448,3.50877,39.61751),
               array(15355,2.52124,36.64856),
               array(2000,1.5100,74.7816),
               array(1968,4.3778,1.4845),
               array(1015,3.2156,35.1641)
               );
          $b1 = array(
               array(227279,3.807931,38.133036),
               array(1803,1.9758,76.2661),
               array(1433,3.1416,0.),
               array(1386,4.8256,36.6486),
               array(1073,6.0805,39.6175)
               );
          $b2 = array(
               array(9691,5.5712,38.1330)
               );
          $B0 = $this->VSOP87term($b0);
          $B1 = $this->VSOP87term($b1);
          $B2 = $this->VSOP87term($b2);
          $B = ($B0 + $B1*$this->j1000_1 + $B2*$this->j1000_2)/1e8;
          $this->object["Neptune"]["heliocentric_latitude"] = $B*self::DEG;
          // heliocentric radius
          $r0 = array(
               array(3007013206,0,0),
               array(27062259,1.32999459,38.13303564),
               array(1691764,3.2518614,36.6485629),
               array(807831,5.185928,1.484473),
               array(537761,4.521139,35.164090),
               array(495726,1.571057,491.557929),
               array(274572,1.845523,175.166060),
               array(135134,3.372206,39.617508),
               array(121802,5.797544,76.266071),
               array(100895,0.377027,73.297126),
               array(69792,3.79617,2.96895),
               array(46688,5.74938,33.67962),
               array(24594,0.50802,109.94569),
               array(16939,1.59422,71.81265),
               array(14230,1.07786,74.78160),
               array(12012,1.92062,1021.24889)
               );
          $r1 = array(
               array(236339,0.704980,38.133036),
               array(13220,3.32015,1.48447)
               );
          $R0 = $this->VSOP87term($r0);
          $R1 = $this->VSOP87term($r1);
          $R = ($R0 + $R1*$this->j1000_1)/1e8;
          $this->object["Neptune"]["heliocentric_radius"] = $R;
          
          $this->heliocentric2ecliptical($this->object["Neptune"]["heliocentric_longitude"], $this->object["Neptune"]["heliocentric_latitude"], $this->object["Neptune"]["heliocentric_radius"], $this->object["Neptune"]["longitude"], $this->object["Neptune"]["latitude"], $this->object["Neptune"]["distance"]);
          $this->ecliptical2equatorial($this->object["Neptune"]["longitude"], $this->object["Neptune"]["latitude"], $this->object["Neptune"]["right_ascension"], $this->object["Neptune"]["declination"]);
          $this->equatorial2horizontal($this->object["Neptune"]["right_ascension"], $this->object["Neptune"]["declination"], $this->object["Neptune"]["azimuth"], $this->object["Neptune"]["height"]);
     }
     private function computeObject($name)
     {
          if ($this->objectInit[$name]["precession_add"])
          {    // AA134
               $dzeta = (2306.2181*$this->j100_1 + 0.30188*$this->j100_2 + 0.017998*$this->j100_3)/3600;
               $eta = (2306.2181*$this->j100_1 + 1.09468*$this->j100_2 + 0.018203*$this->j100_3)/3600;
               $theta = (2004.3109*$this->j100_1 - 0.42665*$this->j100_2 - 0.041833*$this->j100_3)/3600;
               $a = $this->dcos($this->objectInit[$name]["declination"])*$this->dsin($this->objectInit[$name]["right_ascension"] + $dzeta);
               $b = $this->dcos($theta)*$this->dcos($this->objectInit[$name]["declination"])*$this->dcos($this->objectInit[$name]["right_ascension"] + $dzeta) - $this->dsin($theta)*$this->dsin($this->objectInit[$name]["declination"]);
               $c = $this->dsin($theta)*$this->dcos($this->objectInit[$name]["declination"])*$this->dcos($this->objectInit[$name]["right_ascension"] + $dzeta) + $this->dcos($theta)*$this->dsin($this->objectInit[$name]["declination"]);
               $this->object[$name]["declination"] = $this->dasin($c);
               $this->object[$name]["right_ascension"] = $this->fixed360($eta + $this->datan2($a, $b));
          }
          else
          {
               $this->object[$name]["declination"] = $this->objectInit[$name]["declination"];
               $this->object[$name]["right_ascension"] = $this->objectInit[$name]["right_ascension"];
          }
          $this->equatorial2horizontal($this->object[$name]["right_ascension"], $this->object[$name]["declination"], $this->object[$name]["azimuth"], $this->object[$name]["height"]);
     }
     // AA 223
     /*
     private function heliocentric2ecliptical($L, $B, $R, &$lambda, &$beta, &$delta)
     {
          $x = $R*$this->dcos($B)*$this->dcos($L) - $this->EarthHeliocentricRadius*$this->dcos($this->EarthHeliocentricLatitude)*$this->dcos($this->EarthHeliocentricLongitude);
          $y = $R*$this->dcos($B)*$this->dsin($L) - $this->EarthHeliocentricRadius*$this->dcos($this->EarthHeliocentricLatitude)*$this->dsin($this->EarthHeliocentricLongitude);
          $z = $R*$this->dsin($B) - $this->EarthHeliocentricRadius*$this->dsin($this->EarthHeliocentricLatitude);
          
          $lambda = $this->fixed360($this->datan2($y,$x));
          $beta = $this->datan2($z, sqrt($x*$x + $y*$y));
          $delta = sqrt($x*$x + $y*$y + $z*$z);
     }*/
     // Earth heliocentric latitude = 0
     private function heliocentric2ecliptical($L, $B, $R, &$lambda, &$beta, &$delta)
     {
          $x = $R*$this->dcos($B)*$this->dcos($L) - $this->get("Earth", "heliocentric_radius")*$this->dcos($this->get("Earth", "heliocentric_longitude"));
          $y = $R*$this->dcos($B)*$this->dsin($L) - $this->get("Earth", "heliocentric_radius")*$this->dsin($this->get("Earth", "heliocentric_longitude"));
          $z = $R*$this->dsin($B);
          
          $lambda = $this->fixed360($this->datan2($y,$x));
          $beta = $this->datan2($z, sqrt($x*$x + $y*$y));
          $delta = sqrt($x*$x + $y*$y + $z*$z);
     }
     // AA93
     private function ecliptical2equatorial($lambda, $beta, &$alfa, &$delta)
     {
          $alfa = $this->fixed360($this->datan2($this->dsin($lambda)*$this->dcos($this->obliquityEcliptic) - $this->dtan($beta)*$this->dsin($this->obliquityEcliptic), $this->dcos($lambda)));
          $delta = $this->dasin($this->dsin($beta)*$this->dcos($this->obliquityEcliptic) + $this->dcos($beta)*$this->dsin($this->obliquityEcliptic)*$this->dsin($lambda));
     }
     // AA93 + Siroky
     private function equatorial2horizontal($rightAscension, $delta, &$A, &$h)
     {
          $H = $this->siderealTime - $rightAscension; // hour angle
          $h = $this->dasin($this->dsin($this->latitude)*$this->dsin($delta) + $this->dcos($this->latitude)*$this->dcos($delta)*$this->dcos($H));
          if ($this->dcos($h) < 1e-9)
          {
               $sin_A = 0;
               $cos_A = 0;
          }
          else
          {
               $sin_A = $this->dcos($delta)*$this->dsin($H)/$this->dcos($h);
               $cos_A = ($this->dsin($this->latitude)*$this->dcos($delta)*$this->dcos($H) - $this->dcos($this->latitude)*$this->dsin($delta)) / $this->dcos($h);
          }
          if ($sin_A >= 0 and $cos_A >= 0)
               $A = $this->dasin($sin_A);           // I.Q
          elseif ($sin_A >= 0 and $cos_A < 0)
               $A = 180 - $this->dasin($sin_A);     // II.Q
          elseif ($sin_A < 0 and $cos_A < 0)
               $A = 180 - $this->dasin($sin_A);     // III.Q
          elseif ($sin_A < 0 and $cos_A >= 0)
               $A = 360 + $this->dasin($sin_A);     // IV.Q
          else
               $A = 1e8;  //?
     }
     // http://stjarnhimlen.se/comp/ppcomp.html#13
     private function geocentric2topocentric($rightAscension, $declination, $distance, &$rightAscensionTopocentric, &$declinationTopocentric)
     {         
          $mpar = $this->dasin(6378/$distance);
          $gclat = $this->latitude - 0.1924*$this->dsin(2*$this->latitude);
          $rho = 0.99833 + 0.00167*$this->dcos(2*$this->latitude);
          $HA = $this->siderealTime - $rightAscension;
          if ($declination == 90.0 or $gclat == 0.0)
          {
               $rightAscensionTopocentric = $rightAscension;
               $declinationTopocentric = $declination - $mpar*$rho*$this->dsin(-$declination)*$this->dcos($HA);
          }
          else
          {
               $g = $this->datan2($this->dtan($gclat), $this->dcos($HA));
               $rightAscensionTopocentric = $rightAscension  - $mpar*$rho*$this->dcos($gclat)*$this->dsin($HA)/$this->dcos($declination);
               $declinationTopocentric = $declination - $mpar*$rho*$this->dsin($gclat)*$this->dsin($g-$declination)/$this->dsin($g);
          }
     }
     private function VSOP87term($term)
     {
          $s = 0;
          foreach ($term as $i => $a)
               $s += $a[0]*cos($a[1] + $a[2]*$this->j1000_1);
          return($s);
     }
     private function fixed360($x)
     {
          $x = $x/360 - (int)($x/360);
          if ($x < 0) 
               $x = ($x+1)*360;
          else
               $x = $x*360;
          return($x);     
     }        
     private function dsin($x)
     {
          return(sin($x*self::RAD));
     }
     private function dcos($x)
     {
          return(cos($x*self::RAD));
     }
     private function dtan($x)
     {
          return(tan($x*self::RAD));
     }
     private function dasin($x)
     {
          return(asin($x)/self::RAD);
     }
     private function dacos($x)
     {
          return(acos($x)/self::RAD);
     }
     private function datan($x)
     {
          return(atan($x)/self::RAD);
     }
     private function datan2($x, $y)
     {
          return(atan2($x, $y)/self::RAD);
     }
}
?>
