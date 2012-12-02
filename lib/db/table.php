<?php
class EP_Table{
	private $db=null;

	protected $dsn='';
	protected $name='';

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
		//$c = $this->query('show columns from '.$this->normalizeTableName($this->name));
		$c = $this->query('show columns from '.$this->name);
		$newC=array();
		foreach ($c as $key) {
			$newC[$key['Field']]=$key['Type'];
		}

		$this->columns=$newC;
		//var_dump($this->columns);
		return $this->columns;
	}

	// filter $arr 's key and return a new array;
	public function filterColumn($arr){
		$newArr=array();
		$this->getColumns();
		foreach($arr as $key=>$v){
			if(isset($this->columns[$key])){
				$newArr[$key]=$v;
			}
		}
		return $newArr;
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
	/*----------------------
	 * basic sql caller ,everytime will clear the cache.
	 *----------------------*/
	public function select(){

	}
	public function delete($where){

	}
	public function update($arr,$where){

	}
	public function insert($arr,$ignore=false){
		$arr=$this->filterColumn($arr);
		$sql='INSERT INTO `'. addslashes($this->name) .'` ';

		$keys=array();
		$vals=array();
		if(empty($arr)){
			E::log('insert empty!','db');
			return false;
		}
		foreach ($arr as $k => $v) {
			$keys[]='`'.addslashes($k).'`';
			$vals[]='"'.addslashes($v).'"';
		}
		$sql.=' ('.implode(' , ', $keys).') values ('.implode(',', $vals) .')';
		$this->exec($sql);
		return $this->db->lastInsertId();
	}

	public function logSql(){}
	//
	public function debug(){
		$this->debug=true;
	}


	//db operatations:
	public function exec($sql){
		return $this->db->exec($sql);
	}
	public function query($sql){
		return $this->db->query($sql);
	}
	public function prepare($sql,$params=null){
		return $this->db->prepare($sql,$params);
	}
}