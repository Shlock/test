jQuery(function(){
    /*错误提示*/
    jQuery.showError = function(msg, url, title){
        if(jQuery.trim(msg) == ""){
            msg = "操作失败";
        }
        jQuery(".showalert .x-tkfontAlart").html(msg);    
        if(jQuery.trim(title) == ""){
               title = "错误提示";
        }
        jQuery(".showalert .operation_show_title").html(title); 
        if(typeof url != 'undefined'){
            jQuery("#showalert .operation_show_alert").attr("href",url);
        }
        c_alert ();
        jQuery(".showalert").removeClass("none");
    }
    /*成功提示*/
    jQuery.showSuccess = function(msg, url, title){
        if(jQuery.trim(msg) == ""){
            msg = "操作成功";
        }
        jQuery(".showalert .x-tkfontAlart").html(msg);    
        if(jQuery.trim(title) == ""){
               title = "成功提示";
        }
        jQuery(".showalert .operation_show_title").html(title); 
        if(typeof url != 'undefined'){
            jQuery("#showalert .operation_show_alert").attr("href",url);
        } 
        c_alert ();
        jQuery(".showalert").removeClass("none");
    }
    /*拨打电话提示*/
    jQuery.showOrderCancelNotice = function(msg, url, title){
        if(jQuery.trim(msg) == ""){
            msg = "确定执行该项操作？";
        }
        if(jQuery.trim(title) == ""){
            title = "提示";
        }

		if(typeof url != 'undefined'){
			jQuery("#call_operation .call_show_url").attr("href",url);
		}
		
        jQuery("#call_operation .m-tktextare").html(msg);
        jQuery("#call_operation .operation_show_title").html(title);
        //c_alert ();
        jQuery("#call_operation").removeClass("none");
    }
	
	jQuery.closeCallOpera = function(){
		jQuery("#call_operation").addClass("none");
	}


    jQuery.closeOperation = function(){
        jQuery(".operation").addClass("none");
    }

    /*操作提示*/
    jQuery.showOperation = function(msg, url, title){
        if(jQuery.trim(msg) == ""){
            msg = "确定执行该项操作？";
        }
        if(jQuery.trim(title) == ""){
            title = "操作提示";
        }
        jQuery(".operation #operation_show .m-tktextare").html(msg);
        jQuery(".operation .operation_title").html(title);
        if(typeof url != 'undefined'){
            jQuery("#operation .operation_show_url").attr("href",url);
        }
        c_alert ();
        jQuery(".operation").removeClass("none");
    }
    /*处理操作提示*/
    jQuery.tel = function(tel){        
        if(jQuery.trim(tel) != ""){
            jQuery(".dhkuangs .tel_url").attr("tel",tel);
        }
        jQuery(".dhkuangs").show();
        // jQuery("#reminder_show").center();
    }
	//关闭所有弹框
    function c_alert (){
        jQuery(".showalert").addClass("none");
        jQuery(".operation").addClass("none");
    }
    /*******
        2015.4.1
        关闭弹框
    *******/
    jQuery(".operation_show_alert").touchend(function(){
        var url = jQuery(this).attr("href");
        if (url != "") {
            window.location.href = url;
        }
       jQuery("#showalert").addClass("none");
    });
    jQuery(".operation_show_no").touchend(function(){
        var url = jQuery(this).find("a").attr("href");
        if (url != "") {
            window.location.href = url;
        }
        jQuery(".operation").addClass("none");
		jQuery(".call_operation").addClass("none");
    });
    jQuery(".success_show_no").touchend(function(){
        var url = jQuery(this).find("a").attr("href");
        if (url != "") {
            window.location.href = url;
        }
        jQuery(".success-show").addClass("none");
    });
    jQuery(".error_show_no").touchend(function(){
        var url = jQuery(this).find("a").attr("href");
        if (url != "") {
            window.location.href = url;
        }
        jQuery(".error-show").addClass("none");
    });

    jQuery(".tel_show_no").touchend(function(){
        jQuery(".dhkuangs").css("display","none");
    });

    /*******
        2015.4.1
        关闭弹框
    *******/
    jQuery(".f-gbgn").touchend(function(){
        jQuery(".g-tkbg").addClass("none");
    });


    /*******
        2015.4.1
       电话弹窗
    *******/

    jQuery('.dhkuang_show_no').touchend(function (event){
         jQuery("#dhkuang").addClass('none').hide();
         event.preventDefault();
    });

    jQuery(".f-navdh,.dhkuang_show_no,.operation_show_alert,.operation_show_no,.success_show_no,.error_show_no,.tel_show_no").touchend(function (event){
		event.preventDefault();
		event.stopPropagation();
		event.stopImmediatePropagation();
		return false;
    });

})