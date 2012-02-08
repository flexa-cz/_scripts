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

// nastaveni - heslo a databaze
global $config;
include(_ROOT.'santon_order_list/config.php');

// objekt strany
$site=new Site;
$site->addHeader('<style type="text/css">'.debuger::get_css().'</style>');

session_start();

// pouze pokud je uzivatel prihlseny
if(login($site)){
	// objekt databaze
	$db=new Db;
	$db->setMysqlDatabase($config['database'])->connect();

	// vytahne seznam
	$query="SELECT * FROM orders";
	$rows=$db->query($query)->get_rows();

	$table=new Table($rows);
	$site->addContent($table);

}

echo $site;



/**
 * funkce na prihlaseni... :-)
 *
 * @param object $site
 * @return boolean
 */
function login($site){
	$return=false;
	if(empty($_SESSION['log'])){
		if(!empty($_POST['password'])){
			if($_POST['password']==$config['password']){
				$addr='http://'.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
				$_SESSION['log']=true;
				header('location:'.$addr);
				exit();
			}
			else{
				$site->addContent('<h1>špatné heslo</h1>');
			}
		}
		elseif(isset($_POST['password'])){
			$site->addContent('<h1>nezadali jste heslo</h1>');
		}
		$site->addContent('<form action="" method="post">');
		$site->addContent('<fieldset>');
		$site->addContent('<label for="password">heslo</label>');
		$site->addContent('<input type="password" id="password" name="password">');
		$site->addContent('<input type="submit" value="přihlásit">');
		$site->addContent('</fieldset>');
		$site->addContent('</form>');
	}
	else{
		$return=true;
	}
	return $return;
}