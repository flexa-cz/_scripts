<?php
$count=0;
$str='<br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br />';
$replace=str_replace('<br /><br /><br />','<br /><br />',$str,$count);
var_dump($str);
var_dump($replace);
var_dump($count);


$replace=preg_replace('/(\<br \/\>\<br \/\>)+/', '<br />', $str);
var_dump($replace);