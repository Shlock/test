 $("#J_edit").on('click',function(){
 	if($(this).hasClass("ongoing"))
	{
		$(this).html("编辑").removeClass("ongoing").removeClass("f_999").addClass("f_red");
		$(".item-link").find(".item-btn").hide();
		$(".item-link").find(".del_icon").hide();
		$(".item-link").find(".item-inner").removeClass("no_right_ico");
	}
	else
	{
		$(this).html("完成").removeClass("f_red").addClass("ongoing").addClass("f_999");
		$(".item-link").find(".item-btn").show();
		$(".item-link").find(".del_icon").show();
		$(".item-link").find(".item-inner").addClass("no_right_ico");
	}
 	
   // $.alert('Here goes alert text');
 });