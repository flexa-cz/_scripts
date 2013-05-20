<?php
/*
Adaptation en php du fameux et excellent scripte Astro-MoonPhase de Brett Hamilton écrit en Perl.
http://search.cpan.org/~brett/Astro-MoonPhase-0.60/

Ce Scripte vous permettra de connaître, à une date donnée, l'illumination de la Lune, son age,
sa distance en km par rapport à la Terre, son angle en degrés, sa distance par rapport au soleil,
et son angle par rapport au soleil.

*/
class Moon{
	protected static $Synmonth=29.53058868;
	protected static $ml;
	public static function phase($Year, $Month, $Day, $Hour, $Minutes, $Seconds){
//		$DateSec = mktime($Hour, $Minutes, $Seconds, $Month, $Day, $Year, 0);
		$DateSec = strtotime($Year.'-'.$Month.'-'.$Day.' '.$Hour.':'.$Minutes.':'.$Seconds);

		ini_set('precision', "20");	//Defini la precision des calcules

		# Astronomical constants.
		$Epoch					= 2444238.5;		# 1980 January 0.0

		# Constants defining the Sun's apparent orbit.
		$Elonge					= 278.833540;		# ecliptic longitude of the Sun at epoch 1980.0
		$Elongp					= 282.596403;		# ecliptic longitude of the Sun at perigee
		$Eccent					= 0.016718;			# eccentricity of Earth's orbit
		$Sunsmax				= 1.495985e8;		# semi-major axis of Earth's orbit, km
		$Sunangsiz				= 0.533128;			# sun's angular size, degrees, at semi-major axis distance

		# Elements of the Moon's orbit, epoch 1980.0.
		$Mmlong					= 64.975464;		# moon's mean longitude at the epoch
		$Mmlongp				= 349.383063;		# mean longitude of the perigee at the epoch
		$Mlnode					= 151.950429;		# mean longitude of the node at the epoch
		$Minc					= 5.145396;			# inclination of the Moon's orbit
		$Mecc					= 0.054900;			# eccentricity of the Moon's orbit
		$Mangsiz				= 0.5181;			# moon's angular size at distance a from Earth
		$Msmax					= 384401.0;			# semi-major axis of Moon's orbit in km
		$Mparallax				= 0.9507;			# parallax at distance a from Earth
		$Synmonth				=self::$Synmonth;		# synodic month (new Moon to new Moon)

		$pdate = self::jtime($DateSec);

//		$pphase;				# illuminated fraction
//		$mage;					# age of moon in days
//		$dist;					# distance in kilometres
//		$angdia;				# angular diameter in degrees
//		$sudist;				# distance to Sun
//		$suangdia;				# sun's angular diameter


		# Calculation of the Sun's position.

		$Day = $pdate - $Epoch;										# date within epoch
		$N = self::fixangle((360 / 365.2422) * $Day);				# mean anomaly of the Sun
		$M = self::fixangle($N + $Elonge - $Elongp);				# convert from perigee
										# co-ordinates to epoch 1980.0
		$Ec = self::kepler($M, $Eccent);							# solve equation of Kepler
		$Ec = sqrt((1 + $Eccent) / (1 - $Eccent)) * tan($Ec / 2);
		$Ec = 2 * self::todeg(atan($Ec));							# true anomaly
		$Lambdasun = self::fixangle($Ec + $Elongp);					# Sun's geocentric ecliptic
										# longitude
		# Orbital distance factor.
		$F = ((1 + $Eccent * cos(self::torad($Ec))) / (1 - $Eccent * $Eccent));
		$SunDist = $Sunsmax / $F;									# distance to Sun in km
		$SunAng = $F * $Sunangsiz;									# Sun's angular size in degrees


		# Calculation of the Moon's position.

		# Moon's mean longitude.
		self::$ml = $ml = self::fixangle(13.1763966 * $Day + $Mmlong);

		# Moon's mean anomaly.
		$MM = self::fixangle($ml - 0.1114041 * $Day - $Mmlongp);

		# Moon's ascending node mean longitude.
		$MN = self::fixangle($Mlnode - 0.0529539 * $Day);

		# Evection.
		$Ev = 1.2739 * sin(self::torad(2 * ($ml - $Lambdasun) - $MM));

		# Annual equation.
		$Ae = 0.1858 * sin(self::torad($M));

		# Correction term.
		$A3 = 0.37 * sin(self::torad($M));

		# Corrected anomaly.
		$MmP = $MM + $Ev - $Ae - $A3;

		# Correction for the equation of the centre.
		$mEc = 6.2886 * sin(self::torad($MmP));

		# Another correction term.
		$A4 = 0.214 * sin(self::torad(2 * $MmP));

		# Corrected longitude.
		$lP = $ml + $Ev + $mEc - $Ae + $A4;

		# Variation.
		$V = 0.6583 * sin(self::torad(2 * ($lP - $Lambdasun)));

		# True longitude.
		$lPP = $lP + $V;

		# Corrected longitude of the node.
		$NP = $MN - 0.16 * sin(self::torad($M));

		# Y inclination coordinate.
		$y = sin(self::torad($lPP - $NP)) * cos(self::torad($Minc));

		# X inclination coordinate.
		$x = cos(self::torad($lPP - $NP));

		# Ecliptic longitude.
		$Lambdamoon = self::todeg(atan2($y, $x));
		$Lambdamoon += $NP;

		# Ecliptic latitude.
		$BetaM = self::todeg(asin(sin(self::torad($lPP - $NP)) * sin(self::torad($Minc))));

		# Calculation of the phase of the Moon.

		# Age of the Moon in degrees.
		$MoonAge = $lPP - $Lambdasun;

		# Phase of the Moon.
		$MoonPhase = (1 - cos(self::torad($MoonAge))) / 2;

		# Calculate distance of moon from the centre of the Earth.

		$MoonDist = ($Msmax * (1 - $Mecc * $Mecc)) /
			(1 + $Mecc * cos(self::torad($MmP + $mEc)));

		# Calculate Moon's angular diameter.

		$MoonDFrac = $MoonDist / $Msmax;
		$MoonAng = $Mangsiz / $MoonDFrac;

		# Calculate Moon's parallax.

		$MoonPar = $Mparallax / $MoonDFrac;

		$pphase = $MoonPhase;									# illuminated fraction
		$mage = $Synmonth * (self::fixangle($MoonAge) / 360.0);	# age of moon in days
		$dist = $MoonDist;										# distance in kilometres
		$angdia = $MoonAng;										# angular diameter in degrees
		$sudist = $SunDist;										# distance to Sun
		$suangdia = $SunAng;									# sun's angular diameter
		$mpfrac = self::fixangle($MoonAge) / 360.0;
		return array( $pphase, $mage, $dist, $angdia, $sudist, $suangdia, $mpfrac, $mpfrac, 'moon_longitude'=>self::$ml );
	}

