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
$core->site->setTitle('repair PropCMS duplicity');
$core->site->addHeader('<style type="text/css">'.debuger::get_css().'</style>');


// tlacitka
$core->site->addContent('<a href="'.url::getAddrString().'" class="button info">seznam databází</a>');

// seznam databazi
if(empty($_GET)){
	$core->db->setMysqlDatabase('propeople_cms_backup')->connect();
	$query="SHOW DATABASES";
	$rows=array();
	foreach($core->db->query($query)->getRows() as $index => $row){
		$rows[$index]=array('Database'=>'<a href="?db='.$row['Database'].'">'.$row['Database'].'</a>');
	}
	$core->table->setRows($rows);
	$core->site->addContent($core->table);
}
elseif(!empty($_GET['db'])){
	$core->site->addContent('<h1>'.$_GET['db'].'</h1>');
	$core->db->setMysqlDatabase($_GET['db'])->connect();
	if(!empty($_GET['domain'])){
		$core->site->addContent('<h2>'.$_GET['domain'].'</h2>');
		$query="SELECT lft, rgt
			FROM settings
			LEFT JOIN relations ON relations.id=id_relations
			WHERE value='".$_GET['domain']."'
			LIMIT 1";
		$root=$core->db->query($query)->getRow();
	}

	// oprava jedne duplicity
	if(!empty($_GET['duplicity'])){
		$core->site->addContent('<h3>'.$_GET['duplicity'].'</h3>');
		include_once(_ROOT.'repair_prop_cms_duplicity/repair_duplicities.php');
		$repair=new RepairDuplicity($core->db->getMysqlConnect());
		$repair->repair($root['lft'], $root['rgt'], $_GET['duplicity']);
	}
	// vypis duplicit
	elseif(!empty($_GET['domain'])){
		$query="
			# vytahne duplicity
			SELECT *,
				(
					SELECT COUNT(*)
					FROM sites_parts AS sp
					LEFT JOIN relations AS r ON r.id_sites=sp.id_sites
					WHERE sp.part='url' AND sp.content=sites_parts.content AND r.lft>='".$root['lft']."' AND r.rgt<='".$root['rgt']."'
				) AS `count`
			FROM sites_parts
			LEFT JOIN relations ON relations.id_sites=sites_parts.id_sites
			WHERE
				part='url' AND
				content!='' AND
				lft>='".$root['lft']."' AND
				rgt<='".$root['rgt']."'
			GROUP BY content
			HAVING count > 1
			LIMIT 10";
		$rows=array();
		foreach($core->db->query($query)->getRows() as $index => $row){
			$rows[$index]=$row;
			$rows[$index]['content']='<a href="?db='.$_GET['db'].'&amp;domain='.$_GET['domain'].'&amp;duplicity='.urlencode($row['content']).'">'.$row['content'].'</a>';
		}
		$core->site->addContent($core->table->setRows($rows));
	}

	// seznam domen
	else{
		$query="
			# seznam domen
			SELECT *
			FROM settings
			WHERE name='alias'
			GROUP BY id_relations";
		$rows=array();
		foreach($core->db->query($query)->getRows() as $index => $row){
			$rows[$index]=$row;
			$rows[$index]['value']='<a href="?db='.$_GET['db'].'&amp;domain='.urlencode($row['value']).'">'.$row['value'].'</a>';
		}
		$core->site->addContent($core->table->setRows($rows));
	}
}

debuger::breakpoint('before html output');
echo $core->site;

