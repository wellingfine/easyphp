<?php
/*
	by welling fine
	todo: be more elegance
*/
class EP_Controller{

	//传递到页面的参数
	protected $_views=array();
	
	//if json is null , then use view.
	//if json not null, echo it ! note: if you want to output json this way ,make sure your controller don't  output any strings
	protected $_json=null;

	//due with exception
	//$e: Exception 
	//output readable information to user
	function __exception($expMsg,$filePath,$line,$traceMsg=''){
		//echo $expMsg;
	}

	private function output(){

	}
	function __execute($act,$args=array()){
	
		$actFullName='action'.$act;
		if(method_exists($this,$actFullName)){
			if(!$this->onBeforeExecute($act))return ;

			$ret=call_user_func_array(array($this,$actFullName),$args);
			$this->onAfterExecute($act,$ret);

			if($this->_json==null){// 
				$suc=E::i()->displayView(strtolower($act),$this->_views);
				if(!$suc){//view not found
					E::log('view:'.$act.' not found','warning');
					E::i()->displayView(E::c('_view_not_found'));
				}
			}else{

				if(is_string($this->_json)){
					echo $this->_json;
				}else{
					echo json_encode($this->_json/*, JSON_NUMERIC_CHECK*/);
				}
			}
/*
			//if return nothing then display view;
			if($ret===null){ // return ; return null;
				$suc=E::i()->displayView($act,$this->_views);
				if(!$suc){//view not found
					E::log('view:'.$act.' not found','warning');
					E::i()->displayView(E::c('_view_not_found'));
				}
				return ;
			}
			if(is_array($ret)){
				//array is suppose to output ajax request
				echo json_encode($ret);
			}else {//string or something .echo it!
				echo $ret;
			}	
			*/
		}else{
			$this->onActionUndefined($act);
		}
	}

	//when exception or error
	protected function onError(){

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
		E::i()->displayView(E::c('_action_not_found'));
		//throw new Exception('undefined action ['.$actionName.']');
		E::log('undefined action ['.$actionName.']','error');
	}
}

