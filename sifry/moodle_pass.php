<?php
include_once('./password.lib.php');
$pass='pass';
$algo=1;
$options_1=array('cost' => 4);
$options_2=array();

$version_5=password_hash($pass, $algo, $options_1);
echo '<div class="flexa-debug" style="background: red; color: #ffc; font-weight: bold; padding: .3em 1em; margin: 1em 0 0 0; font-size: 130%; font-family: Courier, monospace;">$version_5</div><div style="border: 1px solid red; background: #ffc; padding: 1em; margin: 0 0 1em 0; overflow: auto; font-family: Courier, monospace;"><pre>'; var_export($version_5);echo '</pre><p style="font-size: 75%; color: red;">'; echo '<em># <b>file:</b> '.__FILE__.'; <b>line:</b> '.__LINE__.(__FUNCTION__ ? '; <b>function:</b> ' : false).(__CLASS__ ? __CLASS__.'::' : false).(__FUNCTION__ ? __FUNCTION__ : false).'</em>';foreach(debug_backtrace() as $values){echo (!empty($values['file']) && !empty($values['line']) ? '<br><em># <b>file:</b> '.$values['file'].'; <b>line:</b> '.$values['line'].'; <b>function:</b> '.(!empty($values['class']) ? $values['class'].'::' : false).$values['function'].'</em>' : false);}echo '</p></div>';

$version_6=password_hash($pass, $algo, $options_2);
echo '<div class="flexa-debug" style="background: red; color: #ffc; font-weight: bold; padding: .3em 1em; margin: 1em 0 0 0; font-size: 130%; font-family: Courier, monospace;">$version_6</div><div style="border: 1px solid red; background: #ffc; padding: 1em; margin: 0 0 1em 0; overflow: auto; font-family: Courier, monospace;"><pre>'; var_export($version_6);echo '</pre><p style="font-size: 75%; color: red;">'; echo '<em># <b>file:</b> '.__FILE__.'; <b>line:</b> '.__LINE__.(__FUNCTION__ ? '; <b>function:</b> ' : false).(__CLASS__ ? __CLASS__.'::' : false).(__FUNCTION__ ? __FUNCTION__ : false).'</em>';foreach(debug_backtrace() as $values){echo (!empty($values['file']) && !empty($values['line']) ? '<br><em># <b>file:</b> '.$values['file'].'; <b>line:</b> '.$values['line'].'; <b>function:</b> '.(!empty($values['class']) ? $values['class'].'::' : false).$values['function'].'</em>' : false);}echo '</p></div>';