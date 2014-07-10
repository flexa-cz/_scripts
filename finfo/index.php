<?php
$url='http://is.muni.cz/th/270212/prif_m/Hromkova-Diplomova_prace.pdf';

$fi = new finfo(FILEINFO_MIME);
$mime_type = $fi->buffer(file_get_contents($url));

//$mime_type_arr=explode(';', $mime_type);
//$mime_type=reset($mime_type_arr);

echo '<div style="background: red; color: #ffc; font-weight: bold; padding: .3em 1em; margin: 1em 0 0 0; font-size: 130%; font-family: Courier, monospace;">$mime_type</div><div style="border: 1px solid red; background: #ffc; padding: 1em; margin: 0 0 1em 0; overflow: auto; font-family: Courier, monospace;"><pre>';
var_export($mime_type);
echo '</pre><p style="font-size: 75%; color: red;"><b>file: </b>'.__FILE__.'<br><b>line: </b>'.__LINE__.'</p></div>';