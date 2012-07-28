<?php
class EP_Controller{
	
	//���ݵ�ҳ��Ĳ���
	protected $_views;
	protected $_viewObject;
	
	function __execute($act,$args=array()){
		$actFullName='action'.$act;
		if(method_exists($this,$actFullName)){
			$this->viewObject=new EP_View();
		
			if(!$this->onBeforeExecute($act))return ;
			
			// TODO:action�ķ���ֵҪ����ʲô�أ�
			$viewArgs=call_user_func_array(array($this,$actFullName),$args);
			$this->onAfterExecute($act);
			E::log('create view...','core');
			
			$this->viewObject->show($this->_views);
		}else{
			$this->onActionUndefined($act);
		}
	}
	// ��һ���Զ���ģ��ķ�����������controller ��ʱ�й���ҳ��Ƭ������
	function showPage($name,$args=''){
		$this->_viewObject->render($name,$args);
	}
	
	protected function onBeforeExecute($actionName){
		return true;
	}
	protected function onAfterExecute($actionName){
		return true;
	}
	protected function onActionUndefined($actionName){
		$this->showPage(E::config('not_found_page'));
		//throw new Exception('undefined action ['.$actionName.']');
		E::log('undefined action ['.$actionName.']','error');
	}
}

