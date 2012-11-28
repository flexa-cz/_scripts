<?php
/*
 * formulari nastavit action="" (skutecne prazdne uvozovky)
 * na misto kde se ma vypisovat hlaseni o (ne)uspesnem vypleneni a odeslani
 * vlozit nasledujici ctyri radky - urcite vsak nekde pred samotnym formularem
 * kvuli tomu, ze na konci, pokud se formular odesle, tak vysypu $_POST
 * aby se data ve formulari uz nezobrazila
<?php
include_once('./register_form.php');
register_form();
?>
 *
 * pocitam s tim, ze tento soubor bude ve stejnem adresari, jako soubor s formularem
 * jinak by se musela zmenit adresa ve funkci include_once()
 *
 * po odeslani formulare to vypise hlaseni
 * jde o tagy <p>
 * uspesne ma tridu "succ"
 * neuspesne ma tridu "alert"
 *
 * jeste by doporucil do kazdeho inputu vlozit nasledujici
value="<?php echo @$_POST['{field_name}'] ?>"
 * kdy {field_name} ma byt nahrazeno nazvem inputu - to co je v atribudu "name"
 * aby se pri neuspesnem odeslani nestalo, ze se formular vysype
 */

/**
 * odesila data z registracniho formulare
 */
function register_form(){
	// lze bez obav menit :-)
	$to='indiani@indiani.cz';
	$subject='registrace z webu indiancorral.cz'; // musi byt bez diakritiky, jinak se korektni odeslani komplikuje
	$alert_robot='Pokud nejste robot, potom prosim nechejte položku "web" prázdnou.';
	$alert_param='Pokud se chcete stát èlenem, pak musíte potvrdit platbu èlenského pøíspìvku.';
	$message_intro='Data z registraèního formuláøe na webu indiancorral.cz';
	$succ_sent='Registrace byla odeslána.';
	$alert_sent='Došlo k chybì pøi odeslání e-mailu. Zkuste to prosím za chvíli znovu, nebo kontaktujte správce webu.';

	// pole ktera se zpracovavaji
	// a povinna pole - pokud nema byt nektere pole povinne, staci mu nastavit required na false
	$all_fields=array(
		'jmeno'=>array('cz'=>'jméno','required'=>true),
		'prijmeni'=>array('cz'=>'pøíjmení','required'=>true),
		'ulice'=>array('cz'=>'ulice','required'=>true),
		'mesto'=>array('cz'=>'mìsto','required'=>true),
		'psc'=>array('cz'=>'PSÈ','required'=>true),
		'rodnecislo'=>array('cz'=>'rodné èíslo','required'=>true),
		'prezdivka'=>array('cz'=>'pøezdívka','required'=>true),
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
				if(mail($to, $subject, $message, $headers)){
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
		echo '<p class="succ">'.implode('<br>',$succ).'</p>';
	}
	if(count($alert)){
		echo '<p class="alert">'.implode('<br>',$alert).'</p>';
	}
}