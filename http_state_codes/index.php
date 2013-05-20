<?php
header("HTTP/1.0 404 Not Found");
header("Status: 404 Not Found");
$url='http://localhost/_scripts/http_state_codes/state.php';
$headers = @get_headers($url);
?>
<h2>externi http stranka</h2>
<h3>url</h3>
<p>
	<?=$url?>
</p>

<h3>hlavicky</h3>
<?var_dump($headers);?>



<?php

?>
<h2>tato strana</h2>
<?php
var_dump(headers_list());