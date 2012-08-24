<?php
class EP_Table{
	private $db=null;

	protected $dsn='';
	protected $tableName='';

	//if debug==true sql will not run directly but output to logs.
	public $debug=false;

	private $columns=null;

	private $where=array();

	private $group=array();
	function __construct($ep_db,$tableName){
		$this->db=$ep_db;
		$this->name=$tableName;
	}
	//--------

	private function normalizeTableName($tableName){
		$pieces=explode('.', $tableName);
		foreach($pieces as &$p){
			$p='`'.$p.'`';
		}
		return implode('.', $pieces);
	}
	public function getColumns(){
		if(!empty($this->columns)){
			return $this->columns;
		}
		$this->columns = $this->query('show columns from '.$this->normalizeTableName($this->tableName));
		//todo

		//
		return $this->columns;
	}

	/*--------------------
	 * for developer 
	 *-------------------*/
	public function filterColumn($val=true){

	}
	/*
	 * set resultSet's columns default to *
	 */
	public function columns($columnStr){}
	/*
	
	*/
	public function whereExpr(){
		return $this;
	}
	public function where($key,$op,$val){

	}
	public function order(){

	}
	public function join($method,$table){

	}
	/**
	 * convert to limit()
	 * 
	 */
	public function page($page=1,$pageSize=20){
		$this->limit( ($page-1)*$pageSize,$pageSize);
	}

	public function limit($start,$limit=''){
		if($limit==''){

		}
	}
	public function group(){}
	public 
	/*----------------------
	 *basic sql constructor
	 *----------------------*/
	public function select(){

	}
	public function delete($where){

	}
	public function update($arr,$where){

	}
	public function insert($arr){

	}

	public function logSql(){}
	//
	public function debug(){
		$this->debug=true;
	}


	//db operatations:
	public function exec($sql){
		$this->db->exec($sql);
	}
	public function query($sql){
		$this->db->query($sql);
	}
	public function prepare($sql,$params=null){
		$this->db->query($sql,$params);
	}
}