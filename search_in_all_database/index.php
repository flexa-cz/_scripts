<?php
/**
 * vyhledavani v cele zadane databazi
 *
 * @since 6.4.12 9:17
 * @author Vlahovic
 */

// nezbytnosti
require_once('../core/include.php');
//debuger::set_enable_report(false);
$core=new Core;

// inicializace
$database=(!empty($_POST['database']) ? $_POST['database'] : false);
$search=(!empty($_POST['search']) ? $_POST['search'] : false);

// zvolena databaze
$core->db->setMysqlDatabase($database)->connect();

// objekt strany
$core->site->setTitle('vyhledavani v db');
$core->site->addHeader('<style type="text/css">'.debuger::get_css().'</style>');

// selectbox s databazemi
$query="SHOW DATABASES";
$databases=$core->db->query($query)->getRows();
//debuger::var_dump($databases);
// posklada
$select=false;
$select.=_N.'<select name="database" id="database">';
$select.=_N_T.'<option value="">-- vyber --</option>';
foreach($databases as $db){
	$select.=_N_T.'<option value="'.$db['Database'].'"'.($database==$db['Database'] ? ' selected="selected"' : false).'>'.$db['Database'].'</option>';
}
$select.=_N.'</select>';

// vyhledavani samotne
$return=false;
$found_rows=0;
if($database && $search){
	$query="SHOW TABLES";
	$tables=$core->db->query($query)->getRows();
	foreach($tables as $table){
		$table=$table['Tables_in_'.$database];
		// vybere nazvy sloupcu
		$query="SHOW COLUMNS FROM ".$table;
		$columns=$core->db->query($query)->getRows();
		// posklada podminku
		$where=array();
		foreach($columns as $column){
			$where[]="`".$column['Field']."` LIKE '%".$search."%'";
		}
		$query="SELECT * FROM ".$table." WHERE (".implode(' OR ', $where).")";
		$searched=$core->db->query($query)->getRows();
		if(!empty($searched)){
			$found_rows+=count($searched);
			$t=$core->table->setRows($searched);
			$return.='<h2>table: '.$table.'</h2>';
			$return.=$t;
		}
	}
}

// hlaseni
if(!$found_rows){
	report::getInstance()->setReport('V databazi "<strong>'.$database.'</strong>" nebyl zvoleny retezec "<strong>'.$search.'</strong>" nalezen.','alert');
}
else{
	report::getInstance()->setReport('V databazi "<strong>'.$database.'</strong>" byl zvoleny retezec "<strong>'.$search.'</strong>" nalezen <strong>'.$found_rows.'&times;</strong>.','accept');
}

// formular
$form=false;
$form.=_N.'<form action="" method="post">';
$form.=_N.'<fieldset>';

$form.=report::getInstance()->getReport();

$form.=_N.'<label for="search">hledat</label>';
$form.=_N.'<input type="text" name="search" id="search" value="'.$search.'" />';

$form.=_N.'<label for="database">databaze</label>';
$form.=$select;

$form.=_N.'<input type="submit" value="odeslat">';

$form.=_N.'</fieldset>';
$form.=_N.'</form>';

$core->site->addContent($form);
$core->site->addContent($return);
$core->site->setHighlight($search);

echo $core->site;
