<?php
/*
	
 */
class EP_DB{
	//pdo object
	private $_pdo=null;
	
	private $_charset;
	
	/*
		array=> config
		string=> dsn
	*/
	public function EP_DB($config=null,$charset='utf8'){
		if(is_array($config)){
			$this->setConfig($config);
		}else{
			$this->setDsn($config,$charset);
		}
		if($this->_pdo==null){
			throw new Exception('DB Connection error.');
			return ;
		}
	}
	
	// specify a dsn 
	private function setDsn($dsn,$charset='utf8'){
		$this->_pdo=new PDO($dsn);
		$this->_charset=$charset;
	}
	private function setConfig($config){
		$dft=array(
			'driver'=>'mysql',//
			'host'=>'localhost',//
			'database'=>'',//
			'login'=>'',//
			'password'=>'',//
			'charset'=>'utf8',
			'port'=>'3306',//
		);
		extract($dft);
		extract($config,EXTR_OVERWRITE);
		
		$this->_charset=$charset;
		
		switch($driver){
			//sqlite is special.
			case 'sqlite':
				//$host is a full path filename
				$this->_pdo=new PDO("sqlite:$host");
			
			//TODO:some other db is the same as mysql?
			default :
				$this->_pdo=new PDO("$driver:host=$host;dbname=$database;port=$port", $login, $password);
		}
	}
	
	function init(){
		
	}
}
