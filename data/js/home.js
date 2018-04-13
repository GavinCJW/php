function Editor(value){
	allTableData = $("#TableStyle").bootstrapTable('getData');
	//console.log(allTableData[value].name);
	$("#ID").val(allTableData[value].id);
	$("#A").val(allTableData[value].name);
	$("#B").val(allTableData[value].price);
	$("#C").val(allTableData[value].date);
}

function Delete(value){
	arr = [value];
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

function Print(value){
	$("#data_show").print({});
}

$(function(){
	table = [
		{checkbox: true,},
		//{field: "id",title: "ID",align: 'center',sortable: true,},
		{field: "name",title: "NAEM",align: 'center',sortable: true,},
		{field: "date",title: "DATE",align: 'center',sortable: true,},
		{field: "price",title: "PRICE",align: 'center',sortable: true,},
		{field: "button",title: "操作",align: 'center',formatter: function(value,row,index){return "<a href='#' onclick='Editor("+index+");' class='glyphicon glyphicon-pencil' style = 'text-decoration:none;margin: 0 3px;' data-toggle='modal' data-target='#modal_edit' /><a href='#' onclick='Delete("+row.id+");' class='glyphicon glyphicon-remove' style = 'text-decoration:none;margin: 0 3px;'><a href='#' onclick='Print("+row.id+");' class='glyphicon glyphicon-print' style = 'text-decoration:none;margin: 0 3px;'>";},}
	];
	$("#TableStyle").bootstrapTable({
		url:"/welcome/data_list",
		searchAlien:"right",//搜索框位置
		search:true,//显示搜索框
		striped: true, //行渐变色
		cache: false, //是否缓存
		showToggle:true,
		pagination:true,//是否显示分页
        showRefresh:true,
		showColumns:true,//显示所有列
		uniqueId:"id",//主键
		sortName:"id",//默认排序字段
		toolbar:"#toolbar",
		columns:table,
	});

	$("#qrcode").qrcode(window.location.href);

	$("#btn_delete").click(function(){
		opts = $("#TableStyle").bootstrapTable('getSelections');  
		if (opts == "") {  
			box('alert','确认删除','请选择要删除的数据！');
		}  else {  
			idArray = [];  
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

    $("#btn_import").fileinput({
		showUpload:true,//显示上传按钮
		showRemove:true,//显示移除按钮
		uploadUrl:"/welcome/importExecl",
		enctype : 'multipart/form-data',
		//allowedFileExtensions: ['xlsx','xls'],
	});

	$("#btn_excel").click(function(event) {
		$("#btn_excel").attr("href","/welcome/exportExcel");
		//window.open("/welcome/exportExcel");
	});

	$("#btn_edit").click(function(){
		data = {
			id: $("#ID").val(),
			name:  $("#A").val(),
			price: $("#B").val(),
			date: $("#C").val()
		};
		console.log(data);
		$.post("/welcome/edit",data,function (result) {if(result) box('alert','确认修改','修改成功！',function(){window.location.reload();});},'json')
		.error(function() {box('alert','确认修改','修改失败！');});
	});

	$('#form_date').datetimepicker({
        format: "yyyy-mm-dd hh:ii:ss",
        weekStart: 1,
        autoclose: true,
        todayBtn: true,
		todayHighlight: 1,
		startView: 2,
		minView: 2,
		forceParse: 0,
		startDate: $("#C").val(),
    });

    bbb = [
		//{field: "id",title: "ID",align: 'center',sortable: true,},
		{field: "name",title: "NAEM",align: 'center',footerFormatter: '合计'},
		{field: "date",title: "DATE",align: 'center',},
		{field: "price",title: "PRICE",align: 'center',
			footerFormatter: function (data) {
				count = 0;
		        for (i in data) {
		            count += parseFloat(data[i].price);
		        }
		        return count;
			}
		},
	];
	$("#aaaa").bootstrapTable({
		url:"/welcome/data_list",
		columns:bbb,
	});

});