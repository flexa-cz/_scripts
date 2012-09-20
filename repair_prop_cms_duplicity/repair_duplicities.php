<?php
class RepairDuplicity{
	private $db_conn;
	private $root_lft;
	private $root_rgt;
	private $duplicity_string;
	private $duplicity_items;
	private $able_to_commit;
	private $correct_values;

	public function __construct($db_conn){
		$this->setDbConn($db_conn);
	}

	public function repair($root_lft,$root_rgt,$duplicity_string){
		$this->able_to_commit=true;
		$this		->setRootLft($root_lft)->setRootRgt($root_rgt)->setDuplicityString($duplicity_string)->controlInstance()
						->transaction('start')->selectDuplicityItems()->createCorrectValues()
						->closeTransaction();
		debuger::var_dump($this);
	}

	private function createCorrectValues(){
		$this->correct_values=array();
		$query="SELECT id,content FROM sites_parts WHERE part='title' AND (id_sites=".implode(" OR id_sites=",$this->duplicity_items).")";
		debuger::var_dump($query);
		$res=mysql_query($query,$this->db_conn);
		if($res){
			while($data=mysql_fetch_assoc($res)){
				$this->correct_values[$data['id']]=$data['content'];
			}
		}
		else{
			$this->able_to_commit=false;
			debuger::breakpoint(mysql_error($this->db_conn));
		}
		return $this;
	}

	private function selectDuplicityItems(){
		$this->duplicity_items=array();
		$query="SELECT id,id_sites FROM sites_parts WHERE part='url' AND content='".$this->duplicity_string."'";
		$res=mysql_query($query,$this->db_conn);
		if($res){
			while($data=mysql_fetch_assoc($res)){
				$this->duplicity_items[$data['id']]=$data['id_sites'];
			}
		}
		else{
			$this->able_to_commit=false;
			debuger::breakpoint(mysql_error($this->db_conn));
		}
		return $this;
	}

	private function setDbConn($db_conn){
		$this->db_conn=$db_conn;
		return $this;
	}

	private function setRootLft($root_lft){
		$this->root_lft=(int)$root_lft;
		return $this;
	}

	private function setRootRgt($root_rgt){
		$this->root_rgt=(int)$root_rgt;
		return $this;
	}

	private function setDuplicityString($duplicity_string){
		$this->duplicity_string=(string)$duplicity_string;
		return $this;
	}

	private function transaction($action){
		$action=strtolower($action);
		$query=false;
		if($action=='start'){
			$query='START TRANSACTION';
		}
		elseif($action=='stop'){
			$query='STOP TRANSACTION';
		}
		elseif($action=='commit'){
			$query='COMMIT';
		}
		elseif($action=='rollback'){
			$query='ROLLBACK';
		}
		else{
			throw new Exception('spatny prikaz pro transakce');
			return false;
		}
		mysql_query($query, $this->db_conn);
		return $this;
	}

	private function closeTransaction(){
		if($this->able_to_commit){
			$this->transaction('commit');
			debuger::breakpoint('transakce byla ukoncena prikazem COMMIT');
		}
		else{
			$this->transaction('rollback');
			debuger::breakpoint('transakce byla ukoncena prikazem ROLLBACK');
		}
		$this->transaction('stop');
	}


	private function controlInstance(){
		if(!$this->duplicity_string || !$this->root_lft || !$this->root_rgt || !$this->db_conn){
			$this->able_to_commit=false;
		}
		return $this;
	}
}