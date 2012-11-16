<?php
$cross=false;
$morse=false;
if(isset($_POST['cross'])){
	/* ************************************************************************ */
	/* kriz scitaci																															*/
	/* ************************************************************************ */
	if($_POST['cross']){
		$convert_table=array(
				'a'=>'11',
				'b'=>'12',
				'c'=>'13',
				'd'=>'21',
				'e'=>'22',
				'f'=>'23',
				'g'=>'31',
				'h'=>'32',
				'ch'=>'33',
				'i'=>'41',
				'j'=>'42',
				'k'=>'43',
				'l'=>'51',
				'm'=>'52',
				'n'=>'53',
				'o'=>'61',
				'p'=>'62',
				'q'=>'63',
				'r'=>'71',
				's'=>'72',
				't'=>'73',
				'u'=>'81',
				'v'=>'82',
				'w'=>'83',
				'x'=>'91',
				'y'=>'92',
				'z'=>'93',
				' '=>'',
		);
		$cross_arr=str_split($_POST['cross']);
		foreach($cross_arr as $key => $val){
			$cross_arr[$key]=$convert_table[$val];
		}
		$cross='('.str_replace(' +  + ', ') &times; (', implode(' + ', $cross_arr)).')';
	}

	/* ************************************************************************ */
	/* morzeova ip																															*/
	/* ************************************************************************ */
	if($_POST['morse']){
		$convert_table=array(
				'a'=>'.-',
				'b'=>'-...',
				'c'=>'-.-.',
				'd'=>'-..',
				'e'=>'.',
				'f'=>'..-.',
				'g'=>'--.',
				'h'=>'....',
				'ch'=>'----',
				'i'=>'..',
				'j'=>'.---',
				'k'=>'-.-',
				'l'=>'.-..',
				'm'=>'--',
				'n'=>'-.',
				'o'=>'---',
				'p'=>'.--.',
				'q'=>'--.-',
				'r'=>'.-.',
				's'=>'...',
				't'=>'-',
				'u'=>'..-',
				'v'=>'...-',
				'w'=>'.--',
				'x'=>'-..-',
				'y'=>'-.--',
				'z'=>'--..',
		);
		$words=explode(' ', $_POST['morse']);
		$encode_morse=array();
		foreach($words as $key => $val){
			$encode_words=array();
			$word=str_split($val);
			foreach($word as $w_val){
				$encode_letter=false;
				if(isset($convert_table[$w_val])){
					$letter=str_split($convert_table[$w_val]);
					foreach($letter as $l_val){
						if($l_val=='.'){
							$rand_arr=array(1,3,5,7,9);
						}
						else{
							$rand_arr=array(2,4,6,8);
						}
						$x=mt_rand(0,count($rand_arr)-1);
						$encode_letter.=$rand_arr[$x];
					}
				}
				$encode_words[]=str_pad($encode_letter, 4, 0, STR_PAD_RIGHT);
			}
			$encode_morse[]=implode('.',$encode_words);
		}
		$morse=implode('<br />', $encode_morse);
	}
}
?>
<p>zadavat bez diakritiky a interpunkce v malych znacich</p>
<form method="post" action="">
	<table>
		<tr>
			<td><label for="cross">kriz</label></td>
			<td><textarea cols="10"0 rows="10" name="cross" id="cross"><?=(!empty($_POST['cross']) ? $_POST['cross'] : false)?></textarea></td>
		</tr>
		<tr>
			<td><label for="morse">morseovka</label></td>
			<td><textarea cols="100" rows="10" name="morse" id="morse"><?=(!empty($_POST['morse']) ? $_POST['morse'] : false)?></textarea></td>
		</tr>
		<tr>
			<td></td>
			<td><input type="submit" value="sifruj" /></td>
		</tr>
	</table>
</form>
<?php
if($cross){
	echo '<h2>kriz</h2>';
	echo '<p>'.$cross.'</p><hr />';
}
if($morse){
	echo '<h2>morseovka</h2>';
	echo '<p>'.$morse.'</p><hr />';
}