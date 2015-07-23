<?php
class RestClientRameder{
	private $service_url='https://api.rameder.eu/shop';
	private $service_url_get='/service/{_locale}';

	private $user_name='?';
	private $password='?';

	private	$header_json='Content-Type: application/json';
	private $header_xwsse;

	/* ======================================================================== */
	/* magic methods */
	/* ======================================================================== */

	public function __construct() {
		$this->setHeaderXwsse();
	}

	/* ======================================================================== */
	/* public methods */
	/* ======================================================================== */

	/**
	 * vraci seznam vyrobcu
	 * @return array
	 */
	public function getManufacturers(){
		$return=false;
		$url=$this->service_url.$this->service_url_get.'/vehicle/search/step-by-step/manufacturers.json';
		$manufacturers=$this->printDataGetMethod($url, array($this->header_xwsse));
		if($manufacturers){
			$return=json_decode($manufacturers);
		}
		return $return;
	}

	/* ======================================================================== */
	/* private methods */
	/* ======================================================================== */

	/**
	 * prihlasi se ke sluzbam a ziska hlavicku pro prihlaseni
	 * @return \RestClientRameder
	 * @throws \Exception
	 */
	private function setHeaderXwsse(){
		$url=$this->service_url.'/login.json';
		$body=array(
				'username'=>$this->user_name,
				'password'=>$this->password,
		);
		$data=$this->printDataPostMethod($url, array($this->header_json), $body, true, true);
		$data_array=$this->printHeadersAsArray($data);
		if(!empty($data_array['X-WSSE'])){
			$this->header_xwsse='x-wsse: '.$data_array['X-WSSE'];
		}
		else{
			throw new \Exception('Unexisting X-WSSE header.');
		}
		return $this;
	}

	/**
	 * z hlavicek udela pole a vrati ho
	 * @param string $headers
	 * @return array
	 */
	private function printHeadersAsArray($headers){
		$return=array();
		$data_array=explode('[br]',str_replace(array("\r\n","\n"),'[br]',$headers));
		foreach($data_array as $row){
			if(strpos($row, ':')!==false){
				$row_array=explode(':',$row);
				$index=$row_array[0];
				unset($row_array[0]);
				$return[$index]=trim(implode(':', $row_array));
			}
			elseif($row){
				$return[]=$row;
			}
		}
		return $return;
	}

	/**
	 * provede get pozadavek a vrati vysledek
	 * @param string $url
	 * @param array $headers [optional] default null
	 * @param boolean $print_response_headers [optional] default false
	 * @return string
	 */
	private function printDataGetMethod($url, array $headers=null, $print_response_headers=false){
		return $this->printData('get', $url, $headers, null, false, $print_response_headers);
	}

	/**
	 * provede post pozadavek a vrati vysledek
	 * @param string $url
	 * @param array $headers [optional] default null
	 * @param array $body [optional] default null
	 * @param boolean $json_body [optional] default true
	 * @param boolean $print_response_headers [optional] default false
	 * @return string
	 */
	private function printDataPostMethod($url, array $headers=null, array $body=null, $json_body=true, $print_response_headers=false){
		return $this->printData('post', $url, $headers, $body, $json_body, $print_response_headers);
	}

	/**
	 * provede curl pozadavek a vrati data
	 * @param string $method
	 * @param string $url
	 * @param array $headers [optional] default null
	 * @param array $body [optional] default null
	 * @param boolean $json_body [optional] default true
	 * @param boolean $print_response_headers [optional] default false
	 * @return string
	 * @throws \Exception
	 */
	private function printData($method, $url, array $headers=null, array $body=null, $json_body=true, $print_response_headers=false){
		$curl = curl_init($url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		if($print_response_headers){
			curl_setopt($curl, CURLOPT_VERBOSE, 1);
			curl_setopt($curl, CURLOPT_HEADER, 1);
		}
		curl_setopt($curl, CURLOPT_POST, ($method==='post' ? true : false));
		if(!empty($headers)){
			curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
		}
		if(!empty($body)){
			curl_setopt($curl, CURLOPT_POSTFIELDS, ($json_body ? json_encode($body) : $body));
		}
		$curl_response = curl_exec($curl);
		if ($curl_response === false) {
				$info = curl_getinfo($curl);
				curl_close($curl);
				throw new \Exception('Error occured during curl exec. ('.var_export($info,true).')');
		}
		curl_close($curl);
		return $curl_response;
	}
}