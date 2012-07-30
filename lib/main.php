<?php
/*
 * �������»��߷ָ�
 * Լ����
 * #.dir ��ʾ���� DS ��path ��ʾ�� DS ��filePath ��ʾ����·��
 * #.����EasyPHP���඼���� EP_ ǰ׺
 */
//global $__starttime;
$__starttime=microtime (true);

class E{
	private $config;//����ֻ��
	
	private $logObject;
	
	private $viewObject;
	
	private static $instance=null;
	
	private $classPaths=array();
	
	
	/*
	��ʼ������Ӧ�ð���������ĳ�ʼ��
	*/
	private function __construct($config){
		$this->config=$config;
		
		//����session
		session_start();
		// ��ֹ magic quotes
        @set_magic_quotes_runtime(0);
        // ���� magic quotes �Զ�ת���������
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
		//�Զ�����
		spl_autoload_register(array($this, 'autoload'));
	}
	//��ʼ�������صĶ���,��start�������
	//configs , logs ,
	private function initApp(){
		//װ��Ӧ�ó������ã�����ȫ������
		$appConfig=require_once($this->config['app_dir'].DS.'config.php');
		foreach($appConfig as $k=>$v){
			$this->config[$k]=$v;
		}
		//װ�����к�����
		require_once('classes.php');
		
		
		//��ʼ��ϵͳ��־
		$this->logObject=new EP_Log(
			$this->config['app_dir'].DS.'logs'.DS.$this->config['log_name'],$this->config['log_bufferSize'],
			$this->config['log_maxSize'],
			$this->config['log_tagFilter']
		);
		$this->logObject->setEnable($this->config['log_enable']);
		//View
		$this->viewObject=new EP_View();
	}
	private function autoLoad($className){
		$ret=E::loadFile($className,array(
			$this->config['app_dir'].DS.'model',
			$this->config['project_dir'].DS.'modules'
		));
		if($ret==false){
			E::log('Can\'t find class ['.$className.']! ','error');
			
		}
	}
	
	//��ʼ�ַ�����
	/*
		���ܵĴ���
		1.app������
		2.controller������
		3.action ������
	*/
	public function start ($appname='index'){
		global $__starttime;
		//�趨app��Ŀ¼
		$this->config['app_dir']=$this->config['project_dir'].DS.'apps'.DS.$appname;
		$this->config['app_name']=$appname;
		if(!file_exists($this->config['app_dir'])){
			//app not found
			E::log('app ['.$appname.'] is not exsit.');
			$this->displayView($this->config['app_not_found']);
			return ;
		}
		//ȷ��appĿ¼���ʼ��
		$this->initApp();
		
		$controllerName='default';
		$actionName='default';
		//url rewrite
		if($this->config['route_enable']===true){
			require_once($this->config['lib_dir'].DS.'core'.DS.'route.php');
			EP_Route::dispatch($controllerName,$actionName);
		}else{
			// ·��û����ʱ����get����
			$controllerName=E::get('controller','default');
			$actionName=E::get('action','default');			
		}
		//Role Base Access Control start
		if($this->config['rbac_enable']===true){
			require_once($this->config['lib_dir'].DS.'core'.DS.'rbac.php');
			if(!EP_Rbac::identify($controllerName,$actionName)){
				E::log('no ')
				$this->displayView($this->config['rbac_failed_page']);
				return ;
			}
		}
		
		//write down current ctrl and act
		$this->config['controller']=$controllerName;
		$this->config['action']=$actionName;
		//E::log("$__starttime {$appname}[{$controllerName}/{$actionName}]",'core');
		E::log("begin:{$appname}[{$controllerName}/{$actionName}]",'core');
		
		$controller=$controllerName.'_controller';
		
		//װ��controller
		$this->loadFile($controller,$this->config['app_dir'].DS.'controller');
		
		if(class_exists($controller,false)
		//||interface_exists($controller)
			){
			$controller=new $controller();
			$controller->__execute($actionName);
		}else{
			E::log('controller ['.$controller.'] is not exsit.','error');
			$this->displayView($this->config['controller_not_found']);
		}
		//��֤����־���->flush()
		//E::log($__starttime.' used '.( microtime(true)-$__starttime ),'core')->flush(true);
		E::log('used '.round( microtime(true)-$__starttime,5 ).'s','core')->flush(true);
	}
	//��ʾ��ͼ
	public function displayView($viewName,$args=''){
		return $this->viewObject->render($viewName,$args);
	}
	//����usr��Ϣ
	public function setUser($user,$role){
		$_SESSION[$this->config['rbac_sessionKey']]=$user;
		$_SESSION[$this->config['rbac_roleSessionKey']]=$role;
	}
	public function getUser(){
		return $this->get($this->config['rbac_sessionKey'],array(),$_SESSION);
	}
	public function getRole(){
		return $this->get($this->config['rbac_roleSessionKey'],'',$_SESSION);
	}
//-------------------------��̬�����ָ���
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
	/*
	 * ��ȡ�����ֵ
	 */
	public static function get($key,$default='',$arr=null){
		if($arr==null){
			$arr=$_GET;
		}
		return isset($arr[$key])?$arr[$key]:$default;
	}
}

?>