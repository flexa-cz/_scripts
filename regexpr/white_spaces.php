<?php
$str='[9]     Způsob platby přes internet„Pro platby na internetu jsou v České republice využívány různé systémy.
	';
var_dump($str);

echo strlen($str);
echo mb_strlen($str);

$nbsp=preg_replace('~\x{00a0}~siu', ' ', $str);
echo '<div style="background: red; color: #ffc; font-weight: bold; padding: .3em 1em; margin: 1em 0 0 0; font-size: 130%; font-family: Courier, monospace;">$nbsp</div><div style="border: 1px solid red; background: #ffc; padding: 1em; margin: 0 0 1em 0; overflow: auto; font-family: Courier, monospace;"><pre>';
var_export($nbsp);
echo '</pre><p style="font-size: 75%; color: red;"><b>file: </b>'.__FILE__.'<br><b>line: </b>'.__LINE__.'</p></div>';

$ws = preg_replace('/\s\s*/', " ", $nbsp);
echo '<div style="background: red; color: #ffc; font-weight: bold; padding: .3em 1em; margin: 1em 0 0 0; font-size: 130%; font-family: Courier, monospace;">$ws</div><div style="border: 1px solid red; background: #ffc; padding: 1em; margin: 0 0 1em 0; overflow: auto; font-family: Courier, monospace;"><pre>';
var_export($ws);
echo '</pre><p style="font-size: 75%; color: red;"><b>file: </b>'.__FILE__.'<br><b>line: </b>'.__LINE__.'</p></div>';

$other = preg_replace('~[^a-z0-9 àáÁâãäåçćĆčČďĎèéÉěĚêëìíÍîïľĽňŇńŃñðòóÓôõöøŕŔřŘśŚšŠťŤùúÚûůŮýÝźŹžŽ\:\/\.\-\=\,\;\?\!]++~i', '', $ws);
echo '<div style="background: red; color: #ffc; font-weight: bold; padding: .3em 1em; margin: 1em 0 0 0; font-size: 130%; font-family: Courier, monospace;">$other</div><div style="border: 1px solid red; background: #ffc; padding: 1em; margin: 0 0 1em 0; overflow: auto; font-family: Courier, monospace;"><pre>';
var_export($other);
echo '</pre><p style="font-size: 75%; color: red;"><b>file: </b>'.__FILE__.'<br><b>line: </b>'.__LINE__.'</p></div>';

echo '<table border="1">';
for($x=0;$x<1000;$x++){
	$letter=mb_substr($other, $x,1);
	echo '<tr><td>'.$letter.'</td><td>'.bin2hex($letter).'</td></tr>';
}
echo '</table>';

//foreach($preg_replace as $letter){
//	var_dump(bin2hex($letter));
//}