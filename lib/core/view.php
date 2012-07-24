<?php

/*
 * ��ͼ��
 * ʹ�÷���
 */
class EP_View{
	//�Ƚ����
	private $views=array();
	
	private $blocks=array();
	
	private $status;
	private $view_dirs;//��ͼ������·��
	function __construct(){
		$this->view_dirs=array(
			E::config('app_dir').DS.'view'.DS.E::config('controller'),
			E::config('app_dir').DS.'view',
			E::config('project_dir').DS.'view',
		);

	}
	//�ڸ����ؼ�Ŀ¼��������װ��
	function render($name,$args=''){
		if(is_array($args)){
			extract($args);
		}
		if(file_exists($name)){
			require($name);
		}else{
			foreach($this->view_dirs as $d){
				$d=$d.DS.$name.'.php';
				if(file_exists($d)){
					require($d);
					return ;
				}
			}
		}
	}
	//װ��һ����ͼ�������ʾ
	//name ����, $pub �Ƿ��ǹ��е������˽����������
	private function view($name){
		$this->views[]=array(
			'name'=>$name,
			'isEnd'=>false,//����Ƿ��Ѿ����� ��ǰ��view
			'blocks'=>array(),//����
		);
	}
	private function endView(){
		if(empty($this->views))return ;
		end($this->views);
		$vk=key($this->views);
		$this->views[$vk]['isEnd']=true;
		
		$this->render($this->views[$vk]['name']);
		
		array_pop($this->views[$vk]);//����
	}
	private function block($name){
		ob_start();
		//ѹ��ջ
		$this->blocks[]=$name;
	}
	private function endBlock(){
		$html=ob_get_clean();
		$lastName=array_pop($this->blocks);
		end($this->views);
		$vk=key($this->views);
		E::log('endBlock');
		E::log($this->views);
		if($this->views[$vk]['isEnd']){//����Ѿ�����view�������߲���Ч
			if(isset($this->views[$vk]['blocks'][$lastName])){
				echo $this->views[$vk]['blocks'][$lastName];
			}else{
				echo $html;
			}
		}else{//δ�����Ļ���blocks�����ͬ����ô��������Ч
			$this->views[$vk]['blocks'][$lastName]=$html;
		}
	}
	
	//��ʾ�Լ�
	function show($args){
		$this->render(E::config('action'),$args);
	}
}

