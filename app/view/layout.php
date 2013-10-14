<style>
body{background-color:#225599;}
#box{width:800px;margin:0 auto;}
#header{width:100%;height:80px;background-color:#FFF}
#left ,#right:{float:left;}
#left:{width:200px;height:400px}
#right:{width:580px;height:400px}
#footer:{clear:left;width:100%;height:50px}
</style>

<div id="box">
<div id="header"><?php $this->block('header');echo 'default header';$this->endBlock();?></div>
<div id="left"></div>
<div id="right"></div>
<div id="footer"></div>
</div>
<?php

?>