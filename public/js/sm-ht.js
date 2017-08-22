$(function(){
	/*****
		2015.4.20
		注册协议切换
	*******/
	$(".m-xylst li").click(function(){
		var index=$(this).index();
			$(this).addClass("on").siblings().removeClass("on");
			$(".m-xyshow .xylst").eq(index).removeClass("none").siblings().addClass("none");

	});
	/*****
		2015.4.20
		登录输入框获得焦点时候边框颜色变化
	*******/
	$(".m-dlboxct .u-dliptitem input").focus(function(){
		$(this).parent().css("border-color","#7b8793")
		$(this).siblings().css("border-color","#7b8793")
	});
	$(".m-dlboxct .u-dliptitem input").blur(function(){
		$(this).parent().css("border-color","#ced6dc")
		$(this).siblings().css("border-color","#ced6dc")
	});
	/*****
		2015.4.20
		首页导航效果
	*******/
	$(".g-pzhd .u-nav li").hover(function(){
		$(this).addClass("on");
	},function(){
		$(this).removeClass("on");
	});
	$(".g-pzhd .u-nav li.m-service").hover(function(){
		$(this).find(".u-fwxlct").removeClass("none");
	},function(){
		$(this).find(".u-fwxlct").addClass("none");
	});
	$(".m-zhsztt").hover(function(){
		$(this).find(".u-fwxlct").removeClass("none");
	},function(){
		$(this).find(".u-fwxlct").addClass("none");
	});
/*****
		2015.4.21
		首页弹框
	*******/
	$(".u-sure").click(function(){
		$(".g-tkbg").addClass("none");
	});
	/*****
		2015.4.21
		表格颜色
	*******/
	$(".m-tab table tbody >tr:even").css("background-color","#f9f9f9");
})