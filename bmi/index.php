<?php
/**
 * test from helperu
 *
 * @since 11.4.12 8:49
 * @author Vlahovic
 */


// nezbytnosti
require_once('../core/include.php');
debuger::set_enable_report(true);
$core=new Core;
$core->site->setTitle('BMI');
$core->site->addHeader('<style type="text/css">'.debuger::get_css().'</style>');
$core->site->addHeader('<link rel="stylesheet" type="text/css" href="./range_slider.css" />');
$core->site->addHeader('<script src="./jquery.js" type="text/javascript"></script>');
$core->site->addHeader('<script src="./range_slider.class.js" type="text/javascript"></script>');
$core->site->addHeader('<script src="./bmi_calculator.class.js" type="text/javascript"></script>');
$core->site->addHeader('<script src="./app.js" type="text/javascript"></script>');

$core->site->addContent(_N.'<div id="bmi-calculator">');
$core->site->addContent(_N_T.'<input type="range" name="weight" min="5" max="250" value="70" step="1" />');
$core->site->addContent(_N_T.'<input type="range" name="height" min="30" max="200" value="170" step="1" />');
$core->site->addContent(_N.'</div>');

debuger::breakpoint('before html output');
echo $core->site;
?>