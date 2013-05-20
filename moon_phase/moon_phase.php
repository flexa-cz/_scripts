<?php
/**
 * @param integer $year
 * @param integer $month
 * @param integer $day
 * @original http://jivebay.com/calculating-the-moon-phase/
 */
class MoonPhase{
	private $year;
	private $month;
	private $day;
	private $current_date;

	const synmonth=29.5305882;

	public function __construct($year, $month, $day){
		$this->year=$year;
		$this->month=$month;
		$this->day=$day;
		$this->current_date=$year.'-'.$month.'-'.$day;
		$this->calculate();
	}

	private function calculate(){
		$begin_phase=$this->phase();
		$begin_time=$this->phaseToSeconds($begin_phase);

		$end_phase=$this->phase(true);
		$end_time=$this->phaseToSeconds($end_phase)-1;
	}

	private function phase($end=false){
		$year=$this->year;
		$month=$this->month;
		$day=$this->day;
		if($this->month < 3){
			$year=$this->year-1;
			$month=$this->month+12;
		}
		++$month;
		$c=365.25 * $year;
		$e=30.6 * $month;
		$jd=$c + $e + $day + ($end ? 1 : 0) - 694039.09; //jd is total days elapsed
		$jd /= self::synmonth; //divide by the moon cycle
		$b=(int) $jd; //int(jd) -> b, take integer part of jd
		$jd -= $b; //subtract integer part to leave fractional part of original jd
		$b=round($jd * 8); //scale fraction from 0-8 and round
		if($b >= 8){
			$b=0;//0 and 8 are the same so turn 8 into 0
		}
//		if($b==0)$return='New Moon';
//		elseif($b==1){$return='Waxing Crescent Moon';}
//		elseif($b==2){$return='Quarter Moon';}
//		elseif($b==3){$return='Waxing Gibbous Moon';}
//		elseif($b==4){$return='Full Moon';}
//		elseif($b==5){$return='Waning Gibbous Moon';}
//		elseif($b==6){$return='Last Quarter Moon';}
//		elseif($b==7){$return='Waning Crescent Moon';}
//		else{$return='Error';}
		return $jd;
	}

	private function phaseToSeconds($phase){
		return $this->phaseToDays($phase)*24*60*60;
	}

	private function phaseToDays($phase){
		return self::synmonth*$phase;

	}

	public function getBeginPhase(){
		return $this->begin_phase;
	}

	public function getEndPhase(){
		return $this->end_phase;
	}
}

for($d=1;$d<=30;$d++){
	$phase=new MoonPhase(2013, 1, $d);
	echo '2013-01-'.$d.': '.$phase->getBeginPhase().'<br>';

}
