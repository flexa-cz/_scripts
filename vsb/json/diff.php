<?php
$root=str_replace(array('\\','/diff.php'), array('/',false), __FILE__);

$dirs=array(
	$root.'/source/20150128_060001',
	$root.'/source/20150127_060001',
);

$files=print_files_json_parsed_data($dirs);
compare_files(reset($files), end($files));

function compare_files($source_files, $target_files){
	foreach($source_files as $source_file_name => $source_file_arr){
		$target_file_arr=(!empty($target_files[$source_file_name]) ? $target_files[$source_file_name] : false);
		if($target_file_arr){
			if(strpos($source_file_name,'indexCourse')!==false){
//				compare_arr_1($source_file_name, $source_file_arr[1], $target_file_arr[1]);
			}
			elseif(strpos($source_file_name,'_people_')!==false){
				$prepared_source_arr=prepare_person_arr($source_file_arr);
				$prepared_target_arr=prepare_person_arr($target_file_arr);
//				compare_arr_1($source_file_name, $prepared_source_arr, $prepared_target_arr);
			}
			elseif(strpos($source_file_name,'subjectversions_teachers')!==false){
				$prepared_source_arr=prepare_subjectversions_arr($source_file_arr);
				$prepared_target_arr=prepare_subjectversions_arr($target_file_arr);
				compare_arr_1($source_file_name, $prepared_source_arr, $prepared_target_arr, true);
			}
			elseif(strpos($source_file_name,'subjectversions_studyyearobligations')!==false){
				$prepared_source_arr=prepare_subjectversions_arr($source_file_arr);
				$prepared_target_arr=prepare_subjectversions_arr($target_file_arr);
				compare_arr_2($source_file_name, $prepared_source_arr, $prepared_target_arr, true);
			}
			else{
				echo '<p>unknown method for file '.$source_file_name.'...</p>';
			}
		}
	}
}

function prepare_person_arr($arr){
	$return=array();
	foreach($arr as $data){
		unset($data['degreeBefore'],$data['degreeAfter']);
		$return[$data['personId']]=$data;
	}
	return $return;
}

function prepare_subjectversions_arr($arr){
	$return=array();
	foreach($arr as $data){
		$return[$data['subjectVersionId']]=$data;
	}
	return $return;
}

function compare_arr_1($file_name, $source_arr, $target_arr, $show_changes=false){
	echo '<h2>'.$file_name.'</h2>';
	$cnt=0;
	foreach($source_arr as $s_key => $s_value){
		if(!empty($target_arr[$s_key])){
			if($s_value!==$target_arr[$s_key] && $show_changes){
				echo '<h3>different content at "'.$s_key.'"</h3>';
				echo '<h4>source</h4>';
				echo '<pre>'.var_export($s_value, true).'</pre>';
				echo '<h4>target</h4>';
				echo '<pre>'.var_export($target_arr[$s_key], true).'</pre>';
				$cnt++;
			}
		}
		else{
			echo '<h3>unexisting key "'.$s_key.'"</h3>';
			echo '<pre>'.var_export($s_value, true).'</pre>';
			$cnt++;
		}
	}
	echo '<p>changes at "'.$file_name.'": '.$cnt.'</p>';
}


function compare_arr_2($file_name, $source_arr, $target_arr, $show_changes=false){
	echo '<h2>'.$file_name.'</h2>';
	$cnt=0;
	$doesn_existst=array();
	$td_style=' style="border: 1px solid black; padding: 3px 5px;"';
	foreach($source_arr as $s_key => $s_value){
		if(!empty($target_arr[$s_key])){
			if($s_value['studyYearObligations']!==$target_arr[$s_key]['studyYearObligations'] && $show_changes){
				$source_obligations=prepare_obligations_arr($s_value['studyYearObligations']);
				$target_obligations=prepare_obligations_arr($target_arr[$s_key]['studyYearObligations']);
				$array_diff = obligations_diff($source_obligations, $target_obligations);
				if(!empty($array_diff)){
					echo '<h3>different content at "'.$s_key.'"</h3>';
					echo '<h4>array_diff()</h4>';
					echo '<pre>'.var_export($array_diff, true).'</pre>';
					$cnt++;
					// schovam do jednorozmerneho pole ty co jsou urcite navic
					foreach($array_diff as $arr){
						if($arr['result']==='doesnt exist at target arr'){
							$doesn_existst[]='<tr><td'.$td_style.'>'.$s_key.'</td><td'.$td_style.'>'.$arr['personId'].'</td></tr>';
						}
					}
				}
			}
		}
		else{
			echo '<h3>unexisting key "'.$s_key.'"</h3>';
			echo '<pre>'.var_export($s_value, true).'</pre>';
			$cnt++;
		}
	}
	if(!empty($doesn_existst)){
		echo '<h3>doesnt exist at studyYearObligations</h3>';
		echo '<table style="border: 2px solid black; border-collapse: collapse;">';
		echo '<tr><th'.$td_style.'>subjectVersionId</th><th'.$td_style.'>personId</th></tr>';
		echo implode("\r\n\n", $doesn_existst);
		echo '</table>';
	}
	echo '<p>changes at "'.$file_name.'": '.$cnt.'</p>';
}

function obligations_diff($source_arr, $target_arr){
	$return=array();
	foreach($source_arr as $person_id => $plan_id){
		if(!isset($target_arr[$person_id])){
			$return[]=array(
					'personId'=>$person_id,
					'result'=>'doesnt exist at target arr'
							);
		}
		elseif($plan_id!==$target_arr[$person_id]){
			$return[]=array(
					'personId'=>$person_id,
					'result'=>'different studyPlanId (source: '.$plan_id.', target: '.$target_arr[$person_id].')'
							);
		}
		// toto me nazajima - proste ted je tam student zapsany...
//		foreach($target_arr as $person_id => $plan_id){
//			if(!isset($source_arr[$person_id])){
//				$return[]=array(
//						'personId'=>$person_id,
//						'result'=>'doesnt exist at source arr'
//								);
//			}
//		}
	}
	return $return;
}

function prepare_obligations_arr($arr){
	$return=array();
	foreach($arr as $data){
		if(empty($data['studyPlanId'])){
			$return[$data['personId']]=0;
			continue;
		}
		$return[$data['personId']]=$data['studyPlanId'];
	}
	return $return;
}

function print_files_json_parsed_data($dirs){
	$return=array();
	foreach($dirs as $dir){
		$json_files=print_json_files($dir);
		$return[$dir]=parse_json_files($dir, $json_files);
	}
	return $return;
}

function parse_json_files($path, $files){
	$return=array();
	foreach($files as $file){
		$content=file_get_contents($path.'/'.$file);
		$return[$file]=json_decode($content, true);
	}
	return $return;
}

function print_json_files($dir){
	$return=array();
	$handle=opendir($dir);
	if($handle){
		while(false !== ($file_addr=readdir($handle))){
			if(strpos($file_addr, '.json') !== false){
				$return[]=$file_addr;
			}
		}
		closedir($handle);
	}
	return $return;
}
