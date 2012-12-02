<?php
/*
 * 键名用下划线分隔
 * 约定：
 * #.dir 表示不带 DS ，path 表示带 DS ，filePath 表示绝对路径
 * #.凡是EasyPHP的类都带上 EP_ 前缀
 */
//global $__starttime;
$__starttime=microtime (true);

class E{
	private static $config;//just read .
	
	private $logObject;
	
	private $viewObject;
	
	private static $instance=null;
	
	private $classPaths=array();
	
	//cache the db object (EP_DB)
	private $_db_cache=array();

	private $_cur_controller=null;
	//store used models 
	//we can use this way to avoid creating static Model class,make your code more clean ,but do the same thing.
	//notice!: a standar model's constructor must be no args,or has default value
	private $_models=array();
	/*
		constructor ,ready to go . Should not contain other class's init
	*/
	private function __construct($config){
		self::$config=$config;
		
		//start and block a session first,no matter what you want ,
		//call E::end() or session_write_close() to end a seesion
		session_start();
		// forbiden magic quotes
        @set_magic_quotes_runtime(0);
        // process the converted data ,make it back.
        if (get_magic_quotes_gpc()){
            $in = array(& $_GET, & $_POST, & $_COOKIE, & $_REQUEST);
            while (list ($k, $v) = each($in)){
                foreach ($v as $key => $val){
                    if (! is_array($val)){
                        $in[$k][$key] = stripslashes($val);
                        continue;
                    }
                    $in[] = & $in[$k][$key];
                }
            }
            unset($in);
        }
		//register auto load function 
		spl_autoload_register(array($this, 'autoload'));
	}
	//init all about framework, called in start()
	//configs , logs ,
	private function initApp(){
		$appPath=self::$config['app_path'];
		//load app config ,and overwrite globalconfig
		$appConfig=require($appPath.'config.php');
		foreach($appConfig as $k=>$v){
			self::$config[$k]=$v;
		}
		self::$config['db_config']= require($appPath.'db.php');
		//load all classes
		require('classes.php');
		
		
		//init log
		$this->logObject=new EP_Log(
			$appPath.'logs'.DS.self::$config['log_name'],self::$config['log_bufferSize'],
			self::$config['log_maxSize'],
			self::$config['log_tagFilter']
		);
		$this->logObject->setEnable(self::$config['log_enable']);

		$this->viewObject=new EP_View();
		$this->viewObject->importDir(self::$config['project_path'].'view');

		set_error_handler(array($this,'_errorHandler'));
	}
	/*
	 函数参数不对，等非致命令错误的话，会到这里
	 */
	public function _errorHandler ( $errno ,$errstr , $errfile ,$errline){
		E::log($errstr.' file:'.$errfile.' line:'.$errline,'error')->flush();
		//
		if($this->_cur_controller!=null)
			$this->_cur_controller->__exception($errstr,$errfile,$errline);
	}
	private function autoLoad($className){
		$ret=E::loadFile($className,array(
			self::$config['app_path'].'model',
			self::$config['project_path'].'model',
			self::$config['lib_path'].'modules',
		));
	}
	
	//start my app
	/*
		probably error
		1.app not found
		2.controller  not found
		3.action not found
		4.view not found
	*/
	public function start ($appname='index'){
		global $__starttime;
		//set app path
		self::$config['app_path']=self::$config['project_path'].'apps'.DS.$appname.DS;
		self::$config['app_name']=$appname;
		if(!file_exists(self::$config['app_path'])){
			//app not found
			//TODO: viewObject not init 
			E::log('app ['.$appname.'] is not exsit.');
			$this->displayView(self::$config['app_not_found']);
			return ;
		}
		
		//init when app_dir is set
		$this->initApp();
		$this->viewObject->importDir(self::$config['app_path'].'view');
		$controllerName='default';
		$actionName='default';
		//url rewrite
		if(self::$config['route_enable']===true){
			require(self::$config['lib_path'].'core'.DS.'route.php');
			EP_Route::dispatch($controllerName,$actionName);
		}else{
			// use $_GET only if route is disable
			$controllerName=E::get('controller','default');
			$actionName=E::get('action','default');
		}
		//Role Base Access Control start
		if(self::$config['rbac_enable']===true){
			require(self::$config['lib_path'].'core'.DS.'rbac.php');
			
			if(!EP_Rbac::identify($controllerName,$actionName)){
				E::log('Access deny :role forbiden. Please login first.','error');
				$this->displayView(self::$config['rbac_failed_page'],array(
					'url'=>E::get('REQUEST_URI','/',$_SERVER)
				));
				return ;
			}
		}
		
		//write down current ctrl and act
		self::$config['controller']=$controllerName;
		self::$config['action']=$actionName;
		//E::log("$__starttime {$appname}[{$controllerName}/{$actionName}]",'core');
		E::log("begin:{$appname}[{$controllerName}/{$actionName}]",'core');
		
		$controller=$controllerName.'_controller';
		
		//装载controller
		$this->loadFile($controller,self::$config['app_path'].'controller');

		//View
		$this->viewObject->importDir(self::$config['app_path'].'view'.DS.$controllerName);

		if(class_exists($controller,false)
		//||interface_exists($controller)
			){
			$controller=new $controller();
			$this->_cur_controller=$controller;
			try{
				$controller->__execute($actionName);
			}catch(Exception $e){
				E::log('Exception: '.$e->getMessage(),'error');
				E::log(' file: '.$e->getFile().'\n line: '.$e->getLine(),'error')->flush();
				//call controller's exception
				$controller->__exception($e->getMessage(),$e->getFile(),$e->getLine());
			}
		}else{
			E::log('controller ['.$controller.'] is not exsit.','error');
			$this->displayView(self::$config['controller_not_found']);
		}
		//ensure to flush the log.
		E::log('used '.round( microtime(true)-$__starttime,5 ).'s','core')->flush(true);
	}
	
