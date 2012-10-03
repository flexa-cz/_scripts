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

$core->site->addContent('
<form>
	<div style="width: 700px; position: relative; left: 200px; border: 1px solid #aaa; background: #eee;height: 400px;">
		<div id="bmi-calculator">
			<input type="range" name="weight" min="5" max="250" value="80" step="1" title=" kg" />
			<input type="range" name="height" min="30" max="220" value="180" step="1" title=" cm" />
			<input type="submit" style="clear:both; display:block;" name="bmi_calculate" class="bmi-calculate" value="spocitat" />
			<p class="bmi-score"></>
		</div>
	</div>
</form>');

debuger::breakpoint('before html output');
echo $core->site;
?>