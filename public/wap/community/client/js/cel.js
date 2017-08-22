//菜单导航定位	
$(function() {
	var aPos = [];
    $(".x-prolst .scroll_top:last-of-type").css("min-height",$(window).height()-150);
	for (var i = 0; i<$('.scroll_top').length; i++) {
		aPos.push($('.scroll_top').eq(i).offset().top-150);
	}
	$('#scroll_menu li').click(function() {

		$(window).unbind('scroll', fnWinScroll);
		$('#scroll_menu li').removeClass('on');
		$(this).addClass('on');
		fnScroll($(this));
		
	});
	
	var timer = null;
	function fnScroll(obj) {
		clearInterval(timer);
		timer = setInterval(function() {
			var cur = $(document).scrollTop();
			var iSpeed = 0;
			var iTarget = aPos[obj.index()]+12;
			iSpeed = (iTarget - cur) / 7;
			iSpeed = iSpeed > 0 ? Math.ceil(iSpeed) : Math.floor(iSpeed)

			if (Math.abs(iTarget - cur) < 1) {
				clearInterval(timer);
				window.scrollTo(0, iTarget);
				$(window).bind('scroll', fnWinScroll);
			} else {
				window.scrollTo(0, cur + iSpeed);
			}
			$('.scroll_top').children(".x-prott").removeClass("on");
		},
		30);
	}
	function fnWinScroll() {
		var cur = $(document).scrollTop();		
		for (var i = 0; i < aPos.length; i++) {
			if (cur >= aPos[i]) {
				if (cur < aPos[i + 1]) {
					$('#scroll_menu li').removeClass('on');
					$('#scroll_menu li').eq(i).addClass('on');
					$('.scroll_top').children(".x-prott").removeClass("on");
					$('.scroll_top').eq(i).children(".x-prott").addClass("on");
				}
				else {
					$('#scroll_menu li').removeClass('on');
					// $('#scroll_menu li').eq(aPos.length-1).addClass('on');
					$('#scroll_menu li').eq(i).addClass('on');
					$('.scroll_top').children(".x-prott").removeClass("on");
					$('.scroll_top').eq(i).children(".x-prott").addClass("on");
				}
			}
		}
		if($(document).scrollTop()==0){
			$('.scroll_top').children(".x-prott").removeClass("on");
		}
	};
	$(window).bind('scroll', fnWinScroll);
	// $(".layB").slide({ mainCell:".slide",effect:"leftLoop",autoPlay:true });
});
