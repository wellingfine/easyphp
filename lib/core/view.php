<?php

/*
 * 视图类
 * 使用方法
 */
class EP_View{
	//先进后出
	private $views=array();
	
	private $blocks=array();
	
	private $status;
	private $view_dirs;//视图的搜索路径
	function __construct(){
		$this->view_dirs=array(
			E::config('app_dir').DS.'view'.DS.E::config('controller'),
			E::config('app_dir').DS.'view',
			E::config('project_dir').DS.'view',
		);

	}
	//装载一个视图组件并显示
	//name without '.php'
	private function view($name){
		$this->views[]=array(
			'name'=>$name,
			'isEnd'=>false,//标记是否已经结束 当前的view
			'blocks'=>array(),//块区
		);
	}
	private function endView(){
		if(empty($this->views))return ;
		end($this->views);
		$vk=key($this->views);
		$this->views[$vk]['isEnd']=true;
		
		//a view reach its end,so render and pop it up 
		$this->render($this->views[$vk]['name']);
		array_pop($this->views[$vk]);
	}
	private function block($name){
		ob_start();
		//压入栈
		$this->blocks[]=$name;
	}
	private function endBlock(){
		$html=ob_get_clean();
		$lastName=array_pop($this->blocks);
		end($this->views);
		$vk=key($this->views);
		//E::log('endBlock');
		//E::log($this->views);
		if($this->views[$vk]['isEnd']){//如果已经结束view，后来者不生效
			if(isset($this->views[$vk]['blocks'][$lastName])){
				echo $this->views[$vk]['blocks'][$lastName];
			}else{
				echo $html;
			}
		}else{//未结束的话，blocks如果有同名那么后来者生效
			$this->views[$vk]['blocks'][$lastName]=$html;
		}
	}	
	//search view with name
	//$name:a full path name or view's name without '.php'
	//return true if found else return false
	function render($name,$args=''){
		//empty means kiding :)
		if($name=='')return true;
		if(is_array($args)){
			extract($args);
		}
		if(file_exists($name)){
			require($name);
			return true;
		}else{
			foreach($this->view_dirs as $d){
				$d=$d.DS.$name.'.php';
				if(file_exists($d)){
					require($d);
					return true;
				}
			}
		}
		return false;
	}
}

