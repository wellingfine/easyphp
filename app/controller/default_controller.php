<?php
class Default_Controller extends EP_Controller{
	function __construct(){
		
		
	}
	function actionAbc(){
		echo 'actionAbc';
		//$this->view('index',);
	}
	function index(){

	}
	function actionDefault(){
		$this->_views=array(
			'my_view_args'=>array(
				1,2,3,4,5,6,7,8,9
			),
			'aaa'=>array(
				'fff'=>'eeee',
				'aaaaa','bbbbb'
			),
		);
		$this->show('layout');
	}
}
