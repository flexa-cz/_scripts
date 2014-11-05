<?php
$json=file_get_contents('./106_indexCourse.json');
$data=json_decode($json);
$count=0;
foreach((array)$data[1] as $key => $value){
	if(strpos($key, '106_')===0){
		$count++;
	}
}
echo '<h3>'.$count.'</h3>';

echo '<pre>';
var_export($data[1]);
echo '</pre>';
echo count($data[1]);