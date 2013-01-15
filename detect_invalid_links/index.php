<?php
/**
 * najde a oznami nefukni odkazy v prispevcich
 * primarne pro ucely spektrumzdravi.cz
 * jde samozrejme upravit a prizpusobit jinym vstupnim
 */

// nezbytnosti
require_once('../core/include.php');
debuger::set_enable_report(true);
$core=new Core;
$return=false;
$core->site->setTitle('nalezeni nefunkcnich odkazu');
$core->site->addHeader('<style type="text/css">'.debuger::get_css().'</style>');

$core->db->setMysqlDatabase('propeople_cms_backup')->connect();

// formular s vyberem aliasu a limitu
$form='<form method="post" action="">';
$form.='<table>';

$form.='<tr>';
$form.='<td><label for="alias">Server alias</label></td>';
$form.='<td><input type="text" name="alias" id="alias" value="spektrumzdravi"></td>';
$form.='</tr>';

$form.='<tr>';
$form.='<td><label for="url">Server URL</label></td>';
$form.='<td><input type="text" name="url" id="url" value="http://www.new.spektrumzdravi.cz"></td>';
$form.='</tr>';

$form.='<tr>';
$form.='<td><label for="limit">Limit</label></td>';
$form.='<td><input type="text" name="limit" id="limit" value="10"></td>';
$form.='</tr>';

$form.='</table>';
$form.='<input type="submit" value="Najit nefunkcni linky">';
$form.='</form>';
$core->site->addContent($form);

//$headers = @get_headers('http://www.spektrumzdravi.lh/zahrada-v-breznu/tst');
//var_dump($headers);

if(isset($_POST['alias']) && $_POST['alias'] && isset($_POST['url']) && $_POST['url']){
	// muze trvat hooodne dlouho...
	set_time_limit(0);

	$url=mysql_real_escape_string($_POST['url']);
	$alias=mysql_real_escape_string($_POST['alias']);
	$limit=(intval($_POST['limit']) ? 'LIMIT '.intval($_POST['limit']) : false);

	$core->db->query("SET @root_idr=(SELECT `id_relations` FROM `settings` WHERE `name`='alias' AND `value` LIKE '%".$alias."%')");
	$core->db->query("SET @root_lft=(SELECT `lft` FROM `relations` WHERE `id`=@root_idr)");
	$core->db->query("SET @root_rgt=(SELECT `rgt` FROM `relations` WHERE `id`=@root_idr)");

	$query="
		SELECT
			sp1.id AS `id_sites`,
			sp2.content AS `title`,
			sp3.content AS `url`,
			sp1.content AS `text`
		FROM sites_parts AS sp1
		LEFT JOIN relations ON relations.`id_sites`=sp1.`id_sites`
		JOIN sites_parts AS sp2 ON sp1.id_sites=sp2.id_sites AND sp2.part='title'
		JOIN sites_parts AS sp3 ON sp1.id_sites=sp3.id_sites AND sp3.part='url'
		WHERE
			`relations`.`lft`>=@root_lft AND
			`relations`.`rgt`<=@root_rgt AND
			sp1.part='text' AND
			(
				(sp1.content LIKE '%<a%' OR sp1.content LIKE '%<A%') OR
				(sp1.content LIKE '%<img%' OR sp1.content LIKE '%<IMG%')
			)
		GROUP BY sp1.id
		".$limit;


	$all_sites=0;
	$error_sites=0;
	foreach($core->db->query($query)->getRows() as $item){
		$all_sites++;
		$invalid_links=detect_invalid_links($item['text'],$url);
	//	var_dump($invalid_links);
		if($invalid_links['resume']=='invalid'){
			$error_sites++;
			$item['text']=mb_substr(strip_tags($item['text']),0,100).'...';
			$item['url']='<a href="'.$url.'/'.$item['url'].'">'.$item['url'].'</a>';
			$item['links']=$invalid_links['invalid'];
			$core->table->setRow($item);
		}
	}
	$return.=$core->table;

	$core->site->addContent('<p><strong>all sites:</strong> '.$all_sites.'</p>');
	$core->site->addContent('<p><strong>error sites:</strong> '.$error_sites.'</p>');
	$core->site->addContent($return);
}

echo $core->site;


/**
 * samotna detekce nefunkcnich odkazu
 * @param string $html
 * @param string $server_url
 * @return array
 */
function detect_invalid_links($html,$server_url){
	$return=array('resume'=>'valid');
	$invalid=array();
	// osetreni kodovani - predtim to rozbilo ceske znaky
	$string = mb_convert_encoding(mb_convert_encoding($html, 'utf-8', mb_detect_encoding($html)), 'html-entities', 'utf-8');
	$doc = new DOMDocument('1.0', 'UTF-8');
	@$doc->loadHTML($string);
	// obrazky
	$i=0;
	while(is_object($item = $doc->getElementsByTagName("img")->item($i))){
		$src=$item->getAttribute('src');
		$links[]=$src;
		$i++;
	}
	// linky
	$x=0;
	while(is_object($item = $doc->getElementsByTagName("a")->item($x))){
		$links[]=$item->getAttribute('href');
		$x++;
	}
	// kontrola
	foreach($links as $link){
		$url=(strpos($link,'http')===false && strpos($link,'www')===false ? $server_url : false).$link;
		$http_status=get_http_status($url);
		if(!is_valid_status($http_status)){
			$return['resume']='invalid';
			$invalid[]='<li><a href="'.$url.'">'.$url.'</a><br />'.$http_status.'</li>';
		}
	}

	// resume
	$return['links']=$links;
	$return['invalid']=(!empty($invalid) ? '<ul>'.implode(false,$invalid).'</ul>' : false);
	return $return;
}

function get_http_status($url) {
	$headers = @get_headers($url);
	return $headers[0];
}

function is_valid_status($http_status){
	$valid_status=array('200','301');
	$return=false;
	foreach($valid_status as $status_code){
		if (strpos($http_status,$status_code)!==false){
			$return=true;
			break;
		}
	}
	return $return;
}