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
	private $recipients=array('Milan Vlahovič <flexa@flexa.cz>');
	private $senders=array();
	private $header;
	private $message=false;
	private $subject='Objednávka scanování';
	private $integer_inputs=array('35_mm_pieces','roll_pieces','glass_frame_pieces','48_bit_tiff_pieces','dvd_pieces','archive_dvd_pieces');

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
//							->sendMail()
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
				<table style="border-collapse: collapse; border: none;">
					<tr>
						<td style="padding: .5em 1em; background: #ddd;" colspan="2">35 mm film</td>
						<td style="padding: .5em 1em; background: #ddd;">'.$_POST['35_mm_dpi'].' dpi</td>
						<td style="padding: .5em 1em; background: #ddd;">'.$_POST['35_mm_pieces'].' ks</td>
						<td style="padding: .5em 1em; background: #ddd;">'.$this->getPrice('35_mm',true).' Kč/ks</td>
						<td style="padding: .5em 1em; background: #ddd;">'.$this->getPrice('35_mm').' Kč</td>
					</tr>
					<tr>
						<td style="padding: .5em 1em; background: #eee;">svitkový film</td>
						<td style="padding: .5em 1em; background: #eee;">'.$_POST['size'].' cm</td>
						<td style="padding: .5em 1em; background: #eee;">'.$_POST['roll_dpi'].' dpi</td>
						<td style="padding: .5em 1em; background: #eee;">'.$_POST['roll_pieces'].' ks</td>
						<td style="padding: .5em 1em; background: #eee;">'.$this->getPrice('roll',true).' Kč/ks</td>
						<td style="padding: .5em 1em; background: #eee;">'.$this->getPrice('roll').' Kč</td>
					</tr>
				</table>
			</div>
			';
		echo $this->message;
		return $this;
	}

	private function getPrice($input_name, $return_price_per_piece=false){
		$price=0;
		if($input_name==='35_mm'){
			$pieces=($_POST['35_mm_pieces']<=100 ? '100' : ($_POST['35_mm_pieces']<=500 ? '500' : 'infinity'));
			$price_per_piece=$this->actual_prices['35_mm'][$_POST['35_mm_dpi']][$pieces];
			$price=$_POST['35_mm_pieces']*$price_per_piece;
		}
		elseif($input_name==='roll'){
			$price_per_piece=$this->actual_prices['roll'][$_POST['roll_dpi']][$_POST['size']];
			$price=$_POST['roll_pieces']*$price_per_piece;
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