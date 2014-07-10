<?php
/**
 * @link http://www.w3schools.com/php/php_ref_ftp.asp
 */
class Ftp{
	private $server;
	private $user;
	private $password;
	private $connection;
	private $current_directory='/';

	/* ************************************************************************ */
	/* magic methods */
	/* ************************************************************************ */

	public function __construct($server,$user,$password){
		$this->setServer($server)->setUser($user)->setPassword($password)->connect()->login();
	}

	public function __destruct(){
		$this->closeConnection();
	}

	/* ************************************************************************ */
	/* public methods */
	/* ************************************************************************ */

	/**
	 * put file to ftp server
	 * @param string $local_file
	 * @param string $remote_file
	 * @return \Ftp
	 */
	public function filePut($local_file,$remote_file){
		if(!ftp_put($this->connection, $remote_file, $local_file, FTP_ASCII)){
			throw new Exception('Error during uploading file. ('.$local_file.' -> '.$remote_file.')');
		}
		return $this;
	}

	/**
	 * download file from ftp server
	 * @param string $local_file
	 * @param string $remote_file
	 * @return \Ftp
	 */
	public function fileGet($local_file,$remote_file){
		if(!ftp_get($this->connection, $local_file, $remote_file, FTP_ASCII)){
			throw new Exception('Error during downloading file. ('.$remote_file.' -> '.$local_file.')');
		}
		return $this;
	}

	/**
	 * make directory at curent directory
	 * @param string $dir_name
	 * @return \Ftp
	 */
	public function directoryMake($dir_name){
		if(!ftp_mkdir($this->connection, $dir_name)){
			throw new Exception('Error during making directory "'.$dir_name.'".');
		}
		return $this;
	}

	/**
	 * @return \Ftp
	 */
	public function closeConnection(){
		$this->server=null;
		$this->user=null;
		$this->password=null;
		if($this->connection){
			if(ftp_close($this->connection)){
				$this->connection=null;
			}
			else{
				throw new Exception('Error during closing connection.');
			}
		}
		return $this;
	}

	/**
	 * returns content of curent directory
	 * @return array
	 */
	public function directoryPrintStructure(){
		if(!$return=ftp_nlist($this->connection, $this->current_directory)){
			throw new Exception('Error during getting structure of current directory.');
		}
		return $return;
	}

	/**
	 * @param string $currrent_directory
	 * @return \Ftp
	 */
	public function directoryChangeCurent($currrent_directory){
		if(@ftp_chdir($this->connection, $currrent_directory)){
			$this->current_directory=$currrent_directory;
		}
		else{
			throw new Exception('Error during changing current directory "'.$currrent_directory.'". ');
		}
		return $this;
	}

	/**
	 * @return \Ftp
	 */
	public function directoryChangeCurrentToRoot(){
		if(ftp_pwd($this->connection)){
			$this->current_directory='/';
		}
		else{
			throw new Exception('Error during changing current directory to root.');
		}
		return $this;
	}

	/**
	 * @param string $dir_name
	 * @return \Ftp
	 */
	public function directoryRemove($dir_name){
		if(!ftp_rmdir($this->connection, $dir_name)){
			throw new Exception('Error during removing directory "'.$dir_name.'".');
		}
		return $this;
	}

	/**
	 * @param string $remote_file
	 * @return \Ftp
	 */
	public function fileRemove($remote_file){
		if(!ftp_delete($this->connection, $remote_file)){
			throw new Exception('Error during removing file "'.$remote_file.'".');
		}
		return $this;
	}

	/* ************************************************************************ */
	/* private methods */
	/* ************************************************************************ */

	private function connect(){
		if(!$this->connection = ftp_connect($this->server)){
			throw new Exception('Could not connect to "'.$this->server.'".');
		}
		return $this;
	}

	private function login(){
		if(!ftp_login($this->connection, $this->user, $this->password)){
			throw new Exception('Could not login to "'.$this->server.'".');
		}
		return $this;
	}

	private function setServer($server){
		if(empty($server)){
			throw new Exception('Server must be set.');
		}
		$this->server=$server;
		return $this;
	}


	private function setUser($user){
		if(empty($user)){
			throw new Exception('User must be set.');
		}
		$this->user=$user;
		return $this;
	}


	private function setPassword($password){
		if(empty($password)){
			throw new Exception('Password must be set.');
		}
		$this->password=$password;
		return $this;
	}
}
// 148PnMZO