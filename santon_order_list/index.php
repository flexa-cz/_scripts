<?php
/**
 * klient pro vypis objednavek Santonu
 *
 * @since 8.2.12 15:33
 * @author Vlahovic
 */

// nezbytnosti
require_once('../core/include.php');
debuger::set_enable_report(true);
$core=new Core;

// nastaveni - heslo a databaze
global $config;
include(_ROOT.'santon_order_list/config.php');

// objekt strany
$core->site->setTitle('správa objednávek Santon');
$core->site->addHeader('<style type="text/css">'.debuger::get_css().'</style>');

session_start();

// pouze pokud je uzivatel prihlaseny
if(login($core,$config)){
	// vychozi razeni
	$_GET['order_by']=(!empty($_GET['order_by']) ? $_GET['order_by'] : 'created_at');
	if(!empty($_GET['asc']) && !empty($_GET['desc'])){
		$_GET['desc']=true;
	}

	// odhlasit
	$core->site->addContent(_N.'<a href="'.url::getAddrString().'?logout" class="button cross">odhlásit</a><br /><br />');

	// filtr
	order_filter($core);

	// objekt databaze
	$core->db->setMysqlDatabase($config['database'])->connect();

	// detail
	order_detail($core);

	// odstraneni
	order_delete($core);

	// tabulka s vypisem
	list_of_orders($core);

}

echo $core->site;




function order_delete($core){
	if(!empty($_GET['delete'])){
		$id=intval($_GET['delete']);
		// existuje v db?
		$query="SELECT COUNT(*) AS `count` FROM orders WHERE id=".$id;
		$rows=$core->db->query($query)->getRows();
		if($rows[0]['count']){
			$query="DELETE FROM orders WHERE id=".$id;
			$res=$core->db->query($query)->getResult();
			if($res){
				report::getInstance()->setReport('Položka s ID '.$id.' byla odstraněna.','accept');
				// preventivne jeste promazat jednotlive polozky
				$query="DELETE FROM order_items WHERE order_id=".$id;
				$res=$core->db->query($query)->getResult();
			}
			else{
				report::getInstance()->setReport('Položka s ID '.$id.' nejde odstranit kvůli chybě v systému.','wrn');
			}
		}
		else{
			report::getInstance()->setReport('Položka s ID '.$id.' nejde odstranit, protože není v systému.','alert');
		}
	}
	$core->site->addContent(report::getInstance()->getReport());
}




function order_filter($core){
	$core->site->addContent('<p>Datum zadávejte ve stejném formátu v jakém se vypisuje v tabulce (RRRR-MM-DD HH:MM:SS).<br />Není nutné vypisovat všechny části - je možné je od konce odebírat.<br />Např. "od 2011-05" najde všechny záznamy vzniklé po květnu 2011 včetně.</p>');
	$select=_N.'<select name="type" id="type">';
	$select.=_N_T.'<option value="all">vše</option>';
	$select.=_N_T.'<option value="orders"'.(!empty($_GET['type']) && $_GET['type']=='orders' ? ' selected="selected"' : false).'>objednávky</option>';
	$select.=_N_T.'<option value="reservations"'.(!empty($_GET['type']) && $_GET['type']=='reservations' ? ' selected="selected"' : false).'>rezervace</option>';
	$select.=_N.'</select>';
	$core->site->addContent(_N.'<form action="'.url::getAddrString().'?'.url::getQueryString(array('type','from','to')).'" method="get">');
	$core->site->addContent(_N.'<fieldset>');
	$core->site->addContent(_N.'<legend>filtr</legend>');

	$core->site->addContent(_N.'<label for="type">typ</label>');
	$core->site->addContent($select);

	$core->site->addContent(_N.'<label for="from">vytvořeno od</label>');
	$core->site->addContent(_N.'<input type="text" name="from" id="from" value="'.$_GET['from'].'" />');

	$core->site->addContent(_N.'<label for="to">vytvořeno do</label>');
	$core->site->addContent(_N.'<input type="text" name="to" id="to" value="'.$_GET['to'].'" />');

	$core->site->addContent(_N.'<input type="submit" name="filter" class="button search" value="filtrovat" />');
	$core->site->addContent(_N.'</fieldset>');
	$core->site->addContent(_N.'</form>');
}



function order_detail($core){
		// skryt detail
	if(isset($_GET['id']) && intval($_GET['id'])){
		$core->site->addContent(_N.'<a href="'.url::getAddrString().'?'.url::getQueryString(array('id')).'" class="button delete">skrýt detail</a>');
	}

	// detail
	if(!empty($_GET['id']) && intval($_GET['id'])){
		$query="
			#detail
			SELECT *
			FROM orders
			WHERE `id`=".intval($_GET['id'])."
			LIMIT 1
			";
		$rows=$core->db->query($query)->getRows();
		foreach($rows[0] as $key => $val){
			report::getInstance()->setReport('<strong>'.$key.':</strong> '.(trim($val) ? $val : '&mdash;'),'info');
		}
		$core->site->addContent(report::getInstance()->getReport());
	}
}





