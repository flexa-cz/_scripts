<?php
$username="asdfasdf\asdasdfsa";
echo $username.'<br>';
$username = substr($username, strpos($username, '\\')+1);
echo $username.'<br>';