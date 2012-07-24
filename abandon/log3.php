<?php
class Log{
	private static $instance=null;
	
	private $log;
	private $currentSize=0;
	private $bufferSize=1024;
	private $enable=true;
	
	private $file;
	private $maxSize=52428800 ;//50*1024*1024
	

	function __construct(){
		$logConfig=E::$config['log'];
		$this->bufferSize=E::get('bufferSize',$this->bufferSize,$logConfig);
		$this->enable=E::get('enable',$this->enable,$logConfig);
		$this->maxSize=E::get('maxSize',$this->maxSize,$logConfig);
		$this->log=array();
		$this->logDir=E::$config['app_dir'].DS.'logs'.DS;
		$this->file=$this->logDir.'app.log';
		//$this->cache=array();
	}
	public static function instance(){
		if(!self::$instance){
			self::$instance= new Log();
		}
		return self::$instance;
	}
	static function mylog($fileName,$content,$tag=''){
		if(empty($fileName))return false;
		$log=date('[Y-m-d H:i:s',time()).$tag.']: '.$content;
		$fp = fopen(self::instance()->logDir.$fileName, 'a');
		if ($fp && flock($fp, LOCK_EX)){
			fwrite($fp, $log."\n");
			flock($fp, LOCK_UN);
			fclose($fp);
		}
		return true;
	}
	static function log($content,$tag=''){
		
		$inst=self::instance();
		if($tag!='')$tag=' '.$tag;
		if(is_array($content)||is_object($content)){
			$content="\n".var_export($content,true);
		}
		$log=date('[Y-m-d H:i:s',time()).$tag.']: '.$content;
		$inst->currentSize+=strlen($log);
		$inst->log[]=$log;
		if($inst->currentSize>$inst->bufferSize){
			$inst->flush();
		}
		return $inst;
	}
	/*
		check 是否检查文件是否超过额定大小
		在同时并发时手动调用，有可能打乱日志的顺序，但可以马上看到日志
	*/
	function flush($check=false){
		//
		$fp = fopen($this->file, 'a');
		if ($fp && flock($fp, LOCK_EX)){
			fwrite($fp, implode("\n",$this->log)."\n");
			//print_r(implode("\r\n",$this->log));
			flock($fp, LOCK_UN);
			fclose($fp);
		}
		$this->currentSize=0;
		$this->log=array();
		if($check){
			$size=filesize($this->file);
			echo '<hr>'.$size.'<hr>';
			if($size>$this->maxSize){
				$i=1;
				while(true){
					if(!file_exists($this->file.'_'.$i)){
						$ret=rename($this->file,$this->file.'_'.$i);
						if($ret===false){
							Log::log('Log file is overflow. no permission to change file.');//->flush();
						}
						break;
					}
					$i++;
				}
			}
		}
	}
}
print_r(Log::instance());
