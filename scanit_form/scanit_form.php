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
				'1400'=>array(),
				'2700'=>array(),
				'4000'=>array(),
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