<?php
/*
模板语法：
表达式需要在同一行
变量：{$var}
流程：
{if $expression}
[{(else|elseif $expression)}]
{/if}

{for $arr as $v=>$k}
{/for}

{* comment *}



 */
class EP_TemplateEngine{

	public $tpl=null;
	public $php=null;

	//parsing postion.
	private $pos=0;

	//记录开始剪切模板的位置
	private $cutPos=0;

	// total count
	private $count=0;
	public function __construct(){

	}

	//获取下一个变量，并将指针移到下一个token开始
	//[0-9a-z_]+
	function getToken(){
		

		//清除空格
		while($this->tpl[$this->pos]==' '){
			$this->pos++;
		}
		$startPos=$this->pos;
		while ($this->tpl[$this->pos]!=' ') {
			
		}

	}
	function getError(){

	}
	//解释变量
	function func_var(){

	}
	//if 条件判断
	function func_if(){

	}
	//循环
	function func_foreach(){

	}
	//注释
	function func_comment(){

	}

	protected function start(){

	}
	//
	protected function preProcess(){
		//将嵌入的PHP标签去掉,防止生成PHP文件时被注入
		$this->tpl=str_replace('<?', '<?php echo "<?";?>', $this->tpl);

		$this->count=strlen($this->tpl);

		$this->php=array();
		$this->pos=0;
		$this->cutPos=0;
	}
	/**
	 * tplstr 输入一个模板字符串，转换成PHP文件
	 * 
	 * return phpStr
	 */
	function complie($tpl){
		///初始化变量
		$this->tpl=$tpl;
		$this->preProcess();

		while ($this->pos>=$this->count) {
			//找左大括号 '{'
			if($tpl[$this->pos]!='{'){
				$this->pos++;
				continue;
			}else{//如果找到，看下前一个符号是不是反斜杠
				if((@$tpl[$this->pos-1])=='\\'){
					//如果是则说明这个是转义的
					$this->pos++;
					continue;
				}

			}

			$this->php[]=substr($this->tpl,$this->cutPos,($this->pos-$this->cutPos-1));

			$this->pos++;//move on
			//关键字
			$token=$this->getToken();

			//语法结构分支
			switch ($token) {
				case 'if':
					break;
				case 'foreach'
					break;
				default:
					# code...
					break;
			}

		}

		return implode('', $this->php);
	}
}

?>
