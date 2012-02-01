<?php
// nezbytnosti
require_once('../core/include.php');

// posklada a vrati stranu
$site=new site;
// posklada testovaci odstavce
$text='Příliš žluťoučký kůň úpěl ďábelské ódy.';
for($x=1;$x<=10;$x++){
	$site->addHeader('<link rel="stylesheet" type="text/css" href="../font_embeding/font'.$x.'.css" title="font'.$x.'" media="screen" />');
	$site->addContent(_N_T.'<p class="Font'.$x.'">'.$x.') '.$text.'</p>');
}
// vystup do prohlizece
echo $site;
?>
