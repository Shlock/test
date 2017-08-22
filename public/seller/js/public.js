$(document).ready(function(){
    bind_ajax_form();
});

// 绑定ajax_form
function bind_ajax_form()
{
    $(".ajax_form").find(".submit").bind("click",function(){
        $(".ajax_form").submit();
    });
    $(".ajax_form").bind("submit",function(){
        var ajaxurl = $(this).attr("action");
        var query = $(this).serialize();
        $.ajax({ 
            url: ajaxurl,
            dataType: "json",
            data:query,
            type: "POST",
            success: function(ajaxobj){
                if(ajaxobj.status==1)
                {
                    if(ajaxobj.info!="")
                    {
                        $.showSuccess(ajaxobj.info,function(){
                            if(ajaxobj.jump!="")
                            {
                                location.href = ajaxobj.jump;
                            }
                        }); 
                    }
                    else
                    {
                        if(ajaxobj.jump!="")
                        {
                            location.href = ajaxobj.jump;
                        }
                    }
                }
                else
                {
                    if(ajaxobj.info!="")
                    {
                        $.showErr(ajaxobj.info,function(){
                            if(ajaxobj.jump!="")
                            {
                                location.href = ajaxobj.jump;
                            }
                        }); 
                    }
                    else
                    {
                        if(ajaxobj.jump!="")
                        {
                            location.href = ajaxobj.jump;
                        }
                    }                           
                }
            },
            error:function(ajaxobj)
            {
                if(ajaxobj.responseText!='')
                alert(ajaxobj.responseText);
            }
        });
        return false;
    });
}

function ajax_form(ajax_form){
    var ajaxurl = $(ajax_form).attr("action");
    var query = $(ajax_form).serialize();
    $.ajax({ 
        url: ajaxurl,
        dataType: "json",
        data:query,
        type: "POST",
        success: function(ajaxobj){
            if(ajaxobj.status==1)
            {
                if(ajaxobj.info!="")
                {
                    $.closeModal();
                    $.showSuccess(ajaxobj.info,function(){
                        if(ajaxobj.jump!="")
                        {
                            $.router.loadPage(ajaxobj.jump);
                        }
                    }); 
                }
                else
                {
                    if(ajaxobj.jump!="")
                    {
                        $.router.loadPage(ajaxobj.jump);
                    }
                }
            }
            else
            {
                if(ajaxobj.info!="")
                {
                    $.closeModal();
                    $.showErr(ajaxobj.info,function(){
                        if(ajaxobj.jump!="")
                        {
                            $.router.loadPage(ajaxobj.jump);
                        }
                    }); 
                }
                else
                {
                    if(ajaxobj.jump!="")
                    {
                        $.router.loadPage(ajaxobj.jump);
                    }
                }                           
            }
        },
        error:function(ajaxobj)
        {
            if(ajaxobj.responseText!='')
            alert(ajaxobj.responseText);
        }
    });
    return false;
}

/**
 * 判断变量是否空值
 * undefined, null, '', false, 0, [], {} 均返回true，否则返回false
 */
function empty(v){
    switch (typeof v){
        case 'undefined' : return true;
        case 'string'    : if($.trim(v).length == 0) return true; break;
        case 'boolean'   : if(!v) return true; break;
        case 'number'    : if(0 === v) return true; break;
        case 'object'    :
            if(null === v) return true;
            if(undefined !== v.length && v.length==0) return true;
            for(var k in v){return false;} return true;
            break;
    }
    return false;
}

/**
 * 跳
 */
function JumpURL(url,page,w){
    item_pages = 2;
    loading = false;
    if($(page).length > 0&&w!=1){
        if(w==2){
            if(!$(page).hasClass("page-current")){
                $(page).remove();
                loadUrl(url);
            }
        }else{
            if(!$(page).hasClass("page-current"))
                $.router.loadPage(page);
        }
    }
    else{
        loadUrl(url,page,w);
    }
}

function loadUrl(url,page,w){
    if (w == 1) {
        $.showIndicator();
        window.location.href = url;
    }else{
        $.router.loadPage(url);
    }
}

$(document).on("click",".surchbmanagebtn",function(){
    $(".surchbmanage").addClass("z-open");
});

$(document).on("click",".closesurchbmanage",function(){
    $(".surchbmanage").removeClass("z-open");
});