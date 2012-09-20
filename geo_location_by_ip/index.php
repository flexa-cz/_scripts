<?php
/**
 * test tridy pro geolokaci
 *
 * @since 11.4.12 8:49
 * @author Vlahovic
 */
$fill=false;


// nezbytnosti
require_once('../core/include.php');
debuger::set_enable_report(false);
$core=new Core;

// pripoji db
$core->db->setMysqlDatabase('spektrum-zdravi')->connect();
// trida na zjistovani geolokace
include('./geolocation_by_ip.class.php');
$gl=new GeolocationByIp(false, $core->db->getMysqlConnect());

// vytahne seznam ip adres k prevedeni
// jen tech, ktere jeste nejsou lokalizovane
if($fill){
	set_time_limit(0);
	$query="
		SELECT DISTINCT cd.`ip`
		FROM count_display_of_photo_detail as cd
		LEFT JOIN geolocation_by_ip as gl ON gl.ip = cd.ip
		WHERE
			cd.`ip` IS NOT NULL AND
			cd.`ip`!='' AND
			gl.`ip` IS NULL";
	$core->db->query($query);
	$rows=$core->db->getRows();
	foreach($rows as $row){
		// zjisti lokaci
		$gl->setIp($row['ip'])->checkIp();
	}
}

// nas server
$gl->setIp('77.93.215.68')->checkIp();
var_dump($gl);
exit('<p><b>file:</b> '.__FILE__.'<br><b>line:</b> '.__LINE__.'<br><b>function:</b> exit()</p>');

// neco co nema vsechny udaje
$gl->setIp('89.233.180.30')->checkIp();
var_dump($gl);

echo $gl->mostActiveRegionId().' => '.$gl->mostActiveRegionName().'<br>';
echo $gl->mostActiveRegionId('UK').' => '.$gl->mostActiveRegionName('UK').'<br>';
?>
