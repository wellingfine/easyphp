<?php
class EP_One{
	public static $_instance_=null;
	public static function i(){
		if(self::$_instance_){
			return self::$_instance_;
		}else{
			//echo __CLASS__;
			self::$_instance_=new self();
		}
		return self::$_instance_;
	}
}

require('lazy.php');
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
		session_cache_expire($config['_session_life_time']);
		session_start();
		ini_set('session.gc_maxlifetime', $config['_session_life_time']);
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

	//自动装载类 
	private function autoLoad($className){
		$ret=E::loadFile($className,array(
			self::$config['_app_path'].'model',
		));
	}
	
	/*
		显式装载一个 app/model 下的类
		用于model下有多级目录，框架会自动加上php后缀
		$path =  'dir1/dir2/name'
	*/
	public static function load($path){

		//include()
	}


	//start my app
	/*
		probably error
		1.app not found
		2.controller  not found
		3.action not found
		4.view not found
	*/
	public function start (){
		global $__starttime;

		$appPath=self::$config['_app_path'];
		self::$config['_db_config']= require($appPath.'db.php');
		//load all classes
		require('classes.php');
		
		//init log
		$this->logObject=new EP_Log(
			$appPath.'log'.DS,
			self::$config['_log_name'],
			self::$config['_log_bufferSize'],
			self::$config['_log_tagFilter']
		);
		$this->logObject->setEnable(self::$config['_log_enable']);

		$this->viewObject=new EP_View();
		$this->viewObject->importDir(self::$config['_app_path'].'view');

		set_error_handler(array($this,'_errorHandler'));
		E::log('from ip:'.$_SERVER['REMOTE_ADDR'],'core');
		//*----------
		$this->viewObject->importDir(self::$config['_app_path'].'view');
		$controllerName='default';
		$actionName='default';
		//url rewrite
		if(self::$config['_route_enable']===true){
			require(self::$config['_lib_path'].'core'.DS.'route.php');
			EP_Route::dispatch($controllerName,$actionName);
		}else{
			// use $_GET only if route is disable
			$controllerName=E::get('controller','default');
			$actionName=E::get('action','default');
		}

		//Role Base Access Control start
		if(self::$config['_rbac_enable']===true){
			require(self::$config['_lib_path'].'core'.DS.'rbac.php');
			if(!EP_Rbac::identify($controllerName,$actionName)){
				E::log('Access deny :role forbiden. Please login first.','error');
				$this->displayView(self::$config['_rbac_failed_page'],array(
					'url'=>E::get('REQUEST_URI','/',$_SERVER)
				));
				return ;
			}
		}
		//write down current ctrl and act
		self::$config['_controller']=$controllerName;
		self::$config['_action']=$actionName;

		E::log("begin:[{$controllerName}/{$actionName}]",'core');
		
		$controller=$controllerName.'_controller';

		//装载controller
		$this->loadFile($controller,self::$config['_app_path'].'controller');
		//View
		$this->viewObject->importDir(self::$config['_app_path'].'view'.DS.$controllerName);

		if(class_exists($controller,false)
		//||interface_exists($controller)
			){
			$controller=new $controller();
			$this->_cur_controller=$controller;
			try{
				$controller->__execute($actionName);
			}catch(Exception $e){
				E::log('Exception: '.$e->getMessage()."\n".$e->getTraceAsString(),'error');
				E::log(' file: '.$e->getFile()." line: ".$e->getLine(),'error')->flush();
				//call controller's exception

				//$controller->__exception($e->getMessage(),$e->getFile(),$e->getLine());
				$controller->__exception($e->getMessage(),$e->getFile(),$e->getLine(),$e->getTraceAsString());
			}

		}else{

			E::log('controller ['.$controller.'] is not exsit.','error');
			$this->displayView(self::$config['_controller_not_found']);
		}
		//ensure to flush the log.
		E::log('used '.round( microtime(true)-$__starttime,5 ).'s','core')->flush();
	}
	
	//directly call viewObject
	public function displayView($viewName,$args=null,$manual_dir=''){
		return $this->viewObject->render($viewName,$args,$manual_dir);
	}
	
	
