$(function(){
	$.showError = function(msg, url, title){
if(typeof url != 'undefined'){
            window.location.href = url;
        }
        if($.trim(msg) != ""){
            $(".error-show .m-tsct p").html(msg);    
        }
        if($.trim(title) != ""){
            $(".error-show .error-show-title").html(title);    
        }
        if(typeof url != 'undefined'){
             $(".error-show .m-tsct a").attr("href",url);
        }
        $(".error-show").removeClass("none");
    }
    $.showSuccess = function(msg, url, title){
        if(typeof url != 'undefined'){
            window.location.href = url;
        }
        if($.trim(msg) != ""){
            $(".success-show .m-tsct p").html(msg);    
        }
        if($.trim(title) != ""){
            $(".success-show .success-show-title").html(title);    
        }
        if(typeof url != 'undefined'){
             $(".success-show .m-tsct a").attr("href",url);
        }
        $(".success-show").removeClass("none");
    }

    /*操作提示*/
    $.showOperation = function(msg, url, title){        
        if($.trim(msg) != ""){
            $(".operation #operation_show .m-tktextare").html(msg);    
        }
        if($.trim(title) != ""){
            $(".operation .operation_title").html(title);    
        }
        if(typeof url != 'undefined'){
            $("#operation .operation_show_url").attr("href",url);
        }
        $(".operation").show();
        $("#operation_show").center();
    }
    /*处理操作提示*/
    $.reminder = function(msg){        
        if($.trim(msg) != ""){
            $(".reminder .reminder_msg").html(msg);    
        }
        $(".reminder").show();
        $("#reminder_show").center();
    }
    /*******
        2015.4.1
        关闭弹框
    *******/
    $(".operation_show_no").touchend(function(){
        $(".operation").hide();
    });
    $(".success_show_no").touchend(function(){
        $(".success-show").hide();
    });
    $(".error_show_no").touchend(function(){
        $(".error-show").hide();
    });
    
    
    /*******
        2015.4.1
        关闭弹框
    *******/
    $(".f-gbgn").touchend(function(){
        $(".g-tkbg").addClass("none");
    });


    /*******
        2015.4.1
       电话弹窗
    *******/
    $(".f-navdh").touchend(function (event){
        $("#dhkuang").removeClass('none').show();
        $("#dhkuang_show").center();
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