<?php
/**
 * Výpočet ekliptikálních souřadnic Měsíce
 * @since 7.2.13 11:14
 * @author Milan Vlahovic <vlahovic@propeople.cz>
 * @source http://kalendar.beda.cz/vypocet-ekliptikalnich-souradnic-mesice
 * @source http://mx.ujf.cas.cz/hor/calc/
 * @source http://www.vaisnava.cz/clanek.php3?no=162#ro
 */
class MoonEcliptic{
	public $date;
	public $date_sec;
	private $year;
	private $month;
	private $day;
	private $hour;
	private $minute;
	private $second;

	private $earth_radius=6378;
	/**
	 * svetove datum a cas (UT) prevedene na zlomky dni od 31.12.1999
	 * @var integer
	 */
	private $d;
	/**
	 * delka vystupniho uzlu
	 * @var integer
	 */
	private $z;
	/**
	 * sklon drahy k ekliptice
	 * @var integer
	 */
	private $i=5.1454;
	/**
	 * argument perihelu Měsíce
	 * @var integer
	 */
	private $wm;
	/**
	 * argument perihelu Země
	 * @var integer
	 */
	private $wz;
	/**
	 * velká poloosa dráhy (v poloměrech Země)
	 * @var integer
	 */
	private $a=60.2666;
	/**
	 * číselná excentricita dráhy Měsíce
	 * @var integer
	 */
	private $e=0.0549;
	/**
	 * střední anomálie Měsíce
	 * @var integer
	 */
	private $M;
	/**
	 * excentrická anomálie
	 * @var integer
	 */
	private $E;
	/**
	 * geocentrická pravoúhlá souřadnice x
	 * @var integer
	 */
	private $x;
	/**
	 * geocentrická pravoúhlá souřadnice y
	 * @var integer
	 */
	private $y;
	/**
	 * pravá anomálie
	 * @var integeer
	 */
	private $v;
	/**
	 * předběžná vzdálenost v zemských poloměrech
	 * @var integer
	 */
	private $r0;
	/**
	 * ekliptikální pravoúhlá souřadnice
	 * @var integer
	 */
	private $xe;
	/**
	 * ekliptikální pravoúhlá souřadnice
	 * @var integer
	 */
	private $ye;
	/**
	 * ekliptikální pravoúhlá souřadnice
	 * @var integer
	 */
	private $ze;
	/**
	 * předběžná ekliptikální délka Měsíce
	 * @var integer
	 */
	private $L0;
	/**
	 * předběžná ekliptikální šířka Měsíce
	 * @var integer
	 */
	private $B0;
	/**
	 * střední anomálie Slunce
	 * @var integer
	 */
	private $Ms;
	/**
	 * střední anomálie Měsíce
	 * @var integer
	 */
	private $Mm;
	/**
	 * střední délka Slunce
	 * @var integer
	 */
	private $Ls;
	/**
	 * střední délka Měsíce
	 * @var integer
	 */
	private $Lm;
	/**
	 * střední elongace Měsíce
	 * @var integer
	 */
	private $D;
	/**
	 * argument šířky Měsíce
	 * @var integer
	 */
	private $F;
	/**
	 * oprava pro délku Měsíce
	 * @var integer
	 */
	private $Lp;
	/**
	 * oprava pro šířku Měsíce
	 * @var integer
	 */
	private $Bp;
	/**
	 * oprava pro vzdálenost Měsíce
	 * @var integer
	 */
	private $rp;
	/**
	 * vzdálenost Měsíce
	 * @var integer
	 */
	private $r;
	/**
	 * ekliptikální šířka Měsíce
	 * @var integer
	 */
	private $B;
	/**
	 * ekliptikální délka Měsíce
	 * @var integer
	 */
	private $L;

	/**
	 *
	 * @param string $date datum ve formatu yyyy-mm-dd hh:ii:ss
	 */
	public function __construct($date){
		$this->date=$date;
		$this->date_sec=strtotime($this->date);// rozdil naseho casu oproti UT
		$this->year=gmdate('Y',$this->date_sec);
		$this->month=gmdate('m',$this->date_sec);
		$this->day=gmdate('d',$this->date_sec);
		$this->hour=gmdate('H',$this->date_sec);
		$this->minute=gmdate('i',$this->date_sec);
		$this->second=gmdate('s',$this->date_sec);
		$this
						->setD()
						->setZ()
						->setWm()
						->setWz()
						->setM()
						->setMm()
						->setE()
						->setX()
						->setY()
						->setV()
						->setR0()
						->setXe()
						->setYe()
						->setZe()
						->setL0()
						->setB0()
						->setMs()
						->setLs()
						->setLm()
						->setDm()
						->setR()
						->setF()
						->setLp()
						->setBp()
						->setRp()
						->setB()
						->setL()
						;
		return $this;
	}

	private function setD(){
		$this->d=round(($this->date_sec - strtotime('1999-12-31 01:00:00'))/(60*60*24),6);
//		$this->d=round(($this->date_sec - strtotime('2000-01-01 01:00:00'))/(60*60*24),6);
		return $this;
	}

	private function setZ(){
		$this->z = 125.1228 - 0.0529538083 * $this->d;
		$this->z = 0.347343 - 0.00014709391*$this->d;
		return $this;
	}

