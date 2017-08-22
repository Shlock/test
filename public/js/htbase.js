var YZ = new Object();
YZ.UPDATE_STATUS_FUNC = false;
YZ.UPDATE_STATUS_RELOAD = false;
YZ.AJAX_FROM_SYNC = new Array();

jQuery(function($){
	$.Template = function(html,data){
        return doT.template(html).apply(null,[data]);
	}

    $.tip = function(obj,msg){
        obj.qtip({
            content:msg,
            overwrite:true,
            position:{
                my: 'bottom left',
                at: 'top left',
                adjust:{
                    x:8
                }
            },
            show:{
                ready:true
            },
            style:{
              classes:'qtip-red',
              tip:{
                 corner:'bottom left',
                 mimic: false,
                 width: 10,
                 height: 8,
                 border: true,
                 offset: 8
              }
           }
        });
        obj.addClass('error-tip');
        obj.focus();
    }

    $.ShowAlert = function(msg){
        $.zydialogs.open('<p style="padding:30px;">'+msg+'</p>', {
            boxid:'CONFIRM_URL_WEEBOX',
            width:300,
            title:'操作提示'
        });
	}

	$.ShowConfirm = function(msg,okfun,clearfun,title){
        $.zydialogs.open('<p style="padding:30px;">'+msg+'</p>', {
            boxid:'CONFIRM_WEEBOX',
            width:300,
            title:typeof title !== 'undefined' ? title : '操作提示',
            showClose:false,
            showButton:true,
            showOk:true,
            showCancel:true,
            contentType:'content',
            onOk: function(){
                if(okfun){
                    okfun.call(this,1);
                }
                $.zydialogs.close("CONFIRM_WEEBOX");
            },
            onCancel:function(){
                if(clearfun){
                    clearfun.call(this,0);
                }
                $.zydialogs.close("CONFIRM_WEEBOX");
            }
        });
	}

	$.ShowConfirmToUrl = function(msg,url){
        $.zydialogs.open('<p style="padding:30px;">'+msg+'</p>', {
            boxid:'CONFIRM_URL_WEEBOX',
            width:300,
            title:'操作提示',
            showClose:false,
            showButton:true,
            showOk:true,
            showCancel:true,
            contentType:'content',
            onOk: function(){
                $.zydialogs.close("CONFIRM_URL_WEEBOX");
                location.href = url;
            },
            onCancel:function(){
                $.zydialogs.close("CONFIRM_URL_WEEBOX");
            }
        });
	}

	$.RegionBind = function(id){
		var province,city,area,html,region,selected,rid,pval,cval,aval,showtip;
		var province = $("#"+id);
		pval = province.data("val");
        showtip = province.data("showtip");
		html = '';
        if(showtip == 1){
            html += '<option value="0">请选择</option>';
        }
		for(rid in ZY_CITYS){
			region = ZY_CITYS[rid];
			if(!pval && showtip != 1){
				pval = rid;
			}
			if(rid == pval){
				selected = ' selected="selected"';
			}else{
				selected = '';
			}
			html += '<option value="'+ region.i +'"'+ selected +'>'+ region.n +'</option>';
		}
		province.html(html);

		var cname = province.data("city");
		if(cname){
			city = $("#"+cname);
			var aname = city.data("area");
			if(aname){
				area = $("#"+aname);
				var areaBind = function(){
					pval = province.val();
					cval = city.val();
                    html = '';
                    if(pval < 1 || cval < 1 || typeof(ZY_CITYS[pval].child[cval].child) == "undefined"){
                        area.hide();
                    }else{
                        area.show();
                        aval = area.data("val");
                        if(showtip == 1){
                            html += '<option value="0">请选择</option>';
                        }
                        for(rid in ZY_CITYS[pval].child[cval].child){
                            region = ZY_CITYS[pval].child[cval].child[rid];
                            if(!aval && showtip != 1){
                                aval = rid;
                            }
                            if(rid == aval){
                                selected = ' selected="selected"';
                            }else{
                                selected = '';
                            }
                            html += '<option value="'+ region.i +'"'+ selected +'>'+ region.n +'</option>';
                        }
                    }
					area.html(html);
				}
				city.change(function(){
					city.data('val',city.val());
					areaBind();
				});
			}

			var cityBind = function(){
				pval = province.val();
				html = '';
                if(pval > 0){
                    cval = city.data("val");
                    if(showtip == 1){
                        html += '<option value="0">请选择</option>';
                    }
                    for(rid in ZY_CITYS[pval].child){
                        region = ZY_CITYS[pval].child[rid];
                        if(!cval && showtip != 1){
                            cval = rid;
                        }
                        if(rid == cval){
                            selected = ' selected="selected"';
                        }else{
                            selected = '';
                        }
                        html += '<option value="'+ region.i +'"'+ selected +'>'+ region.n +'</option>';
                    }
                    city.show();
                }else{
                    city.hide();
                }
                city.html(html);
				if(area){
                    areaBind();
				}
			}
			province.change(function(){
				cityBind();
			});
            cityBind();
		}
	}

	$.checkUploadSize = function(obj){
        if($(obj).val() != "" && typeof(obj.files) !== "undefined") {
            var file = obj.files[0];
            if(file.size > 2097152){
                alert("请选择小于 2M 的图片");
                event.stopPropagation();
                event.stopImmediatePropagation();
                return false;
            }
        }
    }
});