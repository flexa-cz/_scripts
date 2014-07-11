<?php
/**
 * *****************************************************************************
 * tohle muzes editovat - respektive jen ceny...
 * *****************************************************************************
 */
$actual_prices=array(
		'35_mm'=>array(
				'1400'=>array(
						'100'=>7,
						'500'=>6.5,
						'infinity'=>5.5,
				),
				'2700'=>array(
						'100'=>9.5,
						'500'=>9,
						'infinity'=>8.5,
				),
				'4000'=>array(
						'100'=>12,
						'500'=>11,
						'infinity'=>10,
				),
		),
		'roll'=>array(
				'1400'=>array(
						'6x4.5'=>17,
						'6x6'=>19,
						'6x7'=>20,
						'6x8'=>22,
						'6x9'=>25,
				),
				'2700'=>array(
						'6x4.5'=>27,
						'6x6'=>32,
						'6x7'=>34,
						'6x8'=>36,
						'6x9'=>40,
				),
				'4000'=>array(
						'6x4.5'=>35,
						'6x6'=>40,
						'6x7'=>45,
						'6x8'=>50,
						'6x9'=>55,
				),
		),
		'glass_frame'=>1,
		'48_bit_tiff'=>2,
		'dvd'=>25,
		'archive_dvd'=>150,
		'transport_2_post'=>120,
		'transport_2_manual'=>0,
		'payment_cash'=>0,
		'payment_on_delivery'=>29,
		'payment_transfer'=>0,
);
/**
 * *****************************************************************************
 * dal uz nic nemen!
 * *****************************************************************************
 */
new ScanitForm($actual_prices);

class ScanitForm{
	private $allowed_domains=array('localhost','flexa.cz','scanit.cz',);
	private $is_allowed_domain=false;
	private $actual_prices;
	private $output;
	private $recipients=array('Radek Myšulka <radek@mgrafika.cz>');
//	private $recipients=array('Milan Vlahovič <flexa@flexa.cz>');
	private $senders=array();
	private $header;
	private $message=false;
	private $subject='Objednávka scanování';
	private $integer_inputs=array('35_mm_pieces','roll_pieces','glass_frame_pieces','48_bit_tiff_pieces','dvd_pieces','archive_dvd_pieces');
	private $even_color='eee';
	private $odd_color='ddd';
	private $sum_price=0;

	public function __construct($actual_prices){
		$this
						->setActualPrices($actual_prices)
						->setIsAllowedDomain()
						->doWork()
						->output();
	}

	private function doWork(){
		if(!empty($_POST['get_actual_prices'])){
			$this->output=$this->actual_prices;
		}
		elseif(!empty($_POST)){
			if($this->validateData() && $this->composeAndSendMail()){
				$this->output=array(
						'error'=>false,
						'error_num'=>0,
						'status'=>'succ',
						'report'=>'Objednávka byla odeslána.'
				);
			}
		}
		return $this;
	}

	private function output(){
		$output=false;
		if($this->is_allowed_domain){
			$output=$this->output;
		}
		else{
			$output=array('error'=>'query from unallowed domain');
		}
		echo json_encode($output);
		return $this;
	}

	private function validateData(){
		$return=true;
		$this->output=array('error'=>false,'error_num'=>false,'status'=>false,'report'=>false,'input_name'=>array());
		foreach($this->integer_inputs as $input_name){
			if(!isset($_POST[$input_name])){
				$this->output['error']=true;
				$this->output['error_num']=2;
				$this->output['status']='error';
				$this->output['report']='Vnitřní chyba objednávkové aplikace.';
				$return=false;
				break;
			}
			elseif($_POST[$input_name] && !intval($_POST[$input_name])){
				$this->output['error']=true;
				$this->output['error_num']=1;
				$this->output['status']='alert';
				$this->output['report']='Zadané hodnoty musí být celá čísla.';
				$this->output['input_name'][]=$input_name;
				$return=false;
			}
			else{
			}
		}
		return $return;
	}

	private function setActualPrices($actual_prices){
		$this->actual_prices=$actual_prices;
		return $this;
	}

	private function setIsAllowedDomain(){
		if(in_array($_SERVER['HTTP_HOST'], $this->allowed_domains)){
			$this->is_allowed_domain=true;
		}
		return $this;
	}

