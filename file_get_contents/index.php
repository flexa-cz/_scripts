<?php
$url='http://cs.wikipedia.org/wiki/Pa%25C5%2599ez';

$utf=iconv('ISO-8859-2','UTF-8',$url);

echo '<div style="background: red; color: #ffc; font-weight: bold; padding: .3em 1em; margin: 1em 0 0 0; font-size: 130%; font-family: Courier, monospace;">$utf</div><div style="border: 1px solid red; background: #ffc; padding: 1em; margin: 0 0 1em 0; overflow: auto; font-family: Courier, monospace;"><pre>';
var_export($utf);
echo '</pre><p style="font-size: 75%; color: red;"><b>file: </b>'.__FILE__.'<br><b>line: </b>'.__LINE__.'</p></div>';

//$enc_url=urlencode($url);
//echo file_get_contents($enc_url);
//
//$dec=hexdec('%25C5%2599');
//echo dechex($dec);

//echo '<hr>';
//$url='http://cs.wikipedia.org/wiki/Pařez';
//echo file_get_contents($url);