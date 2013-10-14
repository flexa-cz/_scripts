<?php
//$search='Berliet Skočit n : Navigac , Hledání Závodní vůz Berliet
//na čtvrtém místě v cíli soutěže Targa Flori , 1. května 190 , řidič Jean Porporato Berliet Cabriolet Berliet
//byla francouzská automobilka vyrábějící osobn , nákladní a užitkové automobil. Firma jako malou dílnu založil v roce 1899 Marius
//Berliet v Brotteaux poblíž Lyon , ačkoliv první pokusy se
//stavbou automobilu už Berliet prováděl už v roce 189 .
//Od té doby vyrobil několik prvních vozů s jednoválcovými motor';

$search='Cyklostyl (alebo mimeograf) je prístroj na rozmnožovanie písomností.
	Bol rozšírený v čase, kedy moderné kopírovacie stroje alebo osobné počítače neboli dostupné.
	Prístroj sa skladal z plechového bubna, na ktorom bola napätá špeciálna blana.
	Princíp tlače spočíval v prepúšťaní farby blany. Blana pozostávala z troch listov.
	Prvý list bola vlastná blana a tá jediná mala význam pre "tlač" - bola na jednej strane opatrená vrstvou fialovej farby.
	Spodný list bol tvrdý papier a uprostred relatívne nekvalitný (na jedno použitie) kopirák.
	Na blanu sa písalo písacím strojom bez farbiacej pásky.';


include './google_search.class.php';
$google_search=new GoogleSearch();
$results_statistics=$google_search->setDebug(true)->search($search);

echo '<div style="background: red; color: #ffc; font-weight: bold; padding: .3em 1em; margin: 1em 0 0 0; font-size: 130%; font-family: Courier, monospace;">$results_statistics</div><div style="border: 1px solid red; background: #ffc; padding: 1em; margin: 0 0 1em 0; overflow: auto; font-family: Courier, monospace;"><pre>';
var_export($results_statistics);
echo '</pre><p style="font-size: 75%; color: red;"><b>file: </b>'.__FILE__.'<br><b>line: </b>'.__LINE__.'</p></div>';

/**
 * https://developers.google.com/custom-search/docs/xml_results?hl=en&csw=1
 * @param array $arr_of_search
 */
//function brute_force_google_search($arr_of_search){
//	$loop=0;
//	foreach($arr_of_search as $search){
//		$loop++;
//		$params_arr=array(
//				'v'=>'1.0',
//				'q'=>urlencode($search),
//
////				'start'=>'0',
////				'filter'=>'0',
////				'num'=>'3',
////				'client'=>'google-csbe',
//		);
//		$params=array();
//		foreach($params_arr as $key => $value){
//			$params[]=$key.'='.$value;
//		}
//		$results = json_decode(file_get_contents('http://ajax.googleapis.com/ajax/services/search/web?'.implode('&',$params)));
//
//		echo "\n".'<h2>hledani c. '.$loop.'</h2>';
//		echo "\n<p>dotaz: ".$search.'</p>';
//		echo "\n<p>parametry dotazu: ".implode('&',$params).'</p>';
//		if(!empty($results->responseStatus) && $results->responseStatus!==403){
//			echo "\n".'<ul>';
//			foreach($results->responseData->results as $result){
//				echo "\n\t".'<li><a href="'.$result->url.'">'.$result->title.'</li>';
//			}
//			echo "\n".'</ul>';
//			sleep(6);
//		}
//		else{
//			echo '<div style="border: 1px solid black; background: #ffc; padding: 1em;">';
//			echo '<p>Google to zariz parde...</p>';
//			var_dump($results);
//			break;
//			echo '</div>';
//		}
//	}
//}