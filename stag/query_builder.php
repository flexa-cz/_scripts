<?php
$department='UBO';
$subject='MT';
$year=2014;




$attributes=array('department','subject','year','ver','template');
$attributes_join=array();
$columns=array();
foreach($attributes as $attr){
	$attributes_join[]="LEFT JOIN {course_attributes} `".$attr."` ON `".$attr."`.course_id=`course`.`id` AND `".$attr."`.`name`='".$attr."'";
	$columns[]="`".$attr."`.`value` `".$attr."`";
}
$query="
	SELECT
		`course`.id,".implode(',',$columns)."
	FROM {course} course
	".implode("\r\n\t\t\t\t\t", $attributes_join)."
	WHERE
		`department`.`value`='".$department."' AND
		`subject`.`value`='".$subject."' AND
		`year`.`value`<='".$year."'
	ORDER BY `year`.`value` DESC, `template`.`value` DESC, `ver`.`value` DESC
	LIMIT 1
	;";




echo '<div style="background: red; color: #ffc; font-weight: bold; padding: .3em 1em; margin: 1em 0 0 0; font-size: 130%; font-family: Courier, monospace;">$query</div><div style="border: 1px solid red; background: #ffc; padding: 1em; margin: 0 0 1em 0; overflow: auto; font-family: Courier, monospace;"><pre>';
var_export($query);
echo '</pre><p style="font-size: 75%; color: red;"><b>file: </b>'.__FILE__.'<br><b>line: </b>'.__LINE__.'</p></div>';