<?php
$count=0;
$str='<br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br />';
$replace=str_replace('<br /><br /><br />','<br /><br />',$str,$count);
var_dump($str);
var_dump($replace);
var_dump($count);


$replace=preg_replace('/(\<br \/\>\<br \/\>)+/', '<br />', $str);
var_dump($replace);

echo '<hr />';

$str='HTTP/1.0 302 Found';
var_dump($str);
//var_dump(settype($str,'integer'));
var_dump(parseInt($str));

function parseInt($string) {
	$pattern=array(
				'/1.0/',
				'/1.1/',
				'/\D/',
		);
	return preg_replace($pattern, '*', $string);
}