	private function composeAndSendMail(){
		if(!isset($this->output['error']) || $this->output['error']===false){
			return $this
							->composeMail()
							->sendMail()
							;
			return true;
		}
		return false;
	}

	private function composeMail(){
		$this->senders[]=$_POST['name'].' '.$_POST['surname'].'<'.$_POST['email'].'>';
		$this->message='
			<div style="font-family: Helvetica, Arial, sans-serif; font-size: 9pt;">
				<h1 style="color: #882346; font-weight: normal;">Objednávka skenování</h1>
				<h2 style="color: #882346; font-weight: normal;">Negativy a diapozitivy</h2>
				<table style="border-collapse: collapse; border: none; width: 750px;">
					<tr>
						<td style="padding: .5em 1em; background: #'.$this->getEvenOddColor().';" colspan="2">35 mm film</td>
						<td style="text-align: right; padding: .5em 1em; background: #'.$this->getEvenOddColor().';">'.$_POST['35_mm_dpi'].' dpi</td>
						<td style="text-align: right; padding: .5em 1em; background: #'.$this->getEvenOddColor().';">'.$_POST['35_mm_pieces'].' ks</td>
						<td style="text-align: right; padding: .5em 1em; background: #'.$this->getEvenOddColor().';">'.$this->getPrice('35_mm',true).' Kč/ks</td>
						<td style="text-align: right; padding: .5em 1em; background: #'.$this->getEvenOddColor().';">'.$this->getPrice('35_mm').' Kč</td>
					</tr>
					<tr>
						<td style="padding: .5em 1em; background: #'.$this->getEvenOddColor(true).';">svitkový film</td>
						<td style="text-align: right; padding: .5em 1em; background: #'.$this->getEvenOddColor().';">'.$_POST['size'].' cm</td>
						<td style="text-align: right; padding: .5em 1em; background: #'.$this->getEvenOddColor().';">'.$_POST['roll_dpi'].' dpi</td>
						<td style="text-align: right; padding: .5em 1em; background: #'.$this->getEvenOddColor().';">'.$_POST['roll_pieces'].' ks</td>
						<td style="text-align: right; padding: .5em 1em; background: #'.$this->getEvenOddColor().';">'.$this->getPrice('roll',true).' Kč/ks</td>
						<td style="text-align: right; padding: .5em 1em; background: #'.$this->getEvenOddColor().';">'.$this->getPrice('roll').' Kč</td>
					</tr>
				</table>
				<h2 style="color: #882346; font-weight: normal;">Volitelné služby</h2>
				<table style="border-collapse: collapse; border: none; width: 750px;">';
		$arr=array(
				'glass_frame'=>'Vyjmutí filmu ze skleněného rámečku',
				'48_bit_tiff'=>'Skenování v barevné hloubce 48 bitů a uložení do TIFF',
				'dvd'=>'Vytvoření další kopie DVD',
				'archive_dvd'=>'Uložení na archivační DVD s životností 160 let',
				);
		foreach($arr as $input => $text){
					$this->message.='<tr>
						<td style="padding: .5em 1em; background: #'.$this->getEvenOddColor(true).';" colspan="3">'.$text.'</td>
						<td style="text-align: right; padding: .5em 1em; background: #'.$this->getEvenOddColor().';">'.$_POST[$input.'_pieces'].' ks</td>
						<td style="text-align: right; padding: .5em 1em; background: #'.$this->getEvenOddColor().';">'.$this->getPrice($input,true).' Kč/ks</td>
						<td style="text-align: right; padding: .5em 1em; background: #'.$this->getEvenOddColor().';">'.$this->getPrice($input).' Kč</td>
					</tr>';
		}
				$this->message.='</table>
				<h2 style="color: #882346; font-weight: normal;">Jak doručíte vaši zakázku k nám?</h2>
				<table style="border-collapse: collapse; border: none; width: 750px;">
					<tr>
						<td style="padding: .5em 1em; background: #'.$this->getEvenOddColor(true).';">'.($_POST['transport_1']==='post' ? 'Poštou' : 'Osobní předání na provozovně Brno').'</td>
					</tr>
				</table>
				<h2 style="color: #882346; font-weight: normal;">Jak si přejete převzít vaši hotovou zakázku?</h2>
				<table style="border-collapse: collapse; border: none; width: 750px;">
					<tr>
						<td style="padding: .5em 1em; background: #'.$this->getEvenOddColor(true).';">'.($_POST['transport_2']==='post' ? 'Poštou' : 'Osobní předání na provozovně Brno').'</td>
						<td style="text-align: right; padding: .5em 1em; background: #'.$this->getEvenOddColor().';">'.$this->getPrice('transport_2_'.$_POST['transport_2']).' Kč</td>
					</tr>
				</table>
				<h2 style="color: #882346; font-weight: normal;">Způsob platby</h2>
				<table style="border-collapse: collapse; border: none; width: 750px;">
					<tr>
						<td style="padding: .5em 1em; background: #'.$this->getEvenOddColor(true).';">'.($_POST['payment']==='cash' ? 'Platba při převzetí na provozovně' : ($_POST['payment']==='on_delivery' ? 'Hotově dobírkou' : 'Převodem a účet')).'</td>
						<td style="text-align: right; padding: .5em 1em; background: #'.$this->getEvenOddColor().';">'.$this->getPrice('payment_'.$_POST['payment']).' Kč</td>
					</tr>
				</table>
				<h2 style="color: #882346; font-weight: normal;">Objednávka celkem</h2>
				<table style="border-collapse: collapse; border: none; width: 750px;">
					<tr>
						<td style="text-align: right; padding: .5em 1em; background: #'.$this->getEvenOddColor(true).';"><b>'.$this->sum_price.' Kč</b></td>
					</tr>
				</table>
				<h2 style="color: #882346; font-weight: normal;">Kontaktní údaje</h2>
				<table style="border-collapse: collapse; border: none; width: 750px;">
					<tr>
						<td style="padding: .5em 1em; background: #'.$this->getEvenOddColor(true).';">Jméno</td>
						<td style="padding: .5em 1em; background: #'.$this->getEvenOddColor().';">'.$_POST['name'].'</td>
					</tr>
					<tr>
						<td style="padding: .5em 1em; background: #'.$this->getEvenOddColor(true).';">Příjmení</td>
						<td style="padding: .5em 1em; background: #'.$this->getEvenOddColor().';">'.$_POST['surname'].'</td>
					</tr>
					<tr>
						<td style="padding: .5em 1em; background: #'.$this->getEvenOddColor(true).';">E-mail</td>
						<td style="padding: .5em 1em; background: #'.$this->getEvenOddColor().';">'.$_POST['email'].'</td>
					</tr>
					<tr>
						<td style="padding: .5em 1em; background: #'.$this->getEvenOddColor(true).';">Telefon</td>
						<td style="padding: .5em 1em; background: #'.$this->getEvenOddColor().';">'.$_POST['phone'].'</td>
					</tr>
				</table>
				<h2 style="color: #882346; font-weight: normal;">Fakturační údaje</h2>
				<table style="border-collapse: collapse; border: none; width: 750px;">
					<tr>
						<td style="padding: .5em 1em; background: #'.$this->getEvenOddColor(true).';" colspan="2">'.($_POST['person_firm']==='person' ? 'Fyzická osoba' : 'Firma').'</td>
					</tr>
					<tr>
						<td style="padding: .5em 1em; background: #'.$this->getEvenOddColor(true).';">Jméno</td>
						<td style="padding: .5em 1em; background: #'.$this->getEvenOddColor().';">'.$_POST['invoice']['name'].'</td>
					</tr>
					<tr>
						<td style="padding: .5em 1em; background: #'.$this->getEvenOddColor(true).';">Příjmení</td>
						<td style="padding: .5em 1em; background: #'.$this->getEvenOddColor().';">'.$_POST['invoice']['surname'].'</td>
					</tr>
					<tr>
						<td style="padding: .5em 1em; background: #'.$this->getEvenOddColor(true).';">Ulice</td>
						<td style="padding: .5em 1em; background: #'.$this->getEvenOddColor().';">'.$_POST['invoice']['street'].'</td>
					</tr>
					<tr>
						<td style="padding: .5em 1em; background: #'.$this->getEvenOddColor(true).';">Město</td>
						<td style="padding: .5em 1em; background: #'.$this->getEvenOddColor().';">'.$_POST['invoice']['city'].'</td>
					</tr>
					<tr>
						<td style="padding: .5em 1em; background: #'.$this->getEvenOddColor(true).';">PSČ</td>
						<td style="padding: .5em 1em; background: #'.$this->getEvenOddColor().';">'.$_POST['invoice']['postal_code'].'</td>
					</tr>
				</table>
				<h2 style="color: #882346; font-weight: normal;">Doručovací adresa</h2>
				<table style="border-collapse: collapse; border: none; width: 750px;">
					<tr>
						<td style="padding: .5em 1em; background: #'.$this->getEvenOddColor(true).';">Jméno</td>
						<td style="padding: .5em 1em; background: #'.$this->getEvenOddColor().';">'.$_POST['address']['name'].'</td>
					</tr>
					<tr>
						<td style="padding: .5em 1em; background: #'.$this->getEvenOddColor(true).';">Příjmení</td>
						<td style="padding: .5em 1em; background: #'.$this->getEvenOddColor().';">'.$_POST['address']['surname'].'</td>
					</tr>
					<tr>
						<td style="padding: .5em 1em; background: #'.$this->getEvenOddColor(true).';">Ulice</td>
						<td style="padding: .5em 1em; background: #'.$this->getEvenOddColor().';">'.$_POST['address']['street'].'</td>
					</tr>
					<tr>
						<td style="padding: .5em 1em; background: #'.$this->getEvenOddColor(true).';">Město</td>
						<td style="padding: .5em 1em; background: #'.$this->getEvenOddColor().';">'.$_POST['address']['city'].'</td>
					</tr>
					<tr>
						<td style="padding: .5em 1em; background: #'.$this->getEvenOddColor(true).';">PSČ</td>
						<td style="padding: .5em 1em; background: #'.$this->getEvenOddColor().';">'.$_POST['address']['postal_code'].'</td>
					</tr>
				</table>
				<h2 style="color: #882346; font-weight: normal;">Doplňující údaje k objednávce</h2>
				<p>'.nl2br($_POST['supplement']).'</p>
			</div>
			';
		return $this;
	}

