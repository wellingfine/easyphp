<?php
/*
	
 */
class EP_DB{
	//pdo object
	private $_pdo=null;
	
	private $_charset;
	
	/*
		if driver is manual ,assign a dsn to host so that EP_DB can directly construct a pdo by dsn.
	*/
	public function __construct($conn){
		$this->setConfig($conn);
		if($this->_pdo==null){
			throw new Exception('DB Connection error.');
			return ;
		}
		$this->init();
	}
	
	//config array
	private function setConfig($config){
		$dft=array(
			'driver'=>'mysql',//
			'host'=>'',//
			'dbname'=>'',//
			'user'=>'',//
			'password'=>'',//
			'charset'=>'utf8',
			'port'=>'3306',//
			'persist'=>true
		);
		extract($dft);
		extract($config,EXTR_OVERWRITE);
		
		$this->_charset=$charset;
		
		$driver=strtolower($driver);
		switch($driver){
			//if driver is manual then use host for dsn.
			case 'manual':
				$dsn=$host;
				break;
			//sqlite is special.
			case 'sqlite':
				//$host is a full path filename
				$dsn="sqlite:$host";
				break;
			case 'odbc'://TODO:
				$dsn="";
				break;
			//TODO:some other db is the same as mysql?
			//PGSQL,MYSQL,cubrid,
			default :
				$dsn="$driver:host=$host;dbname=$dbname;port=$port";
				break;
		}
		$this->_pdo=new PDO($dsn, $user, $password,array(
			PDO::ATTR_PERSISTENT=>$persist,
		));
		
	}
	//init pdo's attribute
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
	
	function binds(){
		
	}
	function update($kv,$where){
		
	}
	function select(){
		
	}
	function delete(){
		
	}
	function insert(){
		
	}
}

