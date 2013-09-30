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
$output=false;
$allowed_domains=array(
		'localhost',
		'flexa.cz',
		'scanit.cz',
				);

/**
 * http://www.php.cekuj.net/php/phpmailer-jednoduche-odesilani-emailu
 */
if(in_array($_SERVER['HTTP_HOST'], $allowed_domains)){
	if(!empty($_POST['get_actual_prices'])){
		$output=$actual_prices;
	}
	elseif(!empty($_POST)){
		$output=array(
				'error'=>false,
				'error_num'=>0,
		);
	}
}
else{
	$output=array('error'=>'query from unallowed domain');
}
if($output){
	echo json_encode($output);
}