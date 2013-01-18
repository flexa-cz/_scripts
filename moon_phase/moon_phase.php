<?php
/**
 * @param integer $year
 * @param integer $month
 * @param integer $day
 * @original http://jivebay.com/calculating-the-moon-phase/
 */
function moon_phase($year, $month, $day){
	$return=false;
	$c=$e=$jd=$b=0;
	if($month < 3){
		$year--;
		$month += 12;
	}
	++$month;
	$c=365.25 * $year;
	$e=30.6 * $month;
	$jd=$c + $e + $day - 694039.09; //jd is total days elapsed
	$jd /= 29.5305882; //divide by the moon cycle
	$b=(int) $jd; //int(jd) -> b, take integer part of jd
	$jd -= $b; //subtract integer part to leave fractional part of original jd
	$b=round($jd * 8); //scale fraction from 0-8 and round
	if($b >= 8){
		$b=0;//0 and 8 are the same so turn 8 into 0
	}
	if($b==0)$return='New Moon';
	elseif($b==1){$return='Waxing Crescent Moon';}
	elseif($b==2){$return='Quarter Moon';}
	elseif($b==3){$return='Waxing Gibbous Moon';}
	elseif($b==4){$return='Full Moon';}
	elseif($b==5){$return='Waning Gibbous Moon';}
	elseif($b==6){$return='Last Quarter Moon';}
	elseif($b==7){$return='Waning Crescent Moon';}
	else{$return='Error';}
	return $return;
}


$timestamp=time();
for($d=1;$d<=30;$d++){
	echo '2013-01-'.$d.': '.moon_phase(2013, 1, $d).'<br>';

}
