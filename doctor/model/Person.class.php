<?php
<<<<<<< HEAD
namespace doctor\model;
use core;
=======
>>>>>>> origin/master
/**
 * Description of Person
 *
 * @author Vlahovic
 */
class Person{
	private $db;

<<<<<<< HEAD
	public function setDb(core\Db $db){
=======
	public function setDb(Db $db){
>>>>>>> origin/master
		$this->db=$db;
		return $db;
	}

	public function printPersons(){
		return $this->controllReadiness()->db->query("SELECT * FROM persons")->getRows();
	}

	private function controllReadiness(){
		if(empty($this->db)){
			throw new Exception('Param Db is required.');
		}
		return $this;
	}
}
