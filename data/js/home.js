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

function Editor(value){
	var allTableData = $("#TableStyle").bootstrapTable('getData');
	//console.log(allTableData[value].name);
	$("#A").val(allTableData[value].name);
	$("#B").val(allTableData[value].price);
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
		{field: "id",title: "ID",align: 'center',sortable: true,},
		{field: "name",title: "NAEM",align: 'center',sortable: true,},
		{field: "date",title: "DATE",align: 'center',sortable: true,},
		{field: "price",title: "PRICE",align: 'center',sortable: true,},
		{field: "button",title: "操作",align: 'center',formatter: function(value,row,index){return "<a href='#' onclick='Editor("+index+");' class='glyphicon glyphicon-pencil' style = 'text-decoration:none' data-toggle='modal' data-target='#modal_edit' />&nbsp;&nbsp;<a href='#' onclick='Delete("+row.id+");' class='glyphicon glyphicon-remove' style = 'text-decoration:none'>";},}
	];
	$("#TableStyle").bootstrapTable({
		url:"/welcome/data_list",
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

	$("#btn_delete").click(function(){
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
	});

	$("#File").fileinput({
		showUpload:true,//显示上传按钮
		showRemove:true,//显示移除按钮
		uploadUrl:"/welcome/do_upload",
		enctype : 'multipart/form-data',
	});
});