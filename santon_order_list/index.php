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
	// odhlasit
	$core->site->addContent(_N.'<a href="'.url::getAddrString().'?logout" class="button cross">odhlásit</a><br /><br />');

	// skryt detail
	if(isset($_GET['id']) && intval($_GET['id'])){
		$core->site->addContent(_N.'<a href="'.url::getAddrString().'?'.url::getQueryString(array('id')).'" class="button delete">skrýt detail</a>');
	}

	// objekt databaze
	$core->db->setMysqlDatabase($config['database'])->connect();

	// detail
	if(!empty($_GET['id']) && intval($_GET['id'])){
		$query="
			#detail
			SELECT *
			FROM orders
			WHERE `id`=".intval($_GET['id'])."
			LIMIT 1
			";
		$rows=$core->db->query($query)->get_rows();
		foreach($rows[0] as $key => $val){
			report::getInstance()->setReport('<strong>'.$key.':</strong> '.(trim($val) ? $val : '&mdash;'),'info');
		}
		$core->site->addContent(report::getInstance()->getReport());
	}

	// vytahne seznam objednavek
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
			`updated_at`,
			# IF(`created_at`!='' AND `created_at`!='0000-00-00 00:00:00', DATE_FORMAT(`created_at`, '%d. %m. %Y, %H:%i'), '') AS `created_at`,
			# IF(`updated_at`!='' AND `updated_at`!='0000-00-00 00:00:00', DATE_FORMAT(`updated_at`, '%d. %m. %Y, %H:%i'), '') AS `updated_at`,

			`objednavka`,
			CONCAT('<a href=\"".url::getAddrString().'?'.url::getQueryString(array('id'))."&id=', `id`, '\" class=\"button info\">detail</a>') AS `detail`
		FROM orders
		WHERE
			`name`!='' OR
			`surname`!='' OR
			`note`!=''
		".(!empty($_GET['order_by']) ? "ORDER BY `".mysql_real_escape_string($_GET['order_by'])."` ".(isset($_GET['desc']) ? 'DESC' : 'ASC') : false)."
		";
	$rows=$core->db->query($query)->get_rows();

	// tabulka
	$header=array(
			'id'=>'ID',
			'name'=>'jméno',
			'surname'=>'příjmení',
			'company'=>'společnost',
			'email'=>'e-mail',
			'phone'=>'telefon',
			'created_at'=>'vytvořeno',
			'updated_at'=>'editováno',
			'objednavka'=>'objednávka',
			'detail'=>'detail',
	);
	$core->table->setRows($rows)->setHeader($header)->setOrderBy(true);
	$core->site->addContent($core->table);

}

echo $core->site;

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
				header('location:'.$addr.'?order_by=id&asc');
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