
this is a view
<br>
<?php
print_r($my_view_args);
echo '<br>';
print_r($aaa);
echo 'view end <br>';

$this->view('layout');

	$this->block('header');
	echo 'my header';
	$this->endBlock();
$this->endView();
?> 
<hr>