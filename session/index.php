<?php
echo '1.<br>';
session_destroy();
echo '2.<br>';
if(!empty($_SESSION)){
	session_destroy();
}
echo '3.<br>';
session_destroy();