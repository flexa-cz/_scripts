<?php
/**
 * hlavni objekt
 */
 class Core{
	 /** @var Site $site */
	public $site;
	/** @var Db $db */
	public $db;
	/** @var Table $table */
	public $table;
	/** @var MVC $mvc */
	public $loader;

	/**
	 * jadro "frameworku" :-)
	 * @param Db $db
	 * @param Site $site
	 * @param Table $table
	 */
	public function __construct(Db $db=null, Site $site=null, Table $table=null){
		$loader=new Loader();
		$_db=($db ? $db : new Db);
		$_site=($site ? $site : new Site);
		$_table=($table ? $table : new Table);
		$this->setDb($_db)->setSite($_site)->setTable($_table)->setLoader($loader);
	}

	private function setLoader(Loader $loader){
		$this->loader=$loader;
		return $this;
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
