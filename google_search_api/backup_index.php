<?php
$search='"Berliet Skočit n : Navigac , Hledání Závodní vůz Berliet"
+"na čtvrtém místě v cíli soutěže Targa Flori , 1"
+". května 190 , řidič Jean Porporato Berliet Cabriolet Berliet"
+"byla francouzská automobilka vyrábějící osobn , nákladní a užitkové automobil"
+". Firma jako malou dílnu založil v roce 1899 Marius"
+"Berliet v Brotteaux poblíž Lyon , ačkoliv první pokusy se"
+"stavbou automobilu už Berliet prováděl už v roce 189 ."
+"Od té doby vyrobil několik prvních vozů s jednoválcovými motor"';

$arr_of_search=array();
//$arr_of_search[]='Milan Flexa Vlahovič';
$arr_of_search[]='Berliet Skočit n : Navigac , Hledání Závodní vůz Berliet';
$arr_of_search[]='na čtvrtém místě v cíli soutěže Targa Flori , 1';
$arr_of_search[]='května 190 , řidič Jean Porporato Berliet Cabriolet Berliet';
$arr_of_search[]='byla francouzská automobilka vyrábějící osobn , nákladní a užitkové automobil';
$arr_of_search[]='. Firma jako malou dílnu založil v roce 1899 Marius';
$arr_of_search[]='Berliet v Brotteaux poblíž Lyon , ačkoliv první pokusy se';
$arr_of_search[]='stavbou automobilu už Berliet prováděl už v roce 189 .';
$arr_of_search[]='Od té doby vyrobil několik prvních vozů s jednoválcovými motor';

/* ************************************************************************** */
/* yahoo api */
/* ************************************************************************** */
require("./yahoo_api/OAuth.php");

//yahoo_search($arr_of_search);

function yahoo_search($arr_of_search){
	$consumer=yahoo_connect();
//	$url = "http://yboss.yahooapis.com/ysearch/web";
	$url = "http://yboss.yahooapis.com/ysearch/news,web,images";

	$loop=0;
	foreach($arr_of_search as $search){
		$loop++;
		$args = array();
		$args["q"] = $search;
		$args["format"] = "json";

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

		echo "\n".'<h2>hledani c. '.$loop.'</h2>';
		echo "\n<p><b>dotaz: </b>".$search.'</p>';
		if(!empty($results->bossresponse->web->results)){
			echo "\n".'<ul>';
			foreach($results->bossresponse->web->results as $result){
				echo "\n\t".'<li><a href="'.$result->url.'">'.$result->title.'</a></li>';
			}
			echo "\n".'</ul>';
		}
		else{
			echo '<div style="background: red; color: #ffc; font-weight: bold; padding: .3em 1em; margin: 1em 0 0 0; font-size: 130%; font-family: Courier, monospace;">$results</div><div style="border: 1px solid red; background: #ffc; padding: 1em; margin: 0 0 1em 0; overflow: auto; font-family: Courier, monospace;"><pre>';
			var_export($results);
			echo '</pre><p style="font-size: 75%; color: red;"><b>file: </b>'.__FILE__.'<br><b>line: </b>'.__LINE__.'</p></div>';
		}
	}
}

function yahoo_connect(){
	static $consumer=null;
	if(!$consumer){
		$cc_key  = "dj0yJmk9VjcyUGJFak9QbkhwJmQ9WVdrOVdXRmxNVUV4Tm1zbWNHbzlNemMyT1RRNE5qSS0mcz1jb25zdW1lcnNlY3JldCZ4PTkw";
		$cc_secret = "c523c08bee5122b4733fce347ccf7e23a564cfd7";
		$consumer = new OAuthConsumer($cc_key, $cc_secret);
	}
	return $consumer;
}

/* ************************************************************************** */
/* google api */
/* ************************************************************************** */
//include './google_search.class.php';
//$google_search=new GoogleSearch();
//$google_search->search('Milan Flexa Vlahovič');

/* ************************************************************************** */
/* bez google api */
/* ************************************************************************** */

brute_force_google_search($arr_of_search);

/**
 * https://developers.google.com/custom-search/docs/xml_results?hl=en&csw=1
 * @param array $arr_of_search
 */
function brute_force_google_search($arr_of_search){
	$loop=0;
	foreach($arr_of_search as $search){
		$loop++;
		$params_arr=array(
				'v'=>'1.0',
				'q'=>urlencode($search),

//				'start'=>'0',
//				'filter'=>'0',
//				'num'=>'3',
//				'client'=>'google-csbe',
		);
		$params=array();
		foreach($params_arr as $key => $value){
			$params[]=$key.'='.$value;
		}
		$results = json_decode(file_get_contents('http://ajax.googleapis.com/ajax/services/search/web?'.implode('&',$params)));

		echo "\n".'<h2>hledani c. '.$loop.'</h2>';
		echo "\n<p><b>dotaz: </b>".$search.'</p>';
		echo "\n<p><b>parametry dotazu: </b>".implode('&',$params).'</p>';
		if(!empty($results->responseStatus) && $results->responseStatus!==403){
			echo "\n".'<ul>';
			foreach($results->responseData->results as $result){
				echo "\n\t".'<li><a href="'.$result->url.'">'.$result->title.'</a></li>';
			}
			echo "\n".'</ul>';
			sleep(6);
		}
		else{
			echo '<div style="border: 1px solid black; background: #ffc; padding: 1em;">';
			echo '<p>Google to zariz parde...</p>';
			var_dump($results);
			break;
			echo '</div>';
		}
	}
}