<?php
/**
 * prochazi web a zjistuje adresy na vysledky s jednotlivymi tahy
 */
class address_checker{
	private $source_address='http://www.sportka-aktualni-vysledky.net/prehledy/podle-data?pageno=';
	private $max_loops=10;
	private $domain_info=array();

	public final function check(){
		for($x=1;$x<=$this->max_loops; $x++){
			$addr=$this->source_address.$x;
			$this->domain_info=array();

			// pomoci curl zjisti vse...
			$ch = curl_init(); // create cURL handle (ch)
			if (!$ch) {
					die("Couldn't initialize a cURL handle");
			}
			// set some cURL options
			$ret = curl_setopt($ch, CURLOPT_URL,            $addr);
			$ret = curl_setopt($ch, CURLOPT_HEADER,         0);
			$ret = curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
			$ret = curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			$ret = curl_setopt($ch, CURLOPT_TIMEOUT,        60);

			// execute
			$ret = curl_exec($ch);

			if (empty($ret)) {
					break;
			} else {
					$info = curl_getinfo($ch);
					$real_url = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
					$host = parse_url($real_url, PHP_URL_HOST);
					$ip = @gethostbyname($host);
					$this->domain_info['host']=$host;
					$this->domain_info['real_url']=$real_url;
					$this->domain_info['ip']=$ip;

					if (empty($info['http_code'])) {
						$this->domain_info['http_code']='no http code';
						$this->domain_info['response']='no response';
					} else {
						// load the HTTP codes
						$http_codes = parse_ini_file(_ROOT."sazka/http_protocol_messages.ini");
						$this->domain_info['http_code']=$info['http_code'];
						$this->domain_info['response']=$http_codes[$info['http_code']];
					}

			}
			curl_close($ch); // close cURL handler

			var_dump($this->domain_info);
		}
	}
}