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
	private $having=array();


	private $sqlPieces=array();
	function __construct($ep_db,$tableName){
		$this->db=$ep_db;
		$this->name=$tableName;
	}
	//if $var is empty then ignore
	function i($sql,$var){
		if($var!=''){
			$this->sqlPieces[]=$sql;
		}
		return $this;
	}
	//append sql
	function a($sql){
		$this->sqlPieces[]=$sql;
		return $this;
	}
	//clear sql
	function c(){
		$this->sqlPieces=array();
		return $this;
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
	// filter $arr 's key and return a new array;
	// TODO: name to be changed
	public function filterByColumn($arr){
		$newArr=array();
		foreach($arr as $key=>$v){
			if(isset($this->columns[$key])){
				$newArr[$key]=$v;
			}
		}
		return $newArr;
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
	public function group(){

	}
	public function 

	/*----------------------
	 * basic sql caller ,everytime will clear the cache.
	 *----------------------*/
	public function select(){

	}
	public function delete(){

	}
	public function update($arr){

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