<?php
/**
 * pocita pravdepodobnost nasledujicich tahu sazky na zaklade databaze historickych tahu
 *
 * @since 8.2.12 15:02
 * @author Vlahovic
 */

// nezbytnosti
require_once('../core/include.php');
debuger::set_enable_report(true);
$core=new Core;
$core->site->setTitle('sazka');
$core->site->addHeader('<style type="text/css">'.debuger::get_css().'</style>');

$core->db->setMysqlDatabase('sazka')->connect();

// tlacitka
$core->site->addContent('<a href="'.url::getAddrString().'" class="button">pouze statistiky</a>');
$core->site->addContent('<a href="'.url::getAddrString().'?check" class="button save">uložit aktuální data</a>');
$core->site->addContent('<a href="'.url::getAddrString().'?tip" class="button add">vytvořit nový tip</a>');


// vypise statistiky
include(_ROOT.'sazka/sazka_statistics.class.php');
$statistics=new SazkaStatistics($core);
$core->site->addContent($statistics);


// nacte data
if(isset($_GET['check'])){
	// html parser
	include(_ROOT.'core/libraries/simplehtmldom_1_5/simple_html_dom.php');

	// najde adresy na strany s vysledky
	include(_ROOT.'sazka/address_checker.class.php');
	$addr=new address_checker($core);
	debuger::breakpoint('before address check');
	debuger::set_enable_report(false);
	$addr->check_addresses();
	debuger::set_enable_report(true);
	debuger::breakpoint('before scores check');
	debuger::set_enable_report(false);
	$addr->check_scores();
	debuger::set_enable_report(true);
	debuger::breakpoint('after scores check');
	$core->site->addContent($addr);
}

debuger::breakpoint('before html output');
echo $core->site;