	//directly call viewObject
	public function displayView($viewName,$args=null,$manual_dir=''){
		return $this->viewObject->render($viewName,$args,$manual_dir);
	}
	
	
//-------------------------static function below.
	//set user(mixed) and role(string).
	//role is a name that you deside,but remember to write it in the app_dir/acl.php
	public static function setUser($user,$role=''){
		$_SESSION[self::$config['rbac_userSessionKey']]=$user;
		$_SESSION[self::$config['rbac_roleSessionKey']]=$role;
	}
	//get user by key or return a usr array
	public static function getUser($key=''){
		$usrinfo=self::get(self::$config['rbac_userSessionKey'],array(),$_SESSION);
		if($key!=''){
			return self::get($key,'',$usrinfo);
		}
		return $usrinfo;
	}
	public static function getRole(){
		return self::get(self::$config['rbac_roleSessionKey'],'',$_SESSION);
	}

	public static function i(){
		return self::$instance;
	}
	//get or set configs
	// 
	//like some kind of cache pool ,all stuff are in configs
	// you may think it a cache or config
	public static function c($item=null,$val=null){
		if($item==null){
			return self::$config;
		}else{
			if($val==null){
				return isset(self::$config[$item])?self::$config[$item]:'';
			}else{
				self::$config[$item]=$val;
			}
		}
	}
	//get database connector
	//forceNew : true to create a new connection without cache 
	public static function d($dsn,$forceNew=false){
		if(!is_array($dsn)){
			$dbConfig=self::c('db_config');
			if(!is_array($dbConfig)){
				throw new Exception('DB Config format error!');
			}

			$dsn=self::get($dsn,array(),$dbConfig);
		}
		$inst=self::$instance;
		if($forceNew){
			$db=new EP_DB($dsn);
		}else{
			$md5= md5(serialize($dsn));
			if(isset($inst->_db_cache[$md5])){
				$db=$inst->_db_cache[$md5];
			}else{
				$db=new EP_DB($dsn);
				$inst->_db_cache[$md5]=$db;
			}
		}
		return $db;
	}

	// get table instance
	//ABANDON:
	public static function t($db,$tableName,$forceNew=false){
		//
		return new EP_Table(E::d($dsn),$tableName);
	}
	//3 ways to go :
	//$className get an exist model by nickName
	//$className 
	/*
	public static function mm($className,$nickName,$args){

	}*/
	//create a model and store to $_models
	//a model's constructor should be no args.
	//ABANDON:
	public static function m($modelName){
		$inst=self::$instance;
		if(isset($inst->_models[$modelName]) ){
			return $inst->_models[$modelName];
		}
		try{
			$modelObject=new $modelName();
		}catch(Exception $e){
			E::log($e)->flush();
		}
		$inst->_models[$modelName]=$modelObject;
		return $modelObject;
	}

	//end up session block ,so next session can go on.
	public static function end(){
		session_write_close();
	}
	public static function log($content='',$tag=''){
		$inst=self::$instance;
		$inst->logObject->log($content,$tag);
		return $inst->logObject;
	}
	public static function loadFile($className,$dirs){
		if(is_string($dirs)){
			$dirs=array($dirs);
		}
		$className=strtolower($className);
		foreach($dirs as $d){
			$file=$d.DS.$className.'.php';
			if(file_exists($file)){
				require_once($file);
				return true;
			}
		}
		E::log('class file :'.$className.' not found.','error')->flush();
		return false;
		//throw new Exception('class '.$className.' not found.');
	}

	// call by framework
	public static function createMe($config){
		if(!self::$instance){
			self::$instance= new E($config);
		}
	}

	/*
	 * get array's value by key 
	 * default to use $_GET
	 */
	public static function get($key,$default='',$arr=null){
		if($arr==null){
			$arr=$_GET;
		}
		return isset($arr[$key])?$arr[$key]:$default;
	}
	public static function post($key,$default=''){
		return isset($_POST[$key])?$_POST[$key]:$default;
	}
	public static function request($key,$default=''){
		return isset($_REQUEST[$key])?$_REQUEST[$key]:$default;
	}
	
}

?>