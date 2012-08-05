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
	public function EP_DB($config,$charset='utf8'){
		if(is_array($config)){
			$this->setConfig($config);
		}else{
			$this->setDsn($config,$charset);
		}
		if($this->_pdo==null){
			throw new Exception('DB Connection error.');
			return ;
		}
		$this->init();
	}
	
	// specify a dsn 
	private function setDsn($dsn,$charset='utf8'){
		$this->_pdo=new PDO($dsn);
		$this->_charset=$charset;
	}
	//config array
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
	
	private function init(){
		//leave column names to origin case. 
		$this->_pdo->setAttribute(PDO::ATTR_CASE,PDO::CASE_NATURAL);
		//if occur error then throw exception
		$this->_pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
		//Convert numeric values to strings when fetching
		$this->_pdo->setAttribute(PDO::ATTR_STRINGIFY_FETCHES,false);
		//ATTR_EMULATE_PREPARES ,always use prepare statment
		$this->_pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES,true);
		//
		$this->_pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE,PDO::FETCH_ASSOC);
	}
}
