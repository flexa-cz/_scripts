<?php
$site_title='Doctor 1.0';

// nezbytnosti
require_once('../core/include.php');
core\debuger::set_enable_report(true);
$core=new core\Core;
$core->db->setMysqlDatabase('doctor')->connect();

$allowed_controllers=array('persons');
$controller=(!empty($_GET['controller']) && in_array($_GET['controller'], $allowed_controllers) ? $_GET['controller'] : 'persons');

<<<<<<< HEAD
$core->site->setTitle($site_title);
$core->site->addContent(_N.'<h1>'.$site_title.'</h1>');

if($controller==='persons'){
	$core->loader->getController('Person', array('Core'=>$core))->render();
=======
$core->site->addContent(_N.'<h1>'.$site_title.'</h1>');

if($controller==='persons'){
	$core->loader->getController('Persons', array('Core'=>$core))->render();
>>>>>>> origin/master
}


echo $core->site;