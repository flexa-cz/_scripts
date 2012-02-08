<?php
/**
 * jde nejak poslepovat fluent interface ze staticke tridy?
 *
 * @since 07.02.12 10:30
 * @author Vlahovic
 */

// nezbytnosti
require_once('../core/include.php');
$site=new Site;

$site->addContent('<h1>fluent interface in static class</h1>');
$site->addContent('<p>Is it posible?</p>');
$site->addContent('<p>I`m afraid... no...</p>');

debuger::set_enable_report(true);
debuger::breakpoint('test');

$site->addHeader('<style type="text/css">'.debuger::get_css().'</style>');
echo $site;
