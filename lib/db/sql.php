<?php
/**
 * sql 语句构造器，只做SQL的构造工作
 */
class EP_SQL{
	private $tmpSql=array();

	private $tableName;
	function __construct($name=''){
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

	function limit($start,$limit){
		$start=intval($start);
		$limit=intval($limit);
		$this->tmpSql[]=' limit '.$start.','.$limit;
	}
	/**
	 * 开始生成SQL
	 */
	function make(){
		return implode(' ', $this->tmpSql);
	}
	//清除tmpsql
	function clear(){
		$this->tmpSql=array();
	}
	/*
	拼接方式:
		纯拼凑
		引起
		转义
	
	**/
	//不转义，不引号
	public function p($str){
		$this->tmpSql[]=$str;
		return $this;
	}
	//拼接数字，自动加转义 纯拼凑+转义
	public function i($int){
		$int=addslashes($int);
		$this->tmpSql[]=$int;
		return $this;
	}

	//拼接SQL字符串 自动加转义
	public function s($str){
		$str=addslashes($str);
		$this->tmpSql[]='\''.$str.'\'';
		return $this;
	}
	
	//字符串加 addslash 转义 or 转引
	public function sa($str,$quote=true){
		$str=addslashes($str);
		if($quote){
			$this->tmpSql[]='\''.$str.'\'';
		}else{
			$this->tmpSql[]=$str;
		}
		return $this;
	}
	//表名
	public function tn(){

		return $this;
	}



}
?>