	private function setWm(){
		$this->wm=318.0634 + 0.1643573223 * $this->d;
		return $this;
	}

	private function setWz(){
		$this->wz = 102.9404 + 0.0000470935 * $this->d;
		return $this;
	}

	private function setM(){
		$this->M = 115.3654 + 13.0649929509 * $this->d;
//		$this->M = 134.963 + 13.0649929509 * $this->d;
//		$this->M = 0.374897 + 0.03629164709 * $this->d;
//		$this->M = 24.518163;
		return $this;
	}

	private function setE(){
		//double e, delta;
		$epsilon = pow(1, -6);
		$e = $m = deg2rad($this->M);
		do {
			$delta = $e - $this->e * sin($e) - $m;
			$e -= $delta / ( 1 - $this->e * cos($e) );
		}
		while ( abs($delta) > $epsilon );
		$this->E=$e;
		return $this;
	}

	private function setX(){
		$this->x = $this->a * (cos($this->E) - $this->e);
		return $this;
	}

	private function setY(){
		$this->y = $this->a * sin($this->E) * sqrt((1 - $this->e * $this->e));
		return $this;
	}

	private function setV(){
		$this->v = atan2($this->y, $this->x);
		return $this;
	}

	private function setR(){
		$this->r = $this->r0 + $this->rp;
		return $this;
	}

	private function setR0(){
		$this->r0 = sqrt($this->x * $this->x + $this->y * $this->y);
		return $this;
	}

	private function setXe(){
		$this->xe = $this->r0 * (cos($this->z) * cos($this->v + $this->wm) - sin($this->z) * sin($this->v + $this->wm) * cos($this->i));
		return $this;
	}

	private function setYe(){
		$this->ye = $this->r0 * (sin($this->z) * cos($this->v + $this->wm) + cos($this->z) * sin($this->v + $this->wm) * cos($this->i));
		return $this;
	}

	private function setZe(){
		$this->ze = $this->r0 * (sin($this->v + $this->wm) * sin($this->i));
		return $this;
	}

	private function setL0(){
		$this->L0 = atan2($this->ye, $this->xe);
//		$this->L0=	283.723809;
		return $this;
	}

	private function setB0(){
		$this->B0 = atan2($this->ze, sqrt($this->xe * $this->xe + $this->ye * $this->ye));
		return $this;
	}

	private function setMs(){
		$this->Ms = 34.546638;
//		$this->Ms	=	357.529 + 0.98560028 * $this->d;
//		$this->Ms = 0.993126 + 0.0027377785* $this->d;
		return $this;
	}


	private function setMm(){
		$this->Mm = $this->M;
		return $this;
	}

	private function setLs(){
		$this->Ls = $this->wz + $this->Ms + 180;
//		$this->Ls = 0.779072 + 0.00273790931 * $this->d;
		return $this;
	}

	private function setLm(){
		$this->Lm = $this->M + $this->wm + $this->z;
//		$this->Lm = 281.052941;
//		$this->Lm = $this->wm + $this->Mm;
//		$this->Lm	=	218.316 + 13.17639648 * $this->d;
//		 $this->Lm = 0.606434 + 0.03660110129 * $this->d;
		return $this;
	}

	private function setDm(){
		$this->D = $this->Lm - $this->Ls;
		return $this;
	}

	private function setF(){
		$this->F = $this->Lm - $this->z;
		return $this;
	}

	private function setLp(){
		$this->Lp = -1.274 * sin($this->Mm - 2 * $this->D)// (evekce)
     +0.658 * sin(2 * $this->D)// (variace)
     -0.186 * sin($this->Ms)// (roční nerovnost)
     -0.059 * sin(2 * $this->Mm - 2 * $this->D)
     -0.057 * sin($this->Mm - 2 * $this->D + $this->Ms)
     +0.053 * sin($this->Mm + 2 * $this->D)
     +0.046 * sin(2 * $this->D - $this->Ms)
     +0.041 * sin($this->Mm - $this->Ms)
     -0.035 * sin($this->D)
     -0.031 * sin($this->Mm + $this->Ms)
     -0.015 * sin(2 * $this->F - 2 * $this->D)
     +0.011 * sin($this->Mm - 4 * $this->D);
		return $this;
	}

	private function setBp(){
		$this->Bp = -0.173 * sin($this->F - 2 * $this->D)
     -0.055 * sin($this->Mm - $this->F - 2 * $this->D)
     -0.046 * sin($this->Mm + $this->F - 2 * $this->D)
     +0.033 * sin($this->F + 2 * $this->D)
     +0.017 * sin(2 * $this->Mm + $this->F);
		return $this;
	}

	private function setRp(){
		$this->rp = -0.58 * cos($this->Mm - 2 * $this->D)
     -0.46 * cos(2 * $this->D);
		return $this;
	}

	private function setB(){
		$this->B = $this->B0 + $this->Bp;
		return $this;
	}

	private function setL(){
		$this->L = $this->L0 + $this->Lp;
		return $this;
	}

	/**
	 * moon distance at the moment (km)
	 * @return integer
	 */
	public function get_moon_distance(){
		return $this->r * $this->earth_radius;
	}
}