<?php
$a=array(
		32.4,
		12,
		array(
				9.4,
				10.9,
				array(
						45.0,
						3245.8,
				)
		)
);

$s=array_sum_integer_values($a);
echo '<div class="flexa-debug" style="background: red; color: #ffc; font-weight: bold; padding: .3em 1em; margin: 1em 0 0 0; font-size: 130%; font-family: Courier, monospace;">$s</div><div style="border: 1px solid red; background: #ffc; padding: 1em; margin: 0 0 1em 0; overflow: auto; font-family: Courier, monospace;"><pre>'; var_export($s);echo '</pre><p style="font-size: 75%; color: red;">'; echo '<em># <b>file:</b> '.__FILE__.'; <b>line:</b> '.__LINE__.(__FUNCTION__ ? '; <b>function:</b> ' : false).(__CLASS__ ? __CLASS__.'::' : false).(__FUNCTION__ ? __FUNCTION__ : false).'</em>';foreach(debug_backtrace() as $values){echo (!empty($values['file']) && !empty($values['line']) ? '<br><em># <b>file:</b> '.$values['file'].'; <b>line:</b> '.$values['line'].'; <b>function:</b> '.(!empty($values['class']) ? $values['class'].'::' : false).$values['function'].'</em>' : false);}echo '</p></div>';

$a2=array(
		'1'=>'string',
		1=>'integer',
);
//unset($a2['1']);
echo '<div class="flexa-debug" style="background: red; color: #ffc; font-weight: bold; padding: .3em 1em; margin: 1em 0 0 0; font-size: 130%; font-family: Courier, monospace;">$a2</div><div style="border: 1px solid red; background: #ffc; padding: 1em; margin: 0 0 1em 0; overflow: auto; font-family: Courier, monospace;"><pre>'; var_export($a2);echo '</pre><p style="font-size: 75%; color: red;">'; echo '<em># <b>file:</b> '.__FILE__.'; <b>line:</b> '.__LINE__.(__FUNCTION__ ? '; <b>function:</b> ' : false).(__CLASS__ ? __CLASS__.'::' : false).(__FUNCTION__ ? __FUNCTION__ : false).'</em>';foreach(debug_backtrace() as $values){echo (!empty($values['file']) && !empty($values['line']) ? '<br><em># <b>file:</b> '.$values['file'].'; <b>line:</b> '.$values['line'].'; <b>function:</b> '.(!empty($values['class']) ? $values['class'].'::' : false).$values['function'].'</em>' : false);}echo '</p></div>';

$glob=array('aa','bb','cc');
echo '<div class="flexa-debug" style="background: red; color: #ffc; font-weight: bold; padding: .3em 1em; margin: 1em 0 0 0; font-size: 130%; font-family: Courier, monospace;">$glob</div><div style="border: 1px solid red; background: #ffc; padding: 1em; margin: 0 0 1em 0; overflow: auto; font-family: Courier, monospace;"><pre>'; var_export($glob);echo '</pre><p style="font-size: 75%; color: red;">'; echo '<em># <b>file:</b> '.__FILE__.'; <b>line:</b> '.__LINE__.(__FUNCTION__ ? '; <b>function:</b> ' : false).(__CLASS__ ? __CLASS__.'::' : false).(__FUNCTION__ ? __FUNCTION__ : false).'</em>';foreach(debug_backtrace() as $values){echo (!empty($values['file']) && !empty($values['line']) ? '<br><em># <b>file:</b> '.$values['file'].'; <b>line:</b> '.$values['line'].'; <b>function:</b> '.(!empty($values['class']) ? $values['class'].'::' : false).$values['function'].'</em>' : false);}echo '</p></div>';
glob_func();
print_glob($glob);

function print_glob($glob){
	echo '<div class="flexa-debug" style="background: red; color: #ffc; font-weight: bold; padding: .3em 1em; margin: 1em 0 0 0; font-size: 130%; font-family: Courier, monospace;">$glob</div><div style="border: 1px solid red; background: #ffc; padding: 1em; margin: 0 0 1em 0; overflow: auto; font-family: Courier, monospace;"><pre>'; var_export($glob);echo '</pre><p style="font-size: 75%; color: red;">'; echo '<em># <b>file:</b> '.__FILE__.'; <b>line:</b> '.__LINE__.(__FUNCTION__ ? '; <b>function:</b> ' : false).(__CLASS__ ? __CLASS__.'::' : false).(__FUNCTION__ ? __FUNCTION__ : false).'</em>';foreach(debug_backtrace() as $values){echo (!empty($values['file']) && !empty($values['line']) ? '<br><em># <b>file:</b> '.$values['file'].'; <b>line:</b> '.$values['line'].'; <b>function:</b> '.(!empty($values['class']) ? $values['class'].'::' : false).$values['function'].'</em>' : false);}echo '</p></div>';
}

function glob_func(){
	global $glob;
	unset($glob['0']);
}

function array_sum_integer_values(array $array){
	$count=0;
	foreach($array as $value){
		if(is_int($value) || is_float($value)){
			$count+=$value;
		}
		elseif(is_array($value)){
			$count+=array_sum_integer_values($value);
		}
	}
	return $count;
}