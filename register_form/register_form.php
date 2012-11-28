<?php
register_form();
/**
 * odesila data z registracniho formulare
 * je volano ajaxem
 * vystup je vypis json
 */
function register_form(){
	// lze bez obav menit :-)
	$to='indiani@indiani.cz';
	$subject='registrace z webu indiancorral.cz'; // musi byt bez diakritiky, jinak se korektni odeslani komplikuje
	$alert_robot='Pokud nejste robot, potom prosim nechejte položku "web" prázdnou.';
	$alert_param='Pokud se chcete stát členem, pak musíte potvrdit platbu členského příspěvku.';
	$message_intro='Data z registračního formuláře na webu indiancorral.cz';
	$succ_sent='Registrace byla odeslána.';
	$alert_sent='Došlo k chybě při odeslání e-mailu. Zkuste to prosím za chvíli znovu, nebo kontaktujte správce webu.';

	// pole ktera se zpracovavaji
	// a povinna pole - pokud nema byt nektere pole povinne, staci mu nastavit required na false
	$all_fields=array(
		'jmeno'=>array('cz'=>'jméno','required'=>true),
		'prijmeni'=>array('cz'=>'příjmení','required'=>true),
		'ulice'=>array('cz'=>'ulice','required'=>true),
		'mesto'=>array('cz'=>'město','required'=>true),
		'psc'=>array('cz'=>'PSČ','required'=>true),
		'rodnecislo'=>array('cz'=>'rodné číslo','required'=>true),
		'prezdivka'=>array('cz'=>'přezdívka','required'=>true),
		'email'=>array('cz'=>'e-mail','required'=>true),
	);

	// inicializace
	$alert=array();
	$succ=array();
	// formular byl odeslan
	if(isset($_POST['jmeno'])){
		// asi jde o robota
		if($_POST['web']){
			$alert[]=$alert_robot;
		}
		// parametr musi byt checknuty
		elseif(!isset($_POST['parametr'])){
			$alert[]=$alert_param;
		}
		else{
			$control=true;
			// hromadne osetreni
			foreach($all_fields as $key => $value){
				$$key=(isset($_POST[$key]) && $_POST[$key] ? addslashes($_POST[$key]) : false);
				if($value['required'] && (!isset($_POST[$key]) || !$_POST[$key])){
					$alert[]='Položka "'.$value['cz'].'" je povinná.';
					$control=false;
				}
			}
			// vsechno je ok
			if($control){
				// message
				$message = '
					<p>'.$message_intro.'</p>
					<table>';
				foreach($all_fields as $key => $value){
						$message.='<tr>
							<th>'.$value['cz'].'</th>
							<td>'.$$key.'</td>
						</tr>';
				}
				$message.='
					</table>
				';

				// To send HTML mail, the Content-type header must be set
				$headers  = 'MIME-Version: 1.0' . "\r\n";
				$headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";

				// Additional headers
				$headers .= 'To: <'.$to.'>' . "\r\n";
				$headers .= 'From: '.$jmeno.' '.$prijmeni.' <'.$email.'>' . "\r\n";
//				$headers .= 'Cc: birthdayarchive@example.com' . "\r\n";
//				$headers .= 'Bcc: birthdaycheck@example.com' . "\r\n";

				// Mail it
				if(@mail($to, $subject, $message, $headers)){
					$succ[]=$succ_sent;
					// vyprazdnim post
					foreach($all_fields as $key => $value){
						unset($_POST[$key]);
					}
				}
				else{
					$alert[]=$alert_sent;
				}
			}
		}
	}
	if(count($succ)){
		$return=array('error'=>0,'alert'=>implode("\r\n",$succ));
	}
	elseif(count($alert)){
		$return=array('error'=>1,'alert'=>implode("\r\n",$alert));
	}
	echo json_encode($return);
}