$(function(){
	/******
		2015.3.30
		服务人员选择
	*******/
	$(".m-fwxzct li").click(function(){
		if($(this).hasClass("on")){
			$(this).removeClass("on");
			$(this).addClass("on2").siblings().removeClass("on2");
		}else{
			$(this).addClass("on").siblings().removeClass("on").removeClass("on2");
			$(this).removeClass("on2");
		}
	});
	/*******
		2015.3.20
		优惠券
	*******/
	$(".m-yhq li").click(function(){
		var index=$(this).index();
		$(this).addClass("on").siblings().removeClass("on");
		$(".m-yhqlst ul").eq(index).removeClass("none").siblings().addClass("none");
	});
	/*******
		2015.3.31
		选择城市
	*******/
	$(".m-citylst li").click(function(){
		$(this).addClass("on").siblings().removeClass("on");
	});
/*******
		2015.3.31
		我的评价
	*******/
	$(" .u-xmlst>span").click(function(){
		$(this).addClass("on").siblings().removeClass("on");
	});
	$(".m-tuct ul li .f-colse").click(function(){
		$(this).parent().remove();
	});
	/*******
		2015.3.31
		更多
	*******/
	$(".m-hdanct .f-hd").click(function(){
		var pr=$(this).parent();
		if(pr.hasClass("on")){
			pr.removeClass("on");
			$(this).animate({left:"0px"},500);
		}else{
			$(this).animate({left:"30px"},500);			
			pr.addClass("on");
		}
	});
	/*******
		2015.5.21
		预约服务弹框
	*******/

})
