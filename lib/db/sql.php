<?php
/**
 * sql 语句构造器，只做SQL的构造工作
 */
class EP_SQL{
	private $tmpSql=array();

	private $tableName;
	function __construct($name){
		$this->tableName=$name;
	}
	function select($columns='*',$addFrom=true){
		$this->tmpSql[]='select '.$columns;
		if($addFrom){
			$this->tmpSql[]=' from '.$this->tableName.' ';
		}
		return $this;
	}
	function columns($g){

	}
	function i(){

	}

	function limit($start,$limit){
		$start=intval($start);
		$limit=intval($limit);
		$this->tmpSql[]=' limit '.$start.','.$limit;
	}
	/**
	 * 开始生成SQL
	 */
	function make(){
		return implode('', $this->tmpSql);
	}
}
?>