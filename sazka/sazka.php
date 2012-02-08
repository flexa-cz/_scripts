<?php
/**
 * pocita pravdepodobnost nasledujicich tahu sazky na zaklade databaze historickych tahu
 *
 * @since 8.2.12 15:02
 * @author Vlahovic
 */

// nezbytnosti
require_once('../core/include.php');
$site=new Site;
$site->addHeader('<style type="text/css">'.debuger::get_css().'</style>');

include(_ROOT.'sazka/address_checker.class.php');
$addr=new address_checker();
$addr->check();


echo $site;