	private function getEvenOddColor($new=false){
		return ($this->getEvenOdd($new)==='odd' ? $this->odd_color : $this->even_color);
	}

	private function getEvenOdd($new){
		static $even_odd=1;
		$even_odd=($new ? ($even_odd===1 ? 2 : 1) : $even_odd);
		$return='even';
		if($even_odd===2){
			$return='odd';
		}
		return $return;
	}

	private function getPrice($input_name, $return_price_per_piece=false){
		$price=0;
		$arr=array(
				'glass_frame'=>'Vyjmutí filmu ze skleněného rámečku',
				'48_bit_tiff'=>'Skenování v barevné hloubce 48 bitů a uložení do TIFF',
				'dvd'=>'Vytvoření další kopie DVD',
				'archive_dvd'=>'Uložení na archivační DVD s životností 160 let',
				);
		if($input_name==='35_mm'){
			$pieces=((int)$_POST['35_mm_pieces']<=100 ? '100' : ((int)$_POST['35_mm_pieces']<=500 ? '500' : 'infinity'));
			$price_per_piece=$this->actual_prices['35_mm'][$_POST['35_mm_dpi']][$pieces];
			$price=(int)$_POST['35_mm_pieces']*$price_per_piece;
		}
		elseif($input_name==='roll'){
			$price_per_piece=$this->actual_prices['roll'][$_POST['roll_dpi']][$_POST['size']];
			$price=(int)$_POST['roll_pieces']*$price_per_piece;
		}
		elseif(isset($arr[$input_name])){
			$price_per_piece=$this->actual_prices[$input_name];
			$price=(int)$_POST[$input_name.'_pieces']*$price_per_piece;
		}
		else{
			$price_per_piece=$this->actual_prices[$input_name];
			$price=$price_per_piece;
		}

		if(!$return_price_per_piece){
			$this->sum_price+=$price;
		}

		return ($return_price_per_piece ? $price_per_piece : $price);
	}

	/**
	 * @return boolean
	 */
	private function sendMail(){
		$header = 'MIME-Version: 1.0'
						. "\r\nContent-type: text/html; charset=UTF-8\r\n"
						."To: ".implode(',', $this->recipients)."\r\n"
						."From: ".implode(',', $this->senders)."\r\n"
						.$this->header;
		return @mail(implode(',',$this->recipients), '=?UTF-8?B?'.base64_encode($this->subject).'?=', $this->message, $header);
	}
}