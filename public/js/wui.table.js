/*
	welling 2012-07-12
*/
var wui={};
(function(w){
	//�൱�ھ�̬����
	var options={
		id:'',
		isAjax:true,//�Ƿ�Զ������	
		data:[],//����
		headers:[],//��ͷ����
		page:'',
		width:500,//���
	}
	/*
	var fieldOptions={
		name:'',
		autoHtml:false,//��Ԫ���Ƿ��Զ�ת��HTML
		key:'key',
		renderer
	}
	filedOptions.name=fieldOptions.key;
	*/
	w.Grid=function(config){
		this.config={};
		$.extend(true,this.config,options,config);
		
		this._create();
	}
	w.Grid.prototype={
		_create:function(){
			var c=this.config;
			var $t=$('#'+c.id);

			//ͷ
			var fieldConfig=[];
			var dftConfig={
				header:'no',
			};
			
			var group='<colgroup>';
			var hstr='<tr>';
			for(var i=0;i<c.fields.length;i++){
				var f=c.fields[i]
				hstr+='<th>'+f.header+'</th>';
				group+='<col width="100px" key="'+f.key+'"/>';
				fieldConfig.push(f.key);
			}
			group+='</colgroup>';
			hstr+='</tr>';
			//����
			var cstr='<table class="table table-striped table-bordered table-condensed">'+group+'<thead>'+hstr+'</thead><tbody>';
			for(var i=0;i<c.data.length;i++){
				var d=c.data[i];
				cstr+='<tr row="r'+i+'">';
				for(var k=0;k<fieldConfig.length;k++){
					var v=d[fieldConfig[k]]==undefined?'':d[fieldConfig[k]];
					cstr+='<td style="width:120px">'+v+'</td>';
				}
				
				cstr+='</tr>';
			}
			cstr+='</tbody></table>'
			console.log(cstr);
			$t.html(cstr);
			$t=$('table',$t);
			
			$t.width(c.width);
		}
	};
	var name='table';
	var data=[];
})(wui);