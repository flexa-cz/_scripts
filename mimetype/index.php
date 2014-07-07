<?php


echo websiteFileMimetypeEasy('http://mihaicaulea.com/index.htm');

function websiteFileMimetypeEasy($url){
	$mimetype_settings=array(
				'text/plain'=>array('suffix'=>'.txt','download'=>false), // txt
				'text/html'=>array('suffix'=>'.htm','download'=>false), // html
				'application/pdf'=>array('suffix'=>'.pdf','download'=>true), // pdf
				'PDF/Adobe Acrobat'=>array('suffix'=>'.pdf','download'=>true), // pdf
				'application/vnd.openxmlformats-officedocument.wordprocessingml.document'=>array('suffix'=>'.docx','download'=>true), // docx
				'application/msword'=>array('suffix'=>'.doc','download'=>true), // doc
				'application/vnd.oasis.opendocument.text'=>array('suffix'=>'.odt','download'=>true), //odt
				);
	$return=false;
	$arr1=explode('?',$url);
	$arr2=explode('/',reset($arr1));
	$file_name=end($arr2);
	foreach($mimetype_settings as $mimetype_str => $mimetype_settings){
		if(strpos($file_name, $mimetype_settings['suffix'])!==false){
			$return=$mimetype_str;
			break;
		}
	}
	return $return;
}