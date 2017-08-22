$(function(){
    /*错误提示*/
    $.showError = function(msg, url, title){
        if($.trim(msg) == ""){
            msg = "操作失败";
        }
        $(".showalert .x-tkfontAlart").html(msg);    
        if($.trim(title) == ""){
               title = "错误提示";
        }
        $(".showalert .operation_show_title").html(title); 
        if(typeof url != 'undefined'){
            $("#showalert .operation_show_alert").attr("href",url);
        }
        $(".showalert").removeClass("none");
    }
    /*成功提示*/
    $.showSuccess = function(msg, url, title){
        if($.trim(msg) == ""){
            msg = "操作成功";
        }
        $(".showalert .x-tkfontAlart").html(msg);    
        if($.trim(title) == ""){
               title = "成功提示";
        }
        $(".showalert .operation_show_title").html(title); 
        if(typeof url != 'undefined'){
            $("#showalert .operation_show_alert").attr("href",url);
        } 
        $(".showalert").removeClass("none");
    }
    /*操作提示*/
    $.showOperation = function(msg, url, title){   
        if($.trim(msg) == ""){
            msg = "确定执行该项操作？";
        }
        if($.trim(title) == ""){
            title = "操作提示";
        }
        $(".operation #operation_show .m-tktextare").html(msg);    
        $(".operation .operation_title").html(title);    
        if(typeof url != 'undefined'){
            $("#operation .operation_show_url").attr("href",url);
        }
        $(".operation").removeClass("none");
    }   
     /*处理操作提示*/
    $.tel = function(tel){        
        if($.trim(tel) != ""){
            $(".dhkuangs .tel_url").attr("tel",tel);
        }
        $(".dhkuangs").show();
        // $("#reminder_show").center();
    }
    /*******
        2015.4.1
        关闭弹框
    *******/
    $(".operation_show_alert").touchend(function(event){
        var url = $(this).attr("href");
        if (url != "") {
            window.location.href = url;
        }
       $("#showalert").addClass("none");
	   event.preventDefault();
	   event.stopPropagation();
	   event.stopImmediatePropagation();
	   return false;
    });
    $(".operation_show_no").touchend(function(event){
        var url = $(this).find("a").attr("href");
        if (url != "") {
            window.location.href = url;
        }
        $(".operation").addClass("none");
		event.preventDefault();
		event.stopPropagation();
		event.stopImmediatePropagation();
		return false;
    });
    $(".success_show_no").touchend(function(event){
        var url = $(this).find("a").attr("href");
        if (url != "") {
            window.location.href = url;
        }
        $(".success-show").addClass("none");
		event.preventDefault();
		event.stopPropagation();
		event.stopImmediatePropagation();
		return false;
    });
    $(".error_show_no").touchend(function(event){
        var url = $(this).find("a").attr("href");
        if (url != "") {
            window.location.href = url;
        }
        $(".error-show").addClass("none");
		event.preventDefault();
		event.stopPropagation();
		event.stopImmediatePropagation();
		return false;
    });

    $(".tel_show_no").touchend(function(event){
        $(".dhkuangs").css("display","none");
		event.preventDefault();
		event.stopPropagation();
		event.stopImmediatePropagation();
	    return false;
    });

    /*******
        2015.4.1
        关闭弹框
    *******/
    $(".f-gbgn").touchend(function(event){
        $(".g-tkbg").addClass("none");
		event.preventDefault();
		event.stopPropagation();
		event.stopImmediatePropagation();
	   	return false;
    });


    /*******
        2015.4.1
       电话弹窗
    *******/
    $(".f-navdh").touchend(function (event){
        $.tel();
        // $("#dhkuang_show").center();
        event.preventDefault();
    });

    $(".f-navdh").click(function (event){
        event.preventDefault(); //阻止冒泡
    });

    $('.dhkuang_show_no').touchend(function (event){
         $("#dhkuang").addClass('none').hide();
         event.preventDefault();
    });

    $(".dhkuang_show_no").click(function (event){
        event.preventDefault(); //阻止冒泡
    });

})