<?php
$from=150;
$to=500;

$key=date('Ymd');
$key=20121205;
$key=20121206;

srand($key);



echo $key;
echo '<br>';
echo rand($from,$to);