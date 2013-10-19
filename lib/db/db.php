<?php
/*
notice:
1.数据表名不能含有"." ,见 normalTableName();
2.when created ,PDO is connecting
TODO:DB要真正执行SQL时才连接
 */
class EP_DB{
	//pdo object
	protected $_pdo=null;
	//execute "set names $_charset;" before any sql;
	protected $_charset;
	
	protected $_config=null;

	protected $isConnect=false;

	protected $isInit=false;

	protected $columns=array();

	//cache all prepared statment.
	protected $prepareStatments=array();

	//table caches
	private $_t_cache=array();
	/*
		if driver is manual ,assign a dsn to host so that EP_DB can directly construct a pdo by dsn.
		$conn:array
	*/
	public function __construct($conn){
		$this->_config=$conn;
		$this->init();
	}
	//to get a  EP_Table object 
	//TODO: Do tables need cache?
	public function t($tableName){
		if(isset($this->_t_cache[$tableName])){
			return $this->_t_cache[$tableName];
		}
		$t=new EP_Table($this,$tableName);
		$this->_t_cache[$tableName]=$t;
		return $t;
	}

	//config array only
	private function setConfig(){
		$driver='mysql';
		$host='127.0.0.1';
		$dbname='';
		$user='';
		$password='';
		$charset='utf8';
		$port='3306';
		$persist=true;

		extract($this->_config,EXTR_OVERWRITE);
		
		$this->_charset=$charset;
		
		$driver=strtolower($driver);

		$attr=array();
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
				$attr[PDO::ATTR_PERSISTENT]=$persist;
				//PDO::MYSQL_ATTR_MAX_BUFFER_SIZE=>1024*1024*50
				$dsn="$driver:host=$host;dbname=$dbname;port=$port";
				break;
		}
		$this->_pdo=new PDO($dsn, $user, $password,$attr);
		
	}
	//connect pdo's attribute
	private function connect(){
		$this->setConfig();

		if($this->_pdo==null){
			throw new Exception('DB Connection error.','DBErr');
			return ;
		}
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
	private function init(){
		if($this->isInit)return ;
		$this->connect();

		$this->exec('set names '.$this->_charset);

		$this->isInit=true;
	}
	public function exec($sql){
		E::log('Execute SQL: '.$sql,'DB');

		$ret= $this->_pdo->exec($sql);
	}
	public function query($sql){
		E::log('Execute SQL: '.$sql,'DB');
		$stm=$this->_pdo->query($sql);
		$stm->setFetchMode(PDO::FETCH_ASSOC);
		try{
			$rows=$stm->fetchAll();

		}catch(Exception $e){
			$rows=$stm->rowCount();
		}
		return $rows;
	}

	// if exec query SQL then return an array of result set
	//if exec update ,delete ,insert  ,then return affectRows
	function prepare($sql,$params=null){
		$pdo=$this->_pdo;
		
		E::log('Execute SQL:'.$sql,'DB');
	
		if(empty($this->prepareStatments[$sql])){
			$stm=$this->prepareStatments[$sql]=$pdo->prepare($sql);

			//default to be assoc. any question?
			$stm->setFetchMode(PDO::FETCH_ASSOC);
		}else{
			$stm=$this->prepareStatments[$sql];
		}
		if(is_array($params)){
			//bind params , pdo's bug ,val must be reference
			foreach ($params as $name => &$val) {
				$type=PDO::PARAM_STR;
				if( is_int( $val ) ){
					$type=PDO::PARAM_INT;//echo 'type: PARAM_INT<br>';
				}else if( is_bool( $val ) ){
	 				$type=PDO::PARAM_BOOL;//echo 'type: PARAM_BOOL<br>';
				}else if( is_null( $val ) ){
	 				$type=PDO::PARAM_NULL;//echo 'type: PARAM_NULL<br>';
	 			}
				//WTEST:
				if(is_numeric($name)){
					$stm->bindValue($name+1,$val,$type);
					E::log("bindValue: ".($name+1)."=>$val ");
				}else{
					$stm->bindParam(':'.$name,$val,$type);
					E::log("bindParam: :$name=>$val ");
				}
			}
		
		}
		//E::log($params,'DB');
		try{
			$execRet=$stm->execute();
		}catch(Exception $e){
			$errInfo=$stm->errorInfo();
			switch ($errInfo[0]) {
				case 'HY093':
					E::log('SQL Error when bind params. [HY093]','DBErr');
					break;
				//case more:
				default:
					E::log('SQL Code:'.$errInfo[0],'DBErr');
					E::log('Database Driver Code:'.$errInfo[1],'DBErr');
					E::log('Driver error message:'.$errInfo[2],'DBErr');
					break;
			}
			
			return false;
		}
		if($execRet===false)return false;

		try{
			//execute update,delete,insert will throw exception, catch it and ignore.
			$rows=$stm->fetchAll();
			return $rows;
		}catch(Exception $e){
			//E::log($e,'dbe');
		}
		return $stm->rowCount();
	}
	//call lastInsertId() when execute successfully
	//some db driver need $name. PDO_PGSQL()  eg.
	public function lastInsertId ($name =null){
		if($this->_pdo==null){
			return -1;
		}
		return $this->_pdo->lastInsertId($name);
	}
	
	public function getPDO(){
		return $this->_pdo;
	}

	/*
	TODO:返回上一次的错误
		
	*/
	public function error(){

	}
	public function sql(){
		return new EP_SQL();
	}}

?>