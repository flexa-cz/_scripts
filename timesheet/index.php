<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="cs" lang="cs">
<head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
</head>
	<form action="" method="post">
		<input type="submit" name="import" value="importovat" />
	</form>
<?php
require_once('./import_csv.class.php');
require_once('./timesheet_to_excel.class.php');

if(isset($_POST['import'])){
	$import_oriflame_courses=new TimesheetToExcel('./timesheet.csv');
	$import_oriflame_courses->setValueSurround('')->import();
}
exit('<br /><b>file: </b>'.__FILE__.'<br><b>line: </b>'.__LINE__.'<br><b>function: </b>exit()');