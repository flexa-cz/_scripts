<?php
$test=3;


if($test==4){
	include('./moon_phase_2.php');
	// create an instance of the class, and use the current time
	for($d=1; $d<=30; $d++){
	//	$date=strtotime('2010-03-'.$d.' 12:00:00');
		$phase=MoonForDay::phase(2013, 01, $d);
		echo '<h3>'.$phase['curent_date'].'</h3>';
		echo '<p>';
		echo '<br /><b>dalsi nov:</b> '.$phase['next_new_moon_date'];
		echo '<br /><b>dalsi uplnek:</b> '.$phase['next_full_moon_date'];
		echo '<br /><b>faze mesice pro den:</b> '.(strpos($phase['phase'],'Moon') ? '<strong style="color:red">'.$phase['phase'].'</strong>' : $phase['phase']);
		echo '<br >begin moon longitude: '.$phase['begin_moon_longitude'];
		echo '<br >end moon longitude: '.$phase['end_moon_longitude'];
		echo '</p>';
		echo '<hr/>';
	}
}
elseif($test==3){
	$time=strtotime('2013-06-02 23:59:59');
	include('./Astronomy.php');
	$astronomy=new Science_Astronomy();
	$phase=$astronomy->calculateMoonPhase($time);
	echo date('d.m.Y, H:i:s',$time);
	var_dump($phase);
}
elseif($test==2){
	include('./moon_ecliptic.php');
	include('./moon_phase_3.php');
	$moon_ecliptic=new MoonEcliptic(date('Y-m-d H:i:s'));
	var_dump($moon_ecliptic);
	var_dump($moon_ecliptic->get_moon_distance());

	$phase=new MoonPhaseForDay(time());
	var_dump($phase);
}
elseif($test==1){
	include('./moon_phase_3.php');

	// create an instance of the class, and use the current time
	for($m=1; $m<=12; $m++){
		echo '<h1>'.$m.'</h1>';
		for($d=1; $d<=30; $d++){
		//	$date=strtotime('2010-03-'.$d.' 12:00:00');
			$date=strtotime('2013-'.str_pad($m,2,'0',STR_PAD_LEFT).'-'.$d.' 00:00:00');
			$phase=new MoonPhaseForDay($date);
			echo '<div style="width: 140px; height: 140px; float: left; background: #eee; margin: 5px; padding: 10px; border: 1px solid #aaa; font-size: 80%">';
			echo '<h3 style="margin: 0;">'.$phase->curent_date.'</h3>';
			echo '<p style="margin: 0;">';
			echo '<br /><b>dalsi nov:</b><br>'.gmdate( 'Y-m-d H:i:s', $phase->next_new_moon());
			echo '<br /><b>dalsi uplnek:</b><br>'.gmdate( 'Y-m-d H:i:s', $phase->next_full_moon());
			echo '<br /><b>faze mesice pro den:</b><br>'.(strpos($phase->moon_phase_for_day(),'Moon') ? '<strong style="color:red">'.$phase->moon_phase_for_day().'</strong>' : $phase->moon_phase_for_day());
			echo '</p>';
			echo '</div>';
		}
		echo '<hr style="clear:both; margin: 20px;">';
	}

	date_default_timezone_set('Europe/Prague');
	include('moon.php');
	$moon_rise=Moon::calculateMoonTimes(2, 3, 2013, 50.087811400000000000, -14.420459800000003000);
	echo gmdate('d.m.Y, H:i:s',$moon_rise->moonrise);
	echo '<br>';
	echo gmdate('d.m.Y, H:i:s',$moon_rise->moonset);
}