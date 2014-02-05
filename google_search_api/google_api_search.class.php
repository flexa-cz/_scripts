<?php
/**
 * manual how to configure google client api
 * http://enarion.net/programming/php/google-client-api/google-client-api-php/
 *
 * nastroje pro vyvojare google aplikaci
 * https://cloud.google.com/console/project
 */
class GoogleApiSearch{
	private $access_type='online';
	private $application_name='Google Search Test';
	/**
	 * all posible scopes are here
	 * https://developers.google.com/admin-sdk/directory/v1/guides/authorizing
	 */
	private $scopes=array('https://www.googleapis.com/auth/admin.directory.device.chromeos');

	private $client_id;
	private $client_secret;
	private $api_key;
	private $script_uri;
	/** @var Google_Client $client */
	private $client;
	/** @var Google_CustomsearchService $service */
	private $service;

	public function __construct($client_id,$client_secret,$api_key){
		require_once dirname(__FILE__).'/google-api-php-client/src/Google_Client.php';
		require_once dirname(__FILE__).'/google-api-php-client/src/contrib/Google_CustomsearchService.php';
		$this
						->setApiKey($api_key)
						->setClientId($client_id)
						->setClientSecret($client_secret)
						->setScriptUri()
						->setClient()
						;
	}

	private function setClient(){
		$this->client=new Google_Client();
		$this->client->setAccessType($this->access_type); // default: offline
		$this->client->setApplicationName($this->application_name);
		$this->client->setClientId($this->client_id);
		$this->client->setClientSecret($this->client_secret);
		$this->client->setRedirectUri($this->script_uri);
		$this->client->setDeveloperKey($this->api_key); // API key
		$this->client->setScopes($this->scopes);

//		if (!$this->client->getAccessToken()) { // auth call to google
//			$authUrl = $this->client->createAuthUrl();
//			header("Location: ".$authUrl);
//			die;
//		}

		$this->service=new Google_CustomsearchService($this->client);



//		$this->client->authenticate();

		echo '<div style="background: red; color: #ffc; font-weight: bold; padding: .3em 1em; margin: 1em 0 0 0; font-size: 130%; font-family: Courier, monospace;">$this->client</div><div style="border: 1px solid red; background: #ffc; padding: 1em; margin: 0 0 1em 0; overflow: auto; font-family: Courier, monospace;"><pre>';
		var_export($this->client);
		echo '</pre><p style="font-size: 75%; color: red;"><b>file: </b>'.__FILE__.'<br><b>line: </b>'.__LINE__.'</p></div>';
	}


	private function setScriptUri(){
		$this->script_uri="http://".$_SERVER["HTTP_HOST"].$_SERVER['PHP_SELF'];
		return $this;
	}

	private function setClientId($client_id){
		$this->client_id=$client_id;
		return $this;
	}

	private function setClientSecret($client_secret){
		$this->client_secret=$client_secret;
		return $this;
	}

	private function setApiKey($api_key){
		$this->api_key=$api_key;
		return $this;
	}
}