function list_of_orders($core){

	// vytahne seznam objednavek
	$from=false;
	if(isset($_GET['from']) && $_GET['from']){
		$from=new DateTime($_GET['from']);
		$from=$from->format('Y-m-d H:i:s');
	}
	$to=false;
	if(isset($_GET['to']) && $_GET['to']){
		$to=new DateTime($_GET['to']);
		$to=$to->format('Y-m-d H:i:s');
	}
	$query="
		# seznam objednavek
		SELECT
			`id`,
			`name`,
			`surname`,
			`company`,
			IF(`email` LIKE '%@%', CONCAT('<a href=\"mailto:', `email`, '\">', `email`, '</a>'), `email`) AS `email`,
			`phone`,

			`created_at`,
			# `updated_at`,
			# IF(`created_at`!='' AND `created_at`!='0000-00-00 00:00:00', DATE_FORMAT(`created_at`, '%d. %m. %Y, %H:%i'), '') AS `created_at`,
			# IF(`updated_at`!='' AND `updated_at`!='0000-00-00 00:00:00', DATE_FORMAT(`updated_at`, '%d. %m. %Y, %H:%i'), '') AS `updated_at`,

			IF(`objednavka`=1, 'objednávka', 'rezervace') AS `type`,

			-- cena
			IF(`objednavka`=1,
			(
				SELECT SUM(i.`price`)
				FROM order_items AS i
				WHERE i.`order_id`=o.`id`
			),
			'') AS `price`,

			CONCAT('<a href=\"".url::getAddrString().'?'.url::getQueryString(array('id','delete'))."&id=', `id`, '\" class=\"button info\">detail</a>') AS `detail`,

			CONCAT('<a href=\"".url::getAddrString().'?'.url::getQueryString(array('delete','id'))."&delete=', `id`, '\" class=\"button delete\" onclick=\"return(confirm(\'Přejete si odstranit položku ID ', `id`, '?\'))\">odstranit</a>') AS `delete`,

			`note`
		FROM orders AS o
		WHERE
			(`name`!='' OR
			`surname`!='' OR
			`note`!='')
			".(isset($_GET['type']) && ($_GET['type']=='orders' || $_GET['type']=='reservations') ? "AND `objednavka`=".($_GET['type']=='orders' ? 1 : 0) : false)."
		".($from ? "AND `created_at`>='".$from."'" : false)."
		".($to ? "AND `created_at`<='".$to."'" : false)."
		".(!empty($_GET['order_by']) ? "ORDER BY `".mysql_real_escape_string($_GET['order_by'])."` ".(isset($_GET['desc']) ? 'DESC' : 'ASC') : false)."
		";
	$rows=$core->db->query($query)->getRows();

	// tabulka
	if($rows && is_array($rows) && count($rows)){
		// kvuli rezervacim, jeste preparsuje
		foreach($rows as $i => $row){
			if($row['type']=='rezervace' && trim($row['note'])){
				$ra=explode(_N,$row['note']);
				if(count($ra)>1){
					$n=trim(str_replace('Jméno: ',false, $ra[0]));
					$n=explode(' ',$n);
					$name=$n[0];
					$surname=(!empty($n[1]) ? $n[1] : false);
					$email=trim(str_replace('Email: ',false, $ra[1]));
					$rows[$i]['name']=$name;
					$rows[$i]['surname']=$surname;
					$rows[$i]['email']=(strpos($email,'@') ? '<a href="mailto:'.$email.'">'.$email.'</a>' : $email);
				}
			}
			unset($rows[$i]['note']);
		}
		$header=array(
				'id'=>'ID',
				'name'=>'jméno',
				'surname'=>'příjmení',
				'company'=>'společnost',
				'email'=>'e-mail',
				'phone'=>'telefon',
				'created_at'=>'vytvořeno',
//				'updated_at'=>'editováno',
				'type'=>'typ',
				'price'=>'cena',
				'detail'=>'detail',
				'delete'=>'odstranit',
		);
		$core->table->setRows($rows)->setHeader($header)->setOrderBy(true);
		$core->site->addContent($core->table);
	}
	// nejsou zaznamy
	else{
		report::getInstance()->setReport('Zadaným podmínkám nevyhovují žádné záznamy.','alert');
		$core->site->addContent(report::getInstance()->getReport());
	}
}







/**
 * funkce na prihlaseni... :-)
 *
 * @param object $core
 * @return boolean
 */
function login($core,$config){
	$return=false;
	$addr='http://'.url::getServerName().url::getAddrString();
	if(isset($_GET['logout'])){
		session_destroy();
		header('location:'.$addr);
		exit();
	}
	elseif(empty($_SESSION['log'])){
		if(!empty($_POST['password'])){
			if($_POST['password']==$config['password']){
				$_SESSION['log']=true;
				header('location:'.$addr);
				exit();
			}
			else{
				report::getInstance()->setReport('Špatné heslo!','alert');
			}
		}
		elseif(isset($_POST['password'])){
			report::getInstance()->setReport('Nezadali jste heslo!','alert');
		}
		$core->site->addContent('<form action="" method="post">');
		$core->site->addContent('<fieldset>');
		$core->site->addContent(report::getInstance()->getReport());
		$core->site->addContent('<label for="password">heslo</label>');
		$core->site->addContent('<input type="password" id="password" name="password">');
		$core->site->addContent('<input type="submit" value="přihlásit">');
		$core->site->addContent('</fieldset>');
		$core->site->addContent('</form>');
	}
	else{
		$return=true;
	}
	return $return;
}