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
	private $config;//just read .
	
	private $logObject;
	
	private $viewObject;
	
	private static $instance=null;
	
	private $classPaths=array();
	
	//store used models 
	//we can use this way to avoid creating static Model class,make your code more clean ,but do the same thing.
	//notice!: a standar model's constructor must be no args,or has default value
	private $_models=array();
	/*
		constructor ,ready to go . Should not contain other class's init
	*/
	private function __construct($config){
		$this->config=$config;
		
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
		//load app config ,and overwrite globalconfig
		$appConfig=require_once($this->config['app_dir'].DS.'config.php');
		foreach($appConfig as $k=>$v){
			$this->config[$k]=$v;
		}
		//load all classes
		require_once('classes.php');
		
		
		//init log
		$this->logObject=new EP_Log(
			$this->config['app_dir'].DS.'logs'.DS.$this->config['log_name'],$this->config['log_bufferSize'],
			$this->config['log_maxSize'],
			$this->config['log_tagFilter']
		);
		$this->logObject->setEnable($this->config['log_enable']);

		$this->viewObject=new EP_View();
		$this->viewObject->importDir($this->config['project_dir'].DS.'view');

		set_exception_handler(array($this,'exceptionHandler'));
		//set_error_handler(array($this,'exceptionHandler'));
	}
	public function exceptionHandler($e){
		E::log('Exception:'.$e->getMessage().' file:'.$e->getFile().' line:'.$e->getLine(),'error')->flush();
	}
	private function autoLoad($className){
		$ret=E::loadFile($className,array(
			$this->config['app_dir'].DS.'model',
			$this->config['project_dir'].DS.'model',
			$this->config['lib_dir'].DS.'modules',
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
		//set app dir
		$this->config['app_dir']=$this->config['project_dir'].DS.'apps'.DS.$appname;
		$this->config['app_name']=$appname;
		if(!file_exists($this->config['app_dir'])){
			//app not found
			//TODO: viewObject not init 
			E::log('app ['.$appname.'] is not exsit.');
			$this->displayView($this->config['app_not_found']);
			return ;
		}
		
		//init when app_dir is set
		$this->initApp();
		$this->viewObject->importDir($this->config['app_dir'].DS.'view');
		$controllerName='default';
		$actionName='default';
		//url rewrite
		if($this->config['route_enable']===true){
			require_once($this->config['lib_dir'].DS.'core'.DS.'route.php');
			EP_Route::dispatch($controllerName,$actionName);
		}else{
			// use $_GET only if route is disable
			$controllerName=E::get('controller','default');
			$actionName=E::get('action','default');			
		}
		//Role Base Access Control start
		if($this->config['rbac_enable']===true){
			require_once($this->config['lib_dir'].DS.'core'.DS.'rbac.php');
			
			if(!EP_Rbac::identify($controllerName,$actionName)){
				E::log('Access deny :role forbiden. Please login first.','error');
				//$this->displayView($this->config['rbac_failed_page']);
				header('Location: '.$this->config['rbac_failed_url']);
				return ;
			}
		}
		
		//write down current ctrl and act
		$this->config['controller']=$controllerName;
		$this->config['action']=$actionName;
		//E::log("$__starttime {$appname}[{$controllerName}/{$actionName}]",'core');
		E::log("begin:{$appname}[{$controllerName}/{$actionName}]",'core');
		
		$controller=$controllerName.'_controller';
		
		//装载controller
		$this->loadFile($controller,$this->config['app_dir'].DS.'controller');

		//View
		$this->viewObject->importDir($this->config['app_dir'].DS.'view'.DS.$controllerName);

		if(class_exists($controller,false)
		//||interface_exists($controller)
			){
			$controller=new $controller();
			$controller->__execute($actionName);
		}else{
			E::log('controller ['.$controller.'] is not exsit.','error');
			$this->displayView($this->config['controller_not_found']);
		}
		//ensure to flush the log.
		E::log('used '.round( microtime(true)-$__starttime,5 ).'s','core')->flush(true);
	}
	
	//directly call viewObject
	public function displayView($viewName,$args=null,$manual_dir=''){
		return $this->viewObject->render($viewName,$args,$manual_dir);
	}
	
	//set user and role.
	//role is a name that you deside,but remember to write it in the app_dir/acl.php
	public function setUser($user,$role){
		$_SESSION[$this->config['rbac_sessionKey']]=$user;
		$_SESSION[$this->config['rbac_roleSessionKey']]=$role;
	}
	//get user by key or return a usr array
	public function getUser($key=''){
		$usrinfo=$this->get($this->config['rbac_sessionKey'],array(),$_SESSION);
		if($key!=''){
			return $this->get($key,'',$usrinfo);
		}
		return $usrinfo;
	}
	public function getRole(){
		return $this->get($this->config['rbac_roleSessionKey'],'',$_SESSION);
	}
//-------------------------static function below.
	public static function db(){
		
		//return new 
	}
	//end up session block ,so next session can go on.
	public static function end(){
		session_write_close();
	}
	public static function log($content,$tag=''){
		$inst=self::instance();
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
	public static function instance($config=null){
		if(!self::$instance){
			self::$instance= new E($config);
		}
		return self::$instance;
	}
	public static function config($item=''){
		if($item==''){
			return self::$instance->config;
		}else{
			return isset(self::$instance->config[$item])?self::$instance->config[$item]:'';
		}
	}
	//create a model and store to $_models
	//a model should be 
	public static function m($modelName){
		$inst=self::$instance;
		if(isset($inst->_models[$modelName]) ){
			return $inst->_models[$modelName];
		}
		$modelObject=new $modelName();
		$inst->_models[$modelName]=$modelObject;
		return $modelObject;
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
}

?>