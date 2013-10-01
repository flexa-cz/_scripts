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
						'6x7'=>36,
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
		foreach($this->integer_inputs as $input_name){
			if(!isset($_POST[$input_name])){
				$this->output=array(
						'error'=>true,
						'error_num'=>2,
						'status'=>'error',
						'report'=>'Vnitřní chyba objednávkové aplikace.'
				);
			}
			elseif($_POST[$input_name] && $_POST[$input_name]!=(int)$_POST[$input_name]){
				$this->output=array(
						'error'=>true,
						'error_num'=>1,
						'status'=>'alert',
						'report'=>'Zadané hodnoty musí být celá čísla.'
				);
				$return=false;
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
//			return $this
//							->composeMail()
//							->sendMail();
			return true;
		}
		return false;
	}

	private function composeMail(){
		return $this;
	}

	/**
	 *
	 * @param array $to
	 * @param array $from
	 * @param string $subject
	 * @param string $message
	 * @param string $header
	 * @return boolean
	 */
	private function sendMail($to, $from, $message, $subject = '(No subject)', $header = ''){
		$recipients=array();
		$senders=array();
		foreach($to as $recipient){
			$recipients[]=$recipient['name'].'<'.$recipient['email'].'>';
		}
		foreach($from as $sender){
			$senders[]=$sender['name'].'<'.$sender['email'].'>';
		}
		$header = 'MIME-Version: 1.0'
						. "\r\nContent-type: text/html; charset=UTF-8\r\n"
						.(!empty($recipients) ? "To: ".implode(',', $recipients)."\r\n" : false)
						.(!empty($senders) ? "From: ".implode(',', $senders)."\r\n" : false)
						.$header;
		return @mail(implode(',',$recipients), '=?UTF-8?B?'.base64_encode($subject).'?=', $message, $header);
	}
}