<?php
//$ldap_json=file_get_contents('./ldap.json');
//echo '<div class="flexa-debug" style="background: red; color: #ffc; font-weight: bold; padding: .3em 1em; margin: 1em 0 0 0; font-size: 130%; font-family: Courier, monospace;">$ldap_json</div><div style="border: 1px solid red; background: #ffc; padding: 1em; margin: 0 0 1em 0; overflow: auto; font-family: Courier, monospace;"><pre>'; var_export($ldap_json);echo '</pre><p style="font-size: 75%; color: red;">'; echo '<em># <b>file:</b> '.__FILE__.'; <b>line:</b> '.__LINE__.(__FUNCTION__ ? '; <b>function:</b> ' : false).(__CLASS__ ? __CLASS__.'::' : false).(__FUNCTION__ ? __FUNCTION__ : false).'</em>';foreach(debug_backtrace() as $values){echo (!empty($values['file']) && !empty($values['line']) ? '<br><em># <b>file:</b> '.$values['file'].'; <b>line:</b> '.$values['line'].'; <b>function:</b> '.(!empty($values['class']) ? $values['class'].'::' : false).$values['function'].'</em>' : false);}echo '</p></div>';
//$ldap_array=json_decode($ldap_json, true);
//echo '<div class="flexa-debug" style="background: red; color: #ffc; font-weight: bold; padding: .3em 1em; margin: 1em 0 0 0; font-size: 130%; font-family: Courier, monospace;">$ldap_array</div><div style="border: 1px solid red; background: #ffc; padding: 1em; margin: 0 0 1em 0; overflow: auto; font-family: Courier, monospace;"><pre>'; var_export($ldap_array);echo '</pre><p style="font-size: 75%; color: red;">'; echo '<em># <b>file:</b> '.__FILE__.'; <b>line:</b> '.__LINE__.(__FUNCTION__ ? '; <b>function:</b> ' : false).(__CLASS__ ? __CLASS__.'::' : false).(__FUNCTION__ ? __FUNCTION__ : false).'</em>';foreach(debug_backtrace() as $values){echo (!empty($values['file']) && !empty($values['line']) ? '<br><em># <b>file:</b> '.$values['file'].'; <b>line:</b> '.$values['line'].'; <b>function:</b> '.(!empty($values['class']) ? $values['class'].'::' : false).$values['function'].'</em>' : false);}echo '</p></div>';

$moo_json=file_get_contents('./107_umoo.json');
//echo '<div class="flexa-debug" style="background: red; color: #ffc; font-weight: bold; padding: .3em 1em; margin: 1em 0 0 0; font-size: 130%; font-family: Courier, monospace;">$moo_json</div><div style="border: 1px solid red; background: #ffc; padding: 1em; margin: 0 0 1em 0; overflow: auto; font-family: Courier, monospace;"><pre>'; var_export($moo_json);echo '</pre><p style="font-size: 75%; color: red;">'; echo '<em># <b>file:</b> '.__FILE__.'; <b>line:</b> '.__LINE__.(__FUNCTION__ ? '; <b>function:</b> ' : false).(__CLASS__ ? __CLASS__.'::' : false).(__FUNCTION__ ? __FUNCTION__ : false).'</em>';foreach(debug_backtrace() as $values){echo (!empty($values['file']) && !empty($values['line']) ? '<br><em># <b>file:</b> '.$values['file'].'; <b>line:</b> '.$values['line'].'; <b>function:</b> '.(!empty($values['class']) ? $values['class'].'::' : false).$values['function'].'</em>' : false);}echo '</p></div>';
$moo_array=json_decode($moo_json, true);
echo '<div class="flexa-debug" style="background: red; color: #ffc; font-weight: bold; padding: .3em 1em; margin: 1em 0 0 0; font-size: 130%; font-family: Courier, monospace;">$moo_array</div><div style="border: 1px solid red; background: #ffc; padding: 1em; margin: 0 0 1em 0; overflow: auto; font-family: Courier, monospace;"><pre>'; var_export($moo_array);echo '</pre><p style="font-size: 75%; color: red;">'; echo '<em># <b>file:</b> '.__FILE__.'; <b>line:</b> '.__LINE__.(__FUNCTION__ ? '; <b>function:</b> ' : false).(__CLASS__ ? __CLASS__.'::' : false).(__FUNCTION__ ? __FUNCTION__ : false).'</em>';foreach(debug_backtrace() as $values){echo (!empty($values['file']) && !empty($values['line']) ? '<br><em># <b>file:</b> '.$values['file'].'; <b>line:</b> '.$values['line'].'; <b>function:</b> '.(!empty($values['class']) ? $values['class'].'::' : false).$values['function'].'</em>' : false);}echo '</p></div>';