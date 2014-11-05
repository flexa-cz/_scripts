<?php
$site_title='Doctor 1.0';

// nezbytnosti
require_once('../core/include.php');
debuger::set_enable_report(true);
$core=new Core;
$core->db->setMysqlDatabase('doctor')->connect();

$allowed_controllers=array('persons');
$controller=(!empty($_GET['controller']) && in_array($_GET['controller'], $allowed_controllers) ? $_GET['controller'] : 'persons');

$core->site->addContent(_N.'<h1>'.$site_title.'</h1>');

if($controller==='persons'){
	$core->loader->getController('Persons', array('Core'=>$core))->render();
}


// vystup na monitor
$core->site->setTitle($site_title);
echo $core->site;