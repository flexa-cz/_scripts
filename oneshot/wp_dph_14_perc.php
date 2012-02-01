<?php
/**
 * pro spektrumzdravi
 * zmeni zaznamy s dph 10% na dph 14% a rozdil dopocita a upravi
 *
 * @since 29.12.11 15:24
 * @author Vlahovic
 */


define('_N',"\r\n");
define('_T',"\t");
define('_N_T',"\r\n\t");
define('_N_T_T',"\r\n\t\t");
define('_N_T_T_T',"\r\n\t\t\t");
define('_N_T_T_T_T',"\r\n\t\t\t\t");

// inicializace
// localhost
$config=array(
		'address'=>'127.0.0.1',
		'user'=>'root',
		'password'=>'',
		'database'=>'spektrum-zdravi',
);
// ostra db
//$config=array(
//		'address'=>'127.0.0.1',
//		'user'=>'root',
//		'password'=>'F0zguaP9',
//		'database'=>'spektrumzdravi',
//);

$r=false;
$row=0;
$report=array();
$from_dph=10;
$to_dph=14;
$diff_dph=$to_dph-$from_dph;
$change_price=false;

// pripojeni k db
connect($config);

// zjisti data, ktera je potreba upravit
$query="SELECT
    @act_price:=(
        SELECT meta_value
        FROM wp_postmeta
        WHERE
            wp_postmeta.post_id=p.ID AND
            wp_postmeta.meta_key='price'
    ) AS `act_price`,
    ".($change_price ? "ROUND(@act_price+(@act_price * 0.0".$diff_dph."))" : "@act_price")." AS `new_price`,
    p.`ID`,
    m.`meta_id` AS `dph_meta_id`,
    (
        SELECT meta_id
        FROM wp_postmeta
        WHERE
            wp_postmeta.post_id=p.ID AND
            wp_postmeta.meta_key='price'
    ) AS `price_meta_id`,
		".$to_dph." AS `new_dph`
FROM wp_posts AS p
LEFT JOIN wp_postmeta AS m ON
    m.post_id = p.ID AND
    m.meta_key='dph'
WHERE
    m.meta_value=".$from_dph.";";

// zpracuje
$res=mysql_query($query,  connect());
if($res && mysql_num_rows($res)){
	$header=false;
	$r.=_N.'<table border="1">';
	while($data=mysql_fetch_assoc($res)){
		$row++;
		// hlavicka
		if(!$header){
			$r.=_N_T.'<tr>';
			$r.=_N_T_T.'<th>#</th>';
			foreach($data as $k => $v){
				$r.=_N_T_T.'<th>'.$k.'</th>';
			}
			$r.=_N_T_T.'<th>resume</th>';
			$r.=_N_T>'</tr>';
			$header=true;
		}
		// radek
		$r.=_N_T.'<tr>';
		$r.=_N_T_T.'<td>'.$row.'</td>';
		foreach($data as $v){
			$r.=_N_T_T.'<td>'.$v.'</td>';
		}
		$update=update_one_article($data);
		if(!empty($update['report'])){
			$report[]=$update['report'];
		}
		$r.=_N_T_T.'<td>'.($update['resume'] ? '<strong class="green">OK' : '<strong class="red">BAD').'</strong></td>';
		$r.=_N_T.'</tr>';
	}
	$r.=_N.'</table>';
}
elseif(!$res){
	$r.=_N_T_T.'<p class="red">chyba v hlavnim dotaze</p>';
}
else{
	$r.=_N_T_T.'<p class="green">neni co editovat</p>';
}

// vystup na monitor
echo '<html>';
echo _N.'<header>';
echo _N_T.'<style>
		.red{color:red;}
		.green{color:green;}
	</style>';
echo _N.'</header>';
echo _N.'<body>';
echo (!empty($report) ? _N_T_T.'<p>'.implode('</p>'._N_T_T.'<p>',$report).'</p>' : false);
echo $r;
echo _N.'</body>';
echo _N.'<html>';

/**
 * edituje jedno zbozi
 *
 * @param array $data
 * @return array
 */
function update_one_article($data){
	$debug=false;
	$resume=true;
	$report=array();
	// upravi cenu
	$query="UPDATE wp_postmeta SET meta_value=".$data['new_price']." WHERE meta_id=".$data['price_meta_id'];
	$query2="UPDATE wp_postmeta SET meta_value=".$data['new_dph']." WHERE meta_id=".$data['dph_meta_id'];
	if(!$debug){
		$res=mysql_query($query,connect());
		if(!$res){
			$resume=false;
			$report[]='nepodarilo se nastavit novou cenu ('.$query.')';
		}
		else{
			// upravi hodnotu dph
			$res=mysql_query($query2,connect());
			if(!$res){
				$resume=false;
				$report[]='nepodarilo se nastavit novou hodnotu dph ('.$query.')';
			}
		}
	}
	else{
		echo $query.'<br>'.$query2.'<hr>';
		$resume=false;
	}
	return array('resume'=>$resume,'report'=>$report);
}

/**
 * pripoji k db a vraci ukazatel vytvoreneho pripojeni
 *
 * @staticvar pointer $connect pripojeni k db
 * @param array $config [optional]
 * @return pointer
 */
function connect($config=false){
	static $connect=null;
	if($connect===null){
		// nacte nastaveni pripojeni
		if(is_array($config) && count($config)){
			// pripojeni
			$connect=mysql_connect($config['address'], $config['user'], $config['password']);
			if($connect){
				$sel=mysql_select_db($config['database']);
				if($sel){
					mysql_query("SET CHARACTER SET utf8",$connect);
					mysql_query('SET NAMES utf8',$connect);
				}
				else{
					echo 'Nepořilo se vybrat databázi.';
				}
			}
			else{
				echo 'Nepodařilo se připojit k databázovemu serveru.';
			}
		}
		else{
			echo 'Nepodařilo se přihlašovací údaje k databázi.';
		}
	}
	return $connect;
}
?>
