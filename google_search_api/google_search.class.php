<?php
/**
 * hleda pomoci google na webu
 */
class GoogleSearch{
	private $max_words=12;
	private $sleep=8;
	private $min_hits_to_return=3;

	private $url='http://ajax.googleapis.com/ajax/services/search/web?';
	private $params=array('v'=>'1.0','start'=>'0','filter'=>'0','num'=>'10','client'=>'google-csbe','q'=>'',);
	private $debug=false;
	private $break_loop=false;
	private $search_array;
	private $group;
	private $params_string;
	private $results;
	private $response_status_ok;
	private $loop;
	private $search_string;
	private $results_statistics;

	/**
	 * spusti cely mechanismus
	 * @param string $search
	 * @param array $params - zatim nefunguje
	 * @return array
	 */
	public function search($search, $params=false){
		$this->setSearchArray($search);
		if(!empty($this->search_array)){
			$this->loop=0;
			$this->results_statistics=array();
			foreach($this->search_array as $this->search_string){
				$this->loop++;
				$this->params['q']=urlencode($this->search_string);
				$params=array();
				foreach($this->params as $key => $value){
					$params[]=$key.'='.$value;
				}
				$this->params_string=implode('&',$params);
				$this
								->oneQuery()
								->processResult()
								->printInfo();
				if($this->break_loop){
					break;
				}
				if(count($this->search_array)!=$this->loop){
					sleep($this->sleep);
				}
			}
		}

		return $this->getFilteredResultsStatistics();
	}

	/**
	 * @param boolean $debug
	 * @return \GoogleSearch
	 */
	public function setDebug($debug){
		$this->debug=($debug ? true : false);
		return $this;
	}

	/**
	 * @return mixed array if is success; false if google stoped our robot
	 */
	private function getFilteredResultsStatistics(){
		$return=false;
		if(!$this->break_loop){
			$return=array();
			if(!empty($this->results_statistics)){
				foreach($this->results_statistics as $result){
					if($result['count']>=$this->min_hits_to_return){
						$return[]=$result;
					}
				}
			}
		}
		return $return;
	}

	/**
	 * polozeni dotazu
	 * je to oddelene, protoze jsem ocekaval mozne zmeny v tom jaka sluzba se bude volat
	 * @return \GoogleSearch
	 */
	private function oneQuery(){
		$this->results=json_decode(file_get_contents($this->url.$this->params_string));
//		echo '<div style="background: red; color: #ffc; font-weight: bold; padding: .3em 1em; margin: 1em 0 0 0; font-size: 130%; font-family: Courier, monospace;">$this->results</div><div style="border: 1px solid red; background: #ffc; padding: 1em; margin: 0 0 1em 0; overflow: auto; font-family: Courier, monospace;"><pre>';
//		var_export($this->results);
//		echo '</pre><p style="font-size: 75%; color: red;"><b>file: </b>'.__FILE__.'<br><b>line: </b>'.__LINE__.'</p></div>';
//		exit('<p><b>file: </b>'.__FILE__.'<br><b>line: </b>'.__LINE__.'<br><b>function: </b>exit()</p>');
		$this->response_status_ok=(!empty($this->results->responseStatus) && $this->results->responseStatus!==403 ? true : false);
		return $this;
	}

	/**
	 * zpracovani vysledku
	 * @return \GoogleSearch
	 */
	private function processResult(){
		if($this->response_status_ok){
			foreach($this->results->responseData->results as $result){
				$hash=md5($result->url);
				if(empty($this->results_statistics[$hash])){
					$this->results_statistics[$hash]=array(
							'count'=>1,
							'url'=>$result->url,
							'title'=>$result->titleNoFormatting,
							'mimetype'=>(!empty($result->fileFormat) ? $result->fileFormat : 'text/html'),
					);
				}
				else{
					$this->results_statistics[$hash]['count']++;
				}
			}
		}
		return $this;
	}

	/**
	 * pokud je v debug modu tak vypise info na monitor
	 * @return \GoogleSearch
	 */
	private function printInfo(){
		if($this->debug){
			echo "\n".'<h2>hledani c. '.$this->loop.'</h2>';
			echo "\n<p><b>dotaz: </b>".$this->search_string.'</p>';
			echo "\n<p><b>parametry dotazu: </b>".$this->params_string.'</p>';
			if($this->response_status_ok){
				echo "\n".'<ul>';
				foreach($this->results->responseData->results as $result){
					echo "\n\t".'<li><a href="'.$result->url.'">'.$result->titleNoFormatting.'</a></li>';
				}
				echo "\n".'</ul>';
			}
			else{
				echo '<div style="border: 1px solid black; background: #ffc; padding: 1em;">';
				echo '<p>Google to zariz parde...</p>';
				var_dump($this->results);
				echo '</div>';
				$this->break_loop=true;
			}
		}
		return $this;
	}

	/**
	 * rozdeli vyhledavany retezec na casti dlouhe dle nastaveni
	 * @param string $search
	 * @return \GoogleSearch
	 */
	private function setSearchArray($search){
		$this->search_array=array();
		$this->group=array();
		$working=explode(' ',$search);
		if(count($working)<$this->max_words){
			$this->search_array[]=$search;
		}
		else{
			$this->group=array();
			foreach($working as $word){
				$this->group[]=$word;
				if(count($this->group)===$this->max_words){
					$this->setSearchArrayItem();
				}
			}
			if(!empty($this->group)){
				$this->setSearchArrayItem();
			}
		}
		return $this;
	}

	/**
	 * spojuje pole do retezcu k vyhledani
	 * @return \GoogleSearch
	 */
	private function setSearchArrayItem(){
		$this->search_array[]=implode(' ',$this->group);
		$this->group=array();
		return $this;
	}
}