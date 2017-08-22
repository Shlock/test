$(function(){
	var i5img_w = $(".x-index5 li .x-naimg").width();
	$(".x-index5 li .x-naimg").css("height",i5img_w);

	// 商家收藏
	$(".x-sjsc").touchend(function(){
		if($(this).hasClass("on")){
			$(this).removeClass("on");
		}else{
			$(this).addClass("on");
		}
	});
	// 清除历史记录
	$(".x-clearhis").touchend(function(){
		$(this).siblings().slideUp('fast', function() {
            $(this).remove();
        });
        $(this).html("暂无搜索记录");
	});
	// 删除地址
	$(".x-address .x-delico").touchend(function(){
		$(this).parents(".x-address").slideUp('fast', function() {
            $(this).remove();
        });
	});
	// 背景
	$(".x-bgfff").css("min-height",$(window).height()-47);
	$(".x-fwtype .x-typelst").css("min-height",$(window).height()-224);
	$(".x-pdel").css("min-height",$(window).height()-92);
	// 菜单数量加减
	 $(".x-fwtype .x-num .x-cartico").click(function(){
	 	var count = $(this).siblings(".numr").find(".count");
        $(count).text(1);
        $(this).hide();
        $(this).siblings(".numr").show();
	 });
    $(".x-num .jia").click(function(){
        var count = $(this).siblings(".count");
        var j=$(count).text(); 
        var i= parseInt(j);
        i=i+1; 
        if(i==0){
            $(count).text(1);
        }
        $(count).text(i);
    });
    $(".x-num .jian").click(function(){
        var count = $(this).siblings(".count");
        var j=$(count).text(); 
        var i= parseInt(j); 
        i=i-1; 
        $(count).text(i);
        if(i<=0){
            $(count).text(1);
            $(this).parents(".x-typelst .numr").hide();
            $(this).parents(".numr").siblings(".x-cartico").show();
        }
    });
	// 关闭规格弹框
	$(".x-closebg").touchend(function(){
		$(this).parents(".f-bgtk").fadeOut();
	});
	$(".x-probox .x-closeico").touchend(function(){
		$(this).parents(".f-bgtk").fadeOut();
	});
	// 显示规格弹框
	$(".x-tosize").touchend(function(){
		$(".size-frame").removeClass("none");
		$(".size-frame").fadeIn();
	});

});
