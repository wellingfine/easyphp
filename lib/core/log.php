<?php
/**
 * This log will not clean old logs,you should do it yourself
 * 
 */
class EP_Log{
	
	private $log;
	private $currentSize=0;//当前缓冲区大小
	
	private $bufferSize;//缓冲区总大小
	private $enable=true;
	
	private $name; //日志名称
	
	private $tagFilter;
	private $date='';

	private $dir;
	/*
	* 日志路径必需 ,缓冲区大小，日志文件最大
	*/
	function __construct($dir,$name,$buffer=1024,$tagFilter=null){
		$this->bufferSize=$buffer;

		$this->log=array();
		$this->date=date('Ymd',time());
		$this->name=$name;
		$this->setTagFilter($tagFilter);
		$this->dir=$dir;
	}

	function __destruct(){
		$this->flush();
	}
	/*
		新建一个Log对象，
		可以区分开特殊的日志
	*/
	function newLog($dir='',$name='',$buffer=1024,$tagFilter=null){
		if(empty($dir)){
			$dir=$this->dir;

		}
		return new EP_Log($dir,$name,$buffer,$tagFilter);
	}
	function setTagFilter($tagFilter=null){
		if($tagFilter==null){
			$this->tagFilter=array();
		}else{
			$this->tagFilter=$tagFilter;
		}
	}
	//开关日志的输出
	function setEnable($e=true){
		if($e!==true)$e=false;
		$this->enable=$e;
		return $this;
	}
	function log($content='',$tag='',$name=''){
		//echo "[log::$content]<br>";

		if($name==''){
			$name=$this->name;
		}
		if($content=='')return $this;
		if(!$this->enable)return $this;
		
		if( in_array($tag,$this->tagFilter))return $this;
		
		
		
		if(is_array($content)||is_object($content)){
			$content="  log object:\n".var_export($content,true);
		}
		$log=date('[H:i:s]',time()).$tag.': '.$content;
		$this->currentSize+=strlen($log);
		$this->log[$name][]=$log;

		if($this->currentSize>$this->bufferSize){
			$this->flush();
		}
		return $this;
	}
	/*
		在同时并发时手动调用，有可能打乱日志的顺序，但可以马上看到日志
	*/
	function flush($name=''){
		if($name==''){
			foreach ($this->log as $k=>$l) {
				if(empty($k))continue;
				$this->flush($k);
				
			}
			$this->log=array();
			return ;
		}
		if(empty($this->log[$name])){
			return ;
		}
		
		

		$fp=null;
		$path=$this->dir.$name.'_'.$this->date.'.log';

		if (file_exists($path)) {
			$fp = fopen($path, 'a');
		}else{
			$fp = fopen($path, 'a');
			//make every can write
			chmod($path, 0666);
		}
		//
		$log=$this->log[$name];
		if ($fp && flock($fp, LOCK_EX)){
			if(count($log)!=0){
				$r=fwrite($fp, implode("\n",$log)."\n");
			}
			//print_r(implode("\r\n",$this->log));
			flock($fp, LOCK_UN);
			fclose($fp);
		}
		$this->currentSize=0;
		$this->log=array();
	}
}