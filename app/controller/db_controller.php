<?php
/*
	DB测试
*/
class db_controller extends EP_controller{
	public function actiontest(){
		//$sql=E::d('easyphp')->sql();
		$sql=new EP_SQL();
		$sql
			->p('select * from')
			->p('user')
			->p('where name=')
			->s("w\"el'l'ing");

		echo $sql->make()."<br>";

		$sql->clear();
		$sql->p('insert into user values( ')
			->p('id=')->i('15"0')
			->p(',name=')->s('welling')
			->p(')');
		echo $sql->make();
	}
}