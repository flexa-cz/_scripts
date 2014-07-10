<form action='' method='post'>
	<table>
		<tr>
			<th>server</th>
			<td><input type='text' name='server' /></td>
		</tr>
		<tr>
			<th>user</th>
			<td><input type='text' name='user' /></td>
		</tr>
		<tr>
			<th>password</th>
			<td><input type='password' name='password' /></td>
		</tr>
		<tr>
			<td colspan="2">
				<input type='submit' />
			</td>
		</tr>
	</table>
</form>
<?php
if(!empty($_POST)){
	include_once('./ftp.class.php');
	$ftp=new Ftp($_POST['server'],$_POST['user'],$_POST['password']);

	$ftp->directoryChangeCurent('/www/public');
	var_dump($ftp->directoryPrintStructure());

	echo '<div style="background: red; color: #ffc; font-weight: bold; padding: .3em 1em; margin: 1em 0 0 0; font-size: 130%; font-family: Courier, monospace;">$ftp</div><div style="border: 1px solid red; background: #ffc; padding: 1em; margin: 0 0 1em 0; overflow: auto; font-family: Courier, monospace;"><pre>';
	var_export($ftp);
	echo '</pre><p style="font-size: 75%; color: red;"><b>file: </b>'.__FILE__.'<br><b>line: </b>'.__LINE__.'</p></div>';

	$ftp->closeConnection();
}