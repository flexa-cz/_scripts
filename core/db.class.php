<?php
class db{
	private $config_file_address="core/config.ini";
	// pripojeni k db
	private $mysql_address=false;
	private $mysql_user=false;
	private $mysql_password=false;
	private $mysql_database=false;
	private $mysql_connect=null;

	/**
	 * pripojeni k db
	 *
	 * @staticvar mixed $cache pokud se podari pripojit tak obsahuje ukazatel pripojeni, jinak false
	 * @return mixed bud ukazatel pripojeni, nebo false
	 */
	final public function connect(){
		if($this->mysql_connect===null){
			// nacte nastaveni pripojeni
			$config=parse_ini_file(_ROOT.$this->config_file_address,true);
			if(isset($config['mysql']) && is_array($config['mysql']) && count($config['mysql'])){
				// nastaveni ze souboru
				$this->mysql_address=$config['mysql']['address'];
				$this->mysql_user=$config['mysql']['user'];
				$this->mysql_password=$config['mysql']['password'];
				$this->mysql_database=$config['mysql']['database'];

				// pripojeni
				$this->mysql_connect=mysql_connect($this->mysql_address, $this->mysql_user, $this->mysql_password);
				if($this->mysql_connect){
					$this->selectDatabase();
				}
				else{
					$this->return[]='Nepodařilo se připojit k databázovemu serveru.';
					$this->able_to_vote=false;
				}
			}
			else{
				$this->return[]='Nepodařilo se přihlašovací údaje k databázi.';
				$this->able_to_vote=false;
			}
		}
	}

	final public function selectDatabase(){
		if($this->mysql_database){
			$sel=mysql_select_db($this->mysql_database);
			if($sel){
				mysql_query("SET CHARACTER SET utf8",$this->mysql_connect);
				mysql_query('SET NAMES utf8',$this->mysql_connect);
				$this->mysql_connect=$this->mysql_connect;
			}
			else{
				$this->return[]='Nepořilo se vybrat databázi.';
				$this->able_to_vote=false;
			}
		}
	}
}
?>
