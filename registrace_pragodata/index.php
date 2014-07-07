<?php
/**
 * pocita pravdepodobnost nasledujicich tahu sazky na zaklade databaze historickych tahu
 *
 * @since 8.2.12 15:02
 * @author Vlahovic
 */
// nezbytnosti
require_once('../core/include.php');
require_once('./registration_form_pragodata.class.php');
debuger::set_enable_report(true);
$core=new Core;
$core->site->setTitle('registrace Pragodata');
$core->site->addHeader('<script type="text/javascript" src="/_scripts/core/js/jquery-1.11.0.min.js"></script>');
$core->site->addHeader('<script type="text/javascript" src="/_scripts/registrace_pragodata/js/add_remove_code_segment.class.js"></script>');
$core->site->addHeader('<script type="text/javascript" src="/_scripts/registrace_pragodata/js/registration_pragodata.js"></script>');
$core->site->addHeader('<style type="text/css">'.debuger::get_css().'</style>');

$core->db->setMysqlDatabase('registrace')->connect();

$action_id=1;
$action=$core->db->query("SELECT * FROM akce WHERE id=".$action_id)->getRow();
$query="
	SELECT
		podakce.*,
		IF(akce.termin_vcasna_cena>NOW(), cena_vcasna, cena_normal) price_czk,
		IF(akce.termin_vcasna_cena>NOW(), cena_vcasna_eur, cena_normal_eur) price_eur,
		IF(akce.termin_vcasna_cena>NOW(), cena_student_vcasna, cena_student_normal) price_student_czk,
		IF(akce.termin_vcasna_cena>NOW(), cena_student_vcasna_eur, cena_student_normal_eur) price_student_eur
	FROM podakce
	LEFT JOIN akce ON akce.id=podakce.akce_id
	WHERE akce_id=".$action_id."
	ORDER BY termin, nazev";
$subactions=$core->db->query($query)->getRows();


$registration_form_pragodata=new RegistrationFormPragodata();
$form=$registration_form_pragodata->setAction($action)->setSubactions($subactions)->printForm();
$core->site->addContent($form);
// tlacitka
//$core->site->addContent('<a href="'.url::getAddrString().'" class="button">pouze statistiky</a>');
//$core->site->addContent('<a href="'.url::getAddrString().'?check" class="button save">uložit aktuální data</a>');
//$core->site->addContent('<a href="'.url::getAddrString().'?tip" class="button add">vytvořit nový tip</a>');
// nacte data
//if(isset($_GET['check'])){
//	// html parser
//	include(_ROOT.'core/libraries/simplehtmldom_1_5/simple_html_dom.php');
//
//	// najde adresy na strany s vysledky
//	include(_ROOT.'sazka/address_checker.class.php');
//	$addr=new address_checker($core);
//	debuger::breakpoint('before address check');
//	debuger::set_enable_report(false);
//	$addr->check_addresses();
//	debuger::set_enable_report(true);
//	debuger::breakpoint('before scores check');
//	debuger::set_enable_report(false);
//	$addr->check_scores();
//	debuger::set_enable_report(true);
//	debuger::breakpoint('after scores check');
//	$core->site->addContent($addr);
//}

debuger::breakpoint('before html output');
echo $core->site;