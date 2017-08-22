jQuery(function($){
	$(document).on('click','.table-status',function(){
		var tr = $(this).parents('tr');
		var table = tr.parents('table');
		var controller = table.attr('controller');
        if (!controller) {
            controller = CURR_CONTROLLER;
        }
		var query = new Object();
		query.field = $(this).attr('field');
		query.val = $(this).attr('status');
		query.id = tr.attr('key');
		query.code = tr.find("td").html();//支付方式
		query.ref_module = table.attr('relmodule') ? table.attr('relmodule') : CURR_CONTROLLER;
		query.ref_action = CURR_ACTION;
		var obj = $(this);

		$.post(SITE_URL + '/' + controller + '/updateStatus', query, function(result){
			if(result.status == 1){
				var title = obj.data('status-' + query.val); 
				if(query.val == 0){
					obj.removeClass('fa-check text-success table-status1').addClass('fa-lock table-status0').attr('status',1).attr('title',title);
				}else{
					obj.removeClass('fa-lock table-status0').addClass('fa-check text-success table-status1').attr('status',0).attr('title',title);
				}
				if(YZ.UPDATE_STATUS_FUNC){
					query.val = result.content;
					YZ.UPDATE_STATUS_FUNC.call(null,query);
				}else if(YZ.UPDATE_STATUS_RELOAD){
					setTimeout(function(){
						location.reload(true);
					},1);
				}
			}else{
				noty({text:'更新失败',type:"error",timeout:2000});
			}
		},'json');
	});

	$.TableCheckHandler = function(obj){
		var checked = obj.checked;
		var table = $(obj).parents('table');
		var inputs = $(" > tbody > tr > td:first-child input",table);
		$(" > tbody > tr > td:first-child input",table).each(function(){
			if(!$(this).attr('disabled')){
				if(checked){
					$(this).parent().addClass("checked");
				}else{
					$(this).parent().removeClass("checked");
				}
				this.checked = checked;
			}
		});
	}

	$.RemoveItem = function(obj, url, tip){
		$.ShowConfirm(tip, function(){
			$.post(url, function(result){
				if(result.status){
					$(obj).parents('tr')
						.find('*')
						.attr('disabled', true)
						.removeAttr('onclick')
						.removeAttr('href')
						.addClass('disabled');
                    if(result.url != ""){
                        window.location.href = result.url;
                    }
				}else{
                    $.ShowAlert(result.msg);
				}
			},'json');
		});
	}

	$.RemoveList = function(obj,id,pk,controller,action){
		var ids =  new Array();
		if(typeof id == "undefined" || id == ''){
			id = 'checkListTable';
		}
		$("#" + id + " input:checked[name='key']").each(function(){
			if(!$(this).hasClass('disabled')){
				ids.push(this.value);
			}
		});
		ids = ids.join(',');
		if(ids == ''){
			$.ShowAlert('请选择要删除的项');
			return false;
		}
		$.ShowConfirm('你确定要删除吗？',function(){
			var query = new Object();
			query.id = ids;
			if(typeof controller == "undefined" || controller == ''){
				controller = CURR_CONTROLLER;
			}
			if(typeof action == "undefined" || action == ''){
				action = 'destroy';
			}
			if(typeof pk != "undefined"){
				query.pk = pk;
			}
			$.post(SITE_URL + '/' + controller + '/' + action, query, function(result){
				if(result.status == 1){
					location.reload(true);
				}else{
                    $.ShowAlert(result.msg);
				}
			},'json');
		});
		return false;
	}
});