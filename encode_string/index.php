<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="cs" lang="cs">
	<head>
		<meta http-equiv="content-type" content="text/html; charset=utf-8" />
		<meta http-equiv="Content-language" content="cs" />
		<meta http-equiv="imagetoolbar" content="no" />
		<title>encode string</title>
		<style type="text/css">
			table{border-collapse: collapse; border: 2px solid black; margin: 1em 0;}
			th,td{border: 1px solid black; text-align: left; padding: .2em .5em;}
			th{background: #eee;}
		</style>
	</head>
	<body>
		<form action="" method="post">
			<label>
				<input type="password" name="string" />
				retezec k prekodovani
			</label>
			<br />
			<input type="submit" />
		</form>
<?php
if(!empty($_POST)){
	echo '<table>';
	echo '<tr><th>plain</th><td>'.$_POST['string'].'</td></tr>';
	echo '<tr><th>MD5</th><td>'.md5($_POST['string']).'</td></tr>';
	echo '<tr><th>SHA1</th><td>'.sha1($_POST['string']).'</td></tr>';
	echo '<tr><th>SHA512</th><td>'.hash('SHA512',$_POST['string']).'</td></tr>';
	echo '<tr><th>SHA256</th><td>'.hash('SHA256',$_POST['string']).'</td></tr>';
	echo '</table>';
}
// pp cms - user admin, hash 8440fcf91048933a68af9bac28fa4f6a
?>
	</body>
</html>