	/**
	 * fix angle
	 * @param integer $x
	 * @return integer
	 */
	protected static function fixangle($x){
		return ($x - 360.0 * (floor($x / 360.0)));
	}

	/**
	 * deg->rad
	 * @param integer $x
	 * @return integer
	 */
	protected static function torad($x){
		return ($x * (M_PI / 180.0));
	}

	/**
	 * rad->deg
	 * @param integer $x
	 * @return integer
	 */
	protected static function todeg($x){
		return ($x * (180.0 / M_PI));
	}

	/**
	 * return julian calendar
	 * @param string $t
	 * @return string
	 */
	protected static function jtime($t){
		$julian = ($t / 86400) + 2440587.5;	# (seconds /(seconds per day)) + julian date of epoch		2440587.5 / 86400 = 28,24753472222 Days
		return ($julian);
	}

	protected static function kepler($m, $ecc){
		$EPSILON = 1e-6;

		$m = self::torad($m);
		$e = $m;
		$delta=false;
		while (abs($delta) > $EPSILON)
			{
			$delta = $e - $ecc * sin($e) - $m;
			$e -= $delta / (1 - $ecc * cos($e));
			}
		return ($e);
	}
}


class MoonForDay extends Moon{
	protected static $curent_date;
	protected static $year;
	protected static $month;
	protected static $day;

