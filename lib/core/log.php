<?php
class EP_Log{
	
	private $log;
	private $currentSize=0;//当前缓冲区大小
	
	private $bufferSize;//缓冲区总大小
	private $enable=true;
	
	private $file; //日志绝对路径
	private $maxSize;//日志文件大小 默认50M
	
	private $tagFilter;
	/*
	* 日志路径必需 ,缓冲区大小，日志文件最大
	*/
	function __construct($file,$buffer=1024,$max=52428800,$tagFilter=null){
		$this->bufferSize=$buffer;
		$this->maxSize=$max;
		$this->log=array();
		
		$this->file=$file;//TODO:检查文件是否合法
		
		$this->setTagFilter($tagFilter);
		return $this;
	}
	function setTagFilter($tagFilter=null){
		if($tagFilter==null){
			$this->tagFilter=array();
		}else{
			$this->tagFilter=$tagFilter;
		}
	}
	function setEnable($e=true){
		if($e!==true)$e=false;
		$this->enable=$e;
		return $this;
	}
	function log($content,$tag=''){
		if(!$this->enable)return $this;
		
		if( in_array($tag,$this->tagFilter))return $this;
		
		if($tag!='')$tag='['.$tag.']';
		if(is_array($content)||is_object($content)){
			$content="\n".var_export($content,true);
		}
		$log=date('[Y-m-d H:i:s]',time()).$tag.': '.$content;
		$this->currentSize+=strlen($log);
		$this->log[]=$log;
		if($this->currentSize>$this->bufferSize){
			$this->flush();
		}
		return $this;
	}
	/*
		check 是否检查文件是否超过额定大小
		在同时并发时手动调用，有可能打乱日志的顺序，但可以马上看到日志
	*/
	function flush($check=false){
		//
		$fp = fopen($this->file, 'a');
		//echo implode("<br>",$this->log)."<br>";
		if ($fp && flock($fp, LOCK_EX)){
			if(count($this->log)!=0){
				$r=fwrite($fp, implode("\n",$this->log)."\n");
			}
			//print_r(implode("\r\n",$this->log));
			flock($fp, LOCK_UN);
			fclose($fp);
		}
		$this->currentSize=0;
		$this->log=array();
		if($check){
			$size=filesize($this->file);
			//echo '<hr>'.$size.'<hr>';
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
					if($i>1000){
						return ;
					}
				}
			}
		}
	}
}