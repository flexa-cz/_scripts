<?php
/**
 * hlavni objekt
 */
 class Core{
	public $site;
	public $db;
	public $table;

	/**
	 * jadro "frameworku" :-)
	 * @param Db $db
	 * @param Site $site
	 * @param Table $table
	 */
	public function __construct(Db $db=null, Site $site=null, Table $table=null){
		$db=($db ? $db : new Db);
		$site=($site ? $site : new Site);
		$table=($table ? $table : new Table);
		$this->setDb($db)->setSite($site)->setTable($table);
	}

	private function setDb(Db $db){
		$this->db=$db;
		return $this;
	}

	private function setSite(Site $site){
		$this->site=$site;
		return $this;
	}

	private function setTable(Table $table){
		$this->table=$table;
		return $this;
	}
 }
