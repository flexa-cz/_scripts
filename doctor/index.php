<?php
$site_title='Doctor 1.0';
$persons_title='Osoby';

// nezbytnosti
require_once('../core/include.php');
debuger::set_enable_report(true);
$core=new Core;
$core->db->setMysqlDatabase('doctor')->connect();

$core->site->addContent(_N.'<h1>'.$site_title.'</h1>');

// seznam osob
$persons=$core->db->query("SELECT * FROM persons")->getRows();
$core->site->addContent(_N.'<h2>'.$persons_title.'</h2>');
$core->site->addContent($core->table->setHeader(array('ID','typ','jméno','příjmení','datum narození'))->setRows($persons));

// vystup na monitor
$core->site->setTitle($site_title);
echo $core->site;