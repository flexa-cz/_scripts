<?php
$dir=str_replace('\\','/',__DIR__).'/';
$dir='./';
echo $dir;
echo exec('convert "'.$dir.'file2[0].pdf" -colorspace RGB -geometry 200 "'.$dir.'thumb.png"');

//$im = new imagick('file.pdf[0]');
//$im->setImageFormat( "jpg" );
//header( "Content-Type: image/jpeg" );
//echo $im;
?>