<?php
echo $temp_addr=sys_get_temp_dir();

$temp =fopen($temp_addr.'/test.tmp', 'w');
if($temp){
	for($i=0;$i<30;$i++){
		fwrite($temp, "writing to tempfile\r\n");
		sleep(1);
	}
	fclose($temp); // this removes the file
}
else{
	echo 'fail';
}
?>