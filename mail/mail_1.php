<?php
/**
 * test odesilani emailu z localhostu
 *
 * @since 28.11.11 9:46
 * @author Vlahovic
 */

// nezbytnosti
require_once('../core/include.php');

// odesle email
email_send();
// posklada a vrati stranu
$site=new Site;
// posklada formular
$site->AddContent(email_form());
// vystup do prohlizece
echo $site;

function email_form(){
	$r=false;

	$r.=_N.'<form method="post" action="">';
	$r.=_N_T.'<fieldset>';
	$r.='<legend>E-mail</legend>';

	// subject
	$r.=_N_T_T.'<label for="subject">Subject</label>';
	$r.=_N_T_T.'<input type="text" name="subject" id="subject"'.(isset($_POST['subject']) ? ' value="'.$_POST['subject'].'"' : false).'>';

	// from
	$r.=_N_T_T.'<label for="from">From</label>';
	$r.=_N_T_T.'<input type="text" name="from" id="from"'.(isset($_POST['from']) ? ' value="'.$_POST['from'].'"' : false).'>';

	// to
	$r.=_N_T_T.'<label for="to">To</label>';
	$r.=_N_T_T.'<input type="text" name="to" id="to"'.(isset($_POST['to']) ? ' value="'.$_POST['to'].'"' : false).'>';

	// body
	$r.=_N_T_T.'<label for="message">Message</label>';
	$r.=_N_T_T.'<textarea name="message" id="message">'.(isset($_POST['message']) ? $_POST['message'] : false).'</textarea>';

	$r.=_N_T.'</fieldset>';
	$r.=_N_T.'<input type="submit" name="send_email" value="odeslat">';
	$r.=_N.'</form>';

	return $r;
}

function email_send(){
	$mail=false;
	$control=true;
	if(isset($_POST['send_email'])){
		if(!$_POST['subject']){
			report::getInstance()->setReport('neni zadan predmet','alert');
			$control=false;
		}
		if(!$_POST['from']){
			report::getInstance()->setReport('neni zadan odesilatel','alert');
			$control=false;
		}
		if(!$_POST['to']){
			report::getInstance()->setReport('neni zadan adresat','alert');
			$control=false;
		}
		if(!$_POST['message']){
			report::getInstance()->setReport('neni zadan obsah emailu','alert');
			$control=false;
		}
		if($control){
			$headers = 'From: '.$_POST['from']."\r\n" .
			'Reply-To: '.$_POST['from']."\r\n" .
			'X-Mailer: PHP/'.phpversion();
			$mail=@mail($_POST['to'], $_POST['subject'], $_POST['message'],$headers);
			if($mail){
				report::getInstance()->setReport('email byl odeslan');
			}
			else{
				report::getInstance()->setReport('email se nepodarilo odeslat','warning');
			}
		}
	}
	return $mail;
}