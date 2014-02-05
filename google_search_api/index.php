<!DOCTYPE html>
<html  dir="ltr" lang="cs" xml:lang="cs">
<head>
    <title>Google API</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>
<body>
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

//my_google_search($search);
//fetch_google($search);
yahoo_search($search);
//google_api_search($search);




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

function google_api_search($search){
	include_once('./google_api_search.class.php');
	$client_id='872097049026.apps.googleusercontent.com';
	$client_secret='fU-8ZHODIW6dU4x-aGomxbyG';
	$api_key='AIzaSyDrL7eqA8lbIRv3vlk7kJglWFoD-r8AZLs';
	$google_api_search=new GoogleApiSearch($client_id,$client_secret,$api_key);
}

function my_google_search($search){
	include './google_search.class.php';
	$google_search=new GoogleSearch();
	$results_statistics=$google_search->setDebug(true)->search($search);

	echo '<div style="background: red; color: #ffc; font-weight: bold; padding: .3em 1em; margin: 1em 0 0 0; font-size: 130%; font-family: Courier, monospace;">$results_statistics</div><div style="border: 1px solid red; background: #ffc; padding: 1em; margin: 0 0 1em 0; overflow: auto; font-family: Courier, monospace;"><pre>';
	var_export($results_statistics);
	echo '</pre><p style="font-size: 75%; color: red;"><b>file: </b>'.__FILE__.'<br><b>line: </b>'.__LINE__.'</p></div>';
}


function fetch_google($query) {
	$cleanQuery = str_replace(" ","+",$query);
	$url = 'http://www.google.com/search?q='.$cleanQuery;
	$scrape = file_get_contents($url);
	$scrapedItem = preg_match_all('/About.*?results/i', $scrape, $matches, PREG_PATTERN_ORDER);
	$results = $matches[0][0];
	$scrapedItem2 = preg_match_all('/[1-9](?:\d{0,2})(?:,\d{3})*(?:\.\d*[1-9])?|0?\.\d*[1-9]|0/i', $results, $matches2, PREG_PATTERN_ORDER);
	$finalResult = $matches2[0][0];
	/* Display */
	echo '<div style="position: aboslute; top: 0px; left: 0px; color: #bada55; background: #000; padding: 65px; z-index: 9999999999999;">';
	echo '<h3>The Search Query (ie, Title of rss post)</h3>'; print_r($query); echo '<br />';
	echo '<h3>Google Result for the query</h3>'; echo $results; echo '<br />';
	echo '<h3>Scraped Total</h3>'; echo $finalResult; echo '<br />';
	echo '</div>';
	print_r($scrape);
}

function yahoo_search($search){
	require("./yahoo_api/OAuth.php");
//	$cc_key  = "dj0yJmk9VjcyUGJFak9QbkhwJmQ9WVdrOVdXRmxNVUV4Tm1zbWNHbzlNemMyT1RRNE5qSS0mcz1jb25zdW1lcnNlY3JldCZ4PTkw";
//	$cc_secret = "c523c08bee5122b4733fce347ccf7e23a564cfd7";
	$cc_key  = "dj0yJmk9VFNrdktUWkpHS2ZIJmQ9WVdrOVlVOHlWVU5MTkhNbWNHbzlNVE0zTkRVd056VTJNZy0tJnM9Y29uc3VtZXJzZWNyZXQmeD1kYw--";
	$cc_secret = "a989b2c0018d13f2fe0bde271b8b2343026fe47e";
	$url = "http://yboss.yahooapis.com/ysearch/web";
	$args = array();
	$args["q"] = 'mouse';
	$args["format"] = "json";

	$consumer = new OAuthConsumer($cc_key, $cc_secret);
	$request = OAuthRequest::from_consumer_and_token($consumer, NULL,"GET", $url, $args);
	$request->sign_request(new OAuthSignatureMethod_HMAC_SHA1(), $consumer, NULL);
	$url = sprintf("%s?%s", $url, OAuthUtil::build_http_query($args));
	$ch = curl_init();
	$headers = array($request->to_header());
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
	$rsp = curl_exec($ch);
	$results = json_decode($rsp);

	echo 'Yahoo search results:';
	print_r($results);
}

?>
</body>