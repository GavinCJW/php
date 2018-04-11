function box(type , title , message , func = null , size = "small"){
	if(type == 'alert'){
		bootbox.alert({
			size: size,
        	title: title,
        	message: message, 
            callback: func
        });
	}else if(type == 'confirm'){
		bootbox.confirm({
			size: size,
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
	$("#edit_ID").val(allTableData[value].id);
	$("#edit_url").val(allTableData[value].url);
	$("#edit_uptype").val(allTableData[value].type == 'GET' ? 0 : 1);
	$("#edit_updata").val(allTableData[value].data);
	$("#edit_retype").val(allTableData[value].dataType == 'JSON' ? 0 : 1);
	$("#edit_redata").val(allTableData[value].result);
}

function Delete(value){
	$.post('/User/delete',{id: value},function (result) {if(result) box('alert','确认删除','删除成功！',function(){window.location.reload();});},'json')
	.error(function() {box('alert','确认删除','删除失败！');});
}

function Show(value){
	$.post('/User/show',{id: value},function (result) {if(result) $("#data_show").text(result);})
	.error(function() {box('alert','接口详情','获取失败！');});
}

$(function(){
	var table = [
		{checkbox: true,},
		{field: "url",title: "url",align: 'center',sortable: true,},
		{field: "type",title: "上传类型",align: 'center',sortable: true,},
		{field: "data",title: "上传数据",align: 'center',sortable: true,},
		{field: "dataType",title: "返回类型",align: 'center',sortable: true,},
		{field: "result",title: "返回数据",align: 'center',sortable: true,},
		{field: "button",title: "操作",align: 'center',formatter: function(value,row,index){return "<a href='#' onclick='Editor("+index+");' class='glyphicon glyphicon-pencil' style = 'text-decoration:none;margin: 0 3px;' data-toggle='modal' data-target='#modal_edit' /><a href='#' onclick='Delete("+row.id+");' class='glyphicon glyphicon-remove' style = 'text-decoration:none;margin: 0 3px;'><a href='#' onclick='Show("+row.id+");' class='glyphicon glyphicon-eye-open' style = 'text-decoration:none;margin: 0 3px;' data-toggle='modal' data-target='#modal_show'>";},}
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
					name: $("#url").val(),
					type:  $("#uptype").val(),
					data: $("#updata").val(),
					result: $("#redata").val(),
					dataType:  $("#retype").val(),
				};
				console.log(data);
				$.post("/User/insert",data,function (result) {console.log(result);if(result) box('alert','确认增加','增加成功！',function(){window.location.reload();});},'json')
				.error(function() {box('alert','确认增加','增加失败！');});
		}
	});

	$("#btn_delete").click(function(){
		var opts = $("#TableStyle").bootstrapTable('getSelections');  
		if (opts == "") {  
			box('alert','确认删除','请选择要删除的数据！');
		}  else {  
			var idArray = [];  
			for (i in opts) 
				idArray.push(opts[i].id);  
			box('confirm','确认删除',"确定删除吗？",
				function(result){ 
					if(result) 
						$.post('/User/delete',{id: idArray},function (result) {if(result) box('alert','确认删除','删除成功！',function(){window.location.reload();});},'json')
						.error(function() {box('alert','确认删除','删除失败！');});
			}); 
		}  
	});

	$("#btn_edit").click(function(){
		if(!isJsonFormat($("#edit_updata").val()) || !isJsonFormat($("#edit_redata").val())){
			box('alert','确认增加','数据格式有误！');
		}
		var data = {
			id: $("#edit_ID").val(),
			type: $("#edit_uptype").val(),
			data: $("#edit_updata").val(),
			result: $("#edit_redata").val(),
			dataType:  $("#edit_retype").val(),
		};
		console.log(data);
		$.post("/User/edit",data,function (result) {if(result) box('alert','确认修改','修改成功！',function(){window.location.reload();});},'json')
		.error(function() {box('alert','确认修改','修改失败！');});
	});



});