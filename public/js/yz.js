$(function(){
	/*_______
		Scope:易赞后台左边导航栏高度与右边相同
		End: 2014.12.30
	*/
	var b=Math.max($(".u-lfslider").height(),$(window).height(),$(".u-frct").height());
	$(".u-lfslider").height(b);
	/*_______
		Scope:易赞后台表格，tr颜色
		End: 2014.12.30
		$(".m-tab table tbody >tr:even").css("background-color","#f9f9f9");
	*/
	
	/*_______
		Scope:左边栏点击效果
		End: 2014.12.30
	*/
	$(".u-lfslider .f-tt").click(function(){
        $(".u-lfslider li").removeClass("on");
        var parent = $(this).parent();
		var dq = parent.find(".m-ycejmenu");
        if (dq.length > 0) {
            if(dq.is(':visible')){
                dq.slideUp();
                $(this).find(".f-tt >i").addClass("fa-chevron-up").removeClass("fa-chevron-down");
            }else{
                $(".u-lfslider .f-tt >i").removeClass("fa-chevron-down").addClass("fa-chevron-up");
                $(".u-lfslider .m-ycejmenu").slideUp();
                dq.slideDown();
                parent.addClass("on");
                $(this).find(".f-tt >i").addClass("fa-chevron-down");
            }
        }
	});
	/*_______
		Scope:分类管理展开效果
		End: 2015.1.4
	*/
	$(".u-tabd .u-flt1 .edit").click(function(){
		$(this).parents(".u-flt1").parent().next().toggle();
	});
	/*_______
		Scope:添加规格弹窗
		End: 2015.1.6
	*/
	$(".tjggbtn").click(function(){
		$(".g-addspbox").removeClass("none");
		var height=($(".m-addsptk").height())/2;
		$(".g-addspbox").css("margin-top",-height);
	});
	$(".tjqxbtn").click(function(){
		$(".g-addspbox").addClass("none");
	});
		/*_______
		Scope:分组弹窗
		End: 2015.3.10
	*/
	$(".u-antt a").click(function(){
		$(".g-addspbox").removeClass("none");
		var height=($(".m-addsptk").height())/2;
		$(".g-addspbox").css("margin-top",-height);
	});
	$(".tjqxbtn").click(function(){
		$(".g-addspbox").addClass("none");
	});
	/*_______
		Scope:所有订单导航切换
		End: 2015.3.5
	*/
	$(".u-orderlstnav li").click(function(){
		$(this).addClass("on").siblings().removeClass("on");
	});

	$(".m-stnav li").hover(function(){
		$(this).find(".m-ztbox").removeClass("none");
	},function(){
		$(this).find(".m-ztbox").addClass("none");
	});
	 /*_______
		Scope:时间管理添加
		End: 2015.6.11
	*/
	$(".m-timebtn").click(function(){
		$(".m-bjtimbox .m-zhouct").html("");
		to_html ();	
		var s = $(".grays").text().split(",");
		for (var i=0; i < s.length; i++) {
            var label = $(".m-bjtimbox .m-zhouct label[for='to_"+ $(".grays").text().split(",")[i].replace( /^\s*/, '') +"']");
            label.find('input').checked = false;
            label.find('input').css('color','red');
            label.find('input').attr("disabled","true");
        	// console.log(label);     
        }   
		$(".m-sjdct ul li").each(function(){
            if($(this).hasClass("on")){
                $(this).removeClass("on");
            }
        });
        $(".m-sjglbcbtn").text("保存");
		$(".m-bjtimbox").slideDown();
	});
	// 选择时间段
	$(".m-sjdct li").click(function(){
		if($(this).hasClass("on")){
			$(this).removeClass("on");
		}else{
			$(this).addClass("on");
		}
	});
	
	// 取消添加
	$(".m-quxiaobtn").click(function(){
		$(".m-bjtimbox").slideUp();
		$(".m-timebtn").removeClass("none");
	});
	// 选择人员弹框人员选择
	$(".x-rylst li").click(function(){
		$(this).addClass("on").siblings().removeClass("on");
	});
})