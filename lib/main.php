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
	private function initApp(){
		//װ��Ӧ�ó������ã�����ȫ������
		$appConfig=require_once($this->config['app_dir'].DS.'config.php');
		foreach($appConfig as $k=>$v){
			$this->config[$k]=$v;
		}
		//װ�����к�����
		require_once('classes.php');
		
		
		//��ʼ����־
		$this->logObject=new Log(
			$this->config['app_dir'].DS.'logs'.DS.$this->config['log_name'],$this->config['log_bufferSize'],
			$this->config['log_maxSize'],
			$this->config['log_tagFilter']
		);
		$this->logObject->setEnable($this->config['log_enable']);
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
			echo 'app ['.$appname.'] is not exsit.';
			return ;
		}
		//ȷ��appĿ¼���ʼ��
		$this->initApp();
		
		$controllerName=E::get('controller','default');
		$actionName=E::get('action','default');
		$this->config['controller']=$controllerName;
		$this->config['action']=$actionName;
		E::log("$__starttime {$appname}[{$controllerName}/{$actionName}]",'core');
		$controller=$controllerName.'_controller';
		$this->loadFile($controller,$this->config['app_dir'].DS.'controller');
		
		if(class_exists($controller,false)
		//||interface_exists($controller)
			){
			$controller=new $controller();
			$controller->__execute($actionName);
		}else{
			E::log('controller ['.$controller.'] is not exsit.','error');
		}
		//��֤����־���
		E::log($__starttime.' used '.( microtime(true)-$__starttime ),'core')->flush(true);
	}
	//��ʾ��ͼ
	public function displayView($args){
		$v=new EP_View($args);
		$v->show();
	}
	//����usr��Ϣ
	public function setUser($user){
		$_SESSION[$this->config['acl_sessionKey']]=$user;
	}
	public function getUser(){
		return $this->get($this->config['acl_sessionKey'],array(),$_SESSION);
	}
//-------------------------��̬�����ָ���
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