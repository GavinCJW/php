function box(type , title , message , func = null){
	if(type == 'alert'){
		bootbox.alert({
			size: "small",
        	title: title,
        	message: message, 
            callback: func
        });
	}else if(type == 'confirm'){
		bootbox.confirm({
			size: "small",
			title: title,
        	message: message, 
            callback: func
		});
	}
}

function isJsonFormat( str ) {  
    try {  
        $.parseJSON(str);  
    } catch (e) {  
        return false;  
    }  
    return true;  
} 

function Editor(value){
	var allTableData = $("#TableStyle").bootstrapTable('getData');
	//console.log(allTableData[value].name);
	$("#ID").val(allTableData[value].id);
	$("#A").val(allTableData[value].name);
	$("#B").val(allTableData[value].price);
	$("#C").val(allTableData[value].date);
}

function Delete(value){
	var arr = [value];
	$.post('/welcome/delete',{id: arr},function (result) {if(result) box('alert','确认删除','删除成功！',function(){window.location.reload();});},'json')
	.error(function() {box('alert','确认删除','删除失败！');});
	/*$.ajax({
		url: '/welcome/delete',
		type: 'POST',
		dataType: 'json',
		data: {id: value},
		success: function (result) {
			if(result)
            	box('alert','确认删除','删除成功！',function(){window.location.reload();});
        },
        error : function() {
        	box('alert','确认删除','删除失败！');
        }
    });*/
}

$(function(){
	var table = [
		{checkbox: true,},
		{field: "name",title: "url",align: 'center',sortable: true,},
		{field: "type",title: "上传类型",align: 'center',sortable: true,},
		{field: "data",title: "上传数据",align: 'center',sortable: true,},
		{field: "dataType",title: "返回类型",align: 'center',sortable: true,},
		{field: "result",title: "返回数据",align: 'center',sortable: true,},
		{field: "button",title: "操作",align: 'center',formatter: function(value,row,index){return "<a href='#' onclick='Editor("+index+");' class='glyphicon glyphicon-pencil' style = 'text-decoration:none' data-toggle='modal' data-target='#modal_edit' />&nbsp;&nbsp;<a href='#' onclick='Delete("+row.id+");' class='glyphicon glyphicon-remove' style = 'text-decoration:none'>";},}
	];
	$("#TableStyle").bootstrapTable({
		url:"/User/list",
		searchAlien:"right",//搜索框位置
		search:true,//显示搜索框
		striped: true, //行渐变色
		cache: false, //是否缓存
		pagination:true,//是否显示分页
        showRefresh:true,
		showToggle:true,//切换视图
		showColumns:true,//显示所有列
		uniqueId:"id",//主键
		sortName:"id",//默认排序字段
		toolbar:"#toolbar",
		columns:table,
	});

	$("#btn_add").click(function(){
		if(!isJsonFormat($("#updata").val()) || !isJsonFormat($("#redata").val())){
			box('alert','确认增加','数据格式有误！');
		}
		else{
				var data = {
					url: $("#url").val(),
					type:  $("#uptype").val(),
					data: $("#updata").val(),
					result: $("#redata").val(),
					dataType:  $("#retype").val(),
				};
				console.log(data);
				$.post("/User/insert",data,function (result) {console.log(result);/*if(result) box('alert','确认增加','增加成功！',function(){window.location.reload();});*/},'json')
				.error(function() {box('alert','确认增加','增加失败！');});
		}
	});

	/*$("#btn_delete").click(function(){
		var opts = $("#TableStyle").bootstrapTable('getSelections');  
		if (opts == "") {  
			box('alert','确认删除','请选择要删除的数据！');
		}  else {  
			var idArray = [];  
			for (i in opts) 
				idArray.push(opts[i].id);  
			box('confirm','确认删除',"确定删除：" + idArray + "吗？",
				function(result){ 
					if(result) 
						$.post('/welcome/delete',{id: idArray},function (result) {if(result) box('alert','确认删除','删除成功！',function(){window.location.reload();});},'json')
						.error(function() {box('alert','确认删除','删除失败！');});
			}); 
		}  
	});*/

	$("#btn_edit").click(function(){
		var data = {
			id: $("#ID").val(),
			name:  $("#A").val(),
			price: $("#B").val(),
			date: $("#C").val()
		};
		console.log(data);
		$.post("/welcome/edit",data,function (result) {if(result) box('alert','确认修改','修改成功！',function(){window.location.reload();});},'json')
		.error(function() {box('alert','确认修改','修改失败！');});
	});

	$("#btn_delete").click(function(){
		$.post('https://payment.niudingfeng.com/payment-web/gateway/authSendSms.do', {param1: 'value1'}, function(data, textStatus, xhr) {
			/*optional stuff to do after success */
		});
	});

});