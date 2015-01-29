<?php
echo '
	<form action="" method="post">
	<input type="submit" name="do_job" value="upravit json soubory" />
	</form>
	';
if(!empty($_POST['do_job'])){
	read_dirs();
}

function read_dirs(){
	$root=str_replace(array('\\','/index.php'), array('/',false), __FILE__);
	$dirs=array(
			array(
					'source'=>$root.'/source/20150127_060001',
					'target'=>$root.'/target/20150127_060001',
			),
			array(
					'source'=>$root.'/source/20150128_060001',
					'target'=>$root.'/target/20150128_060001',
			),
	);
	foreach($dirs as $dir){
		read_dir($dir['source'],$dir['target']);
	}
}

function read_dir($source_dir, $target_dir){
	$handle=opendir($source_dir);
	if($handle){
		/* This is the correct way to loop over the directory. */
		$ok=0;
		$bad=0;
		while(false !== ($file_addr=readdir($handle))){
			if(strpos($file_addr, '.json') !== false){
				$return=parse_json_file($source_dir.'/'.$file_addr, $target_dir.'/'.$file_addr);
				if($return){
					$ok++;
				}
				else{
					$bad++;
				}
			}
		}
		closedir($handle);
		echo $source_dir.' > '.$target_dir.' > ok: '.$ok.'; bad: '.$bad.'<br>';
	}
}

function parse_json_file($source_file_addr, $target_file_addr){
	$source_content=file_get_contents($source_file_addr);
	$target_content=str_replace('","', "\",\r\n\"", $source_content);
	$file = fopen($target_file_addr,"w");
	$return=fwrite($file,$target_content);
	fclose($file);
	return $return;
}