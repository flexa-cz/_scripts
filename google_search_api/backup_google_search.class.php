<?php
class GoogleSearch{
//	private $url='http://www.google.com/cse/api/<USER_ID>/cse/<CSE_ID>';
//
//	private $user_id='104468625807147486769';
//	private $user_id_pattern='<USER_ID>';
//
//	private $app_id='872097049026';
//	private $app_id_pattern='<CSE_ID>';

	/** @var \Google_Client */
	private $client;
	/** @var \Google_CustomsearchService */
	private $custom_search;

	private $client_id='872097049026.apps.googleusercontent.com';
	private $client_secret='fU-8ZHODIW6dU4x-aGomxbyG';
	private $client_redirect_url='https://localhost/_scripts/google_search_api/';
	private $api_key='AIzaSyDrL7eqA8lbIRv3vlk7kJglWFoD-r8AZLs';

	public function __construct(){
		require_once './google-api-php-client/src/Google_Client.php';
		require_once './google-api-php-client/src/contrib/Google_CustomsearchService.php';
//		require_once './google-api-php-client/src/contrib/Google_CalendarService.php';
		$this->authorization();
	}

	private function authorization(){
		session_start();

		$this->client=new Google_Client();
		$this->client->setApplicationName('Google Custom Search');
		// Visit https://code.google.com/apis/console?api=plus to generate your
		// client id, client secret, and to register your redirect uri.
		$this->client->setClientId($this->client_id);
		$this->client->setClientSecret($this->client_secret);
		$this->client->setRedirectUri($this->client_redirect_url);
		$this->client->setDeveloperKey($this->api_key);
//		$this->custom_search=new Google_CalendarService($this->client);
		$this->custom_search=new Google_CustomsearchService($this->client);

		if (isset($_GET['code'])) {
//		if (!isset($_SESSION['token'])) {
			$this->client->authenticate();
			$_SESSION['token'] = $this->client->getAccessToken();
			$redirect = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];
			header('Location: ' . filter_var($redirect, FILTER_SANITIZE_URL));
		}

		if (isset($_SESSION['token'])) {
			$this->client->setAccessToken($_SESSION['token']);
		}
		return $this;
	}




	public function search($query){
		$this->testClient();
	}
}