//-------------------------static function below.
	//set user(mixed) and role(string).
	//role is a name that you deside,but remember to write it in the app_dir/acl.php
	//24*3600=86400
	public static function setUser($user,$role='',$seconds=86400){
		session_destroy();
		session_write_close();

		session_start();
		session_regenerate_id();
		//session_cache_expire(intval($seconds/60));
		//ini_set('session.gc_maxlifetime', ''.$seconds);
		$_SESSION[self::$config['_rbac_userSessionKey']]=$user;
		$_SESSION[self::$config['_rbac_roleSessionKey']]=$role;

		setcookie( session_name(),session_id(),time()+$seconds,'/');
	}
	//get user by key or return a usr array
	public static function getUser($key=''){
		$usrinfo=self::get(self::$config['_rbac_userSessionKey'],array(),$_SESSION);
		if($key!=''){
			return self::get($key,'',$usrinfo);
		}
		return $usrinfo;
	}
	public static function getRole(){
		return self::get(self::$config['_rbac_roleSessionKey'],'',$_SESSION);
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
	// why use force new?
	public static function d($dsn,$forceNew=false){
		if(!is_array($dsn)){
			$dbConfig=self::c('_db_config');
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
	// public static function t($db,$tableName,$forceNew=false){
	// 	//
	// 	return new EP_Table(E::d($dsn),$tableName);
	// }

	/*
		在注册类中寻找已注册的类别名，如找到则返回
		使得自定的类只要初始化一次即可

		带上obj则表示要初始化
	*/
	public static function m($modelName,$obj=null){
		$inst=self::$instance;
		if(!empty($inst->_models[$modelName]) ){
			return $inst->_models[$modelName];
		}
		if($obj!=null){
			$inst->_models[$modelName]=$obj;
			return $obj;
		}

		try{
			$modelObject=new $modelName();
		}catch(Exception $e){
			E::log($e->getMessage())->flush();
			return null;
		}
		$inst->_models[$modelName]=$modelObject;
		return $modelObject;
	}
	//目的在于可以比较方便的改APP名。。。
	//但如果跨APP用的话，会变成当前请求的APP名
	//TODO:make more functions
	public static function url($ctrlAct=''){
		return '/'.self::$config['_app_name'].'/'.$ctrlAct;
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
		return empty($arr[$key])?$default:$arr[$key];
	}
	public static function post($key,$default=''){
		return empty($_POST[$key])?$default:$_POST[$key];
	}
	public static function request($key,$default=''){
		return empty($_REQUEST[$key])?$default:$_REQUEST[$key];
	}
	
	/*
		对于没有路由规则的URL，获取整条被 / 分隔的 route
		$index 下标 1开始 *(注：因为原始路径是以 /开头 所以split后，第一个元素为空)
		$getKey 如果下标值为空，用 $_GET的Key去填充
		$dft  默认值
	*/
	public static function route($index,$getKey='',$dft=''){
		$route=E::c('_path_info');
		if(isset($route[$index])){
			return $route[$index];
		}else{
			//如果不是Route来的，就从$_GET参数中获取
			return E::get($getKey,$dft);
		}
		
	}
	/*
		文件缓存 *
		获取缓存，设置缓存
		@key array,string 字符串或数组，框架会把它算MD5，然后找缓存
		$val 设置的值
		$time 时间
		3600*24=
	*/
	public static function cache($key,$val=false,$time=3600){

		if(is_array($key)){
			$md5=md5(implode('', $key));
		}else{
			$md5=md5($key);
		}
		$dir=self::c('_cache_path');
		$path=$dir.$md5;

		if((fileperms($dir) & 0666) != 0666){//没权限
			E::log('no permission process cache at '.$dir);
			return false;
		}
		if(!file_exists($path)){
			return false;
		}
		//获取缓存
		if($val===false){
			if((time()-filectime($path))>$time){
				unlink($path);
				return false;
			}
			return file_get_contents($path);
		}else{
			file_put_contents($path, $val);
		}
		return true;
	}
}

?>