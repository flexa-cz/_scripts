<?php
/**
 * hlavni objekt
 */
 class Core{
	public $site;
	public $db;
	public $table;

	public function __construct(){
		$this->db=new Db;
		$this->site=new Site;
		$this->table=new Table;
	}
 }