	public static function phase($Year, $Month, $Day){
		self::$year=$Year;
		self::$month=$Month;
		self::$day=$Day;
		self::$curent_date=$Year.'-'.$Month.'-'.$Day;
		$begin_time=strtotime(self::$curent_date.' 00:00:00');
		$begin_moon=parent::phase($Year, $Month, $Day, 0, 0, 0);
		$begin_percent=round($begin_moon[0]*100,2);
		$phase = $begin_moon[0] < 0.5 ? 'Waxing' : 'Waning';
		$stage = $begin_moon[0] < 0.5 ? 'First' : 'Last';

		$end_time=strtotime(self::$curent_date.' 23:59:59');
		$end_moon=parent::phase($Year, $Month, $Day, 23, 59, 59);
		$end_percent=round($end_moon[0]*100,2);

//		var_dump($begin_moon);
//		var_dump($end_moon);

		if($begin_moon[0]>0.5 && $end_moon[0]<0.5){$moon_phase_for_day='New Moon';}
		elseif($begin_time<self::next_fulL_moon() && self::next_fulL_moon()<$end_time){$moon_phase_for_day='Full Moon';}
		elseif(($begin_percent<50 && $end_percent>50) || ($begin_percent>50 && $end_percent<50)){$moon_phase_for_day=$stage.' Quarter';}
		elseif($begin_percent<50){$moon_phase_for_day=$phase.' Crescent';}
		elseif($begin_percent>50){$moon_phase_for_day=$phase.' Gibbous';}
		else{$moon_phase_for_day='Error';}

		$return=$begin_moon;
		$return['curent_date']=self::$curent_date;
		$return['phase']=$moon_phase_for_day;
		$return['next_new_moon']=self::next_new_moon();
		$return['next_new_moon_date']=gmdate( 'Y-m-d H:i:s', $return['next_new_moon']);
		$return['next_full_moon']=self::next_fulL_moon();
		$return['next_full_moon_date']=gmdate( 'Y-m-d H:i:s', $return['next_full_moon']);
		$return['begin_moon_longitude']=$begin_moon['moon_longitude'];
		$return['end_moon_longitude']=$end_moon['moon_longitude'];
		return $return;
	}

	private static function next_fulL_moon(){
		$half_synmonth=(self::$Synmonth*24*60*60)/2;
		$today=strtotime(self::$curent_date.' 00:00:00');
		if($today<$next_full_moon=round(self::next_new_moon()-$half_synmonth,4)){
			return $next_full_moon;
		}
		return round(self::next_new_moon()+$half_synmonth,4);
	}

	private static function next_new_moon(){
		$actual_moon=parent::phase(self::$year, self::$month, self::$day, 0, 0, 0);
		$actual_time=strtotime(self::$curent_date.' 00:00:00');
		$days_to_new_moon=self::$Synmonth-$actual_moon[1];
		$sec_to_new_moon=self::days_to_sec($days_to_new_moon);
		$new_new_moon=$actual_time+$sec_to_new_moon;
		return $new_new_moon;
	}

	private static function days_to_sec($days){
		return $days*24*60*60;
	}
}
//Exemple d'utilisation :

//
//// create an instance of the class, and use the current time
//for($d=1; $d<=30; $d++){
////	$date=strtotime('2010-03-'.$d.' 12:00:00');
//	$phase=MoonForDay::phase(2013, 01, $d);
//	echo '<h3>'.$phase['curent_date'].'</h3>';
//	echo '<p>';
//	echo '<br /><b>dalsi nov:</b> '.$phase['next_new_moon_date'];
//	echo '<br /><b>dalsi uplnek:</b> '.$phase['next_full_moon_date'];
//	echo '<br /><b>faze mesice pro den:</b> '.(strpos($phase['phase'],'Moon') ? '<strong style="color:red">'.$phase['phase'].'</strong>' : $phase['phase']);
//	echo '</p>';
//	echo '<hr/>';
//}


//Pour le 11 Avril 2009 à 00h00
//$phase_for_day=MoonForDay::phase(2013, 01, 27);
//var_dump($phase_for_day);
//list($MoonPhase, $MoonAge, $MoonDist, $MoonAng, $SunDist, $SunAng, $mpfrac) = $phase_for_day;
//echo "La Lune est éclairée à ".number_format($MoonPhase*100, 2, ',', '')."%"."<br>";
//echo "Son age est de ".number_format($MoonAge, 0, ',', '')." jours"."<br>";
//echo "Et elle se situe à une distance de ".number_format($MoonDist, 0, ',', '')." km par rapport à la Terre."."<br>";
//echo 'next full moon: '.gmdate( 'G:i:s, j M Y', $phase_for_day['next_full_moon']).'<br>';
//echo 'next new moon: '.gmdate( 'G:i:s, j M Y', $phase_for_day['next_new_moon']).'<br>';
?>