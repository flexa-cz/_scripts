<?php
/**
 * test odesilani emailu z localhostu
 *
 * @since 28.11.11 9:46
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
