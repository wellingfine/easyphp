<?php
/*
	by welling fine
	todo: be more elegance
*/
class EP_Controller{
	
	//传递到页面的参数
	protected $_views=array();
	
	function __execute($act,$args=array()){
		$actFullName='action'.$act;
		if(method_exists($this,$actFullName)){
			if(!$this->onBeforeExecute($act))return ;

			$ret=call_user_func_array(array($this,$actFullName),$args);
			$this->onAfterExecute($act,$ret);

			if($ret===null){// ret===null=> ret=null or return; ,means default 
				//manually set null ,don't render view.
				if($this->_views!==null){
					$suc=E::instance()->displayView($act,$this->_views);
					if(!$suc){//view not found
						E::log('view:'.$act.' not found','warning');
						E::instance()->displayView(E::config('view_not_found'));
					}
				}
			}else{
				//array is suppose to output ajax request
				if(is_array($ret)){
					echo json_encode($ret);
				}else{ //string or something .echo it!
					echo $ret;
				}
			}
		}else{
			$this->onActionUndefined($act);
		}
	}
	//
	protected function onBeforeExecute($actionName){
		return true;
	}
	//
	protected function onAfterExecute($actionName,$ret){
		return true;
	}
	protected function onActionUndefined($actionName){
		E::instance()->displayView(E::config('action_not_found'));
		//throw new Exception('undefined action ['.$actionName.']');
		E::log('undefined action ['.$actionName.']','error');
	}
}

