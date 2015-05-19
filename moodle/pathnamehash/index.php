<?php
//$correct_pathnamehash='e916a8e36cb12e83a0035e0bbe69c2794f51be39';
//$contextid='1';
//$component='question';
//$filearea='questiontext';
//$itemid='936';
//$filepath='/';
//$filename='moodle_test_duplikace.jpg';

$correct_pathnamehash='48c1ea8b4a6240163c0839a93bfe8b5184cf7945';
$contextid='15192';
$component='mod_scorm';
$filearea='content';
$itemid='0';
$filepath='/shared/';
$filename='style.css';



$sha1=printPathnamehash($contextid, $component, $filearea, $itemid, $filepath, $filename);
if($correct_pathnamehash===$sha1){
	echo 'OK';
}
else{
	echo 'BAD';
}

/**
 * vraci moodle filepath - pro pouziti v db tabulce files
 * @param integer $contextid
 * @param string $component
 * @param string $filearea
 * @param integer $itemid
 * @param string $filepath
 * @param string $filename
 * @return string
 */
function printPathnamehash($contextid, $component, $filearea, $itemid, $filepath, $filename){
	$data=array(
			'contextid'=>$contextid,
			'component'=>$component,
			'filearea'=>$filearea,
			'itemid'=>$itemid,
			'filepath'=>trim($filepath,'/'),
			'filename'=>$filename,
			);
	if(!$data['filepath']){
		unset($data['filepath']);
	}
	$string='/'.implode('/',$data);
	return sha1($string);
}