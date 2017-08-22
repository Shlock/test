$(document).on("click","#getCode",function(event){
	event.preventDefault();
	event.stopPropagation();
	event.isImmediatePropagationStopped();
	
	var mobile = $("#mobile").val();
	var reg = /^1\d{10}$/;
	if(!reg.test(mobile)){
		alert("请输入正确的手机号码");
		return false;
	}
	$.lastTime();
	$.post(getcode_url,{mobile:mobile},function(res){
		/*if(res.code == 0){
			$.showSuccess(res.msg);
		}else{
			$.showError(res.msg);
		}*/
		if(res.code == 0){
			$.showError(res.msg);
		}else{
			$.showError(res.msg);
		}
	},"json");
})

//表单判断
$.vilidate = function(){
	var mobile = $("#mobile").val();
	var code = $("#verifyCode").val();
	var pwd = $("#password").val();
	var repwd = $("#rePassword").val();
	var reg = /^1\d{10}$/;
	if(!reg.test(mobile)){
		alert("请输入正确的手机号码");
		return false;
	}
	if(code.length != 6){
		alert("验证码输入错误");
		return false;
	}
	var pwd_len = pwd.length;
	if(pwd_len > 20 && pwd_len < 5){
		alert("请输入5-20位密码");
		return false;
	}
	if(pwd != repwd){
		alert("两次密码输入不一致");
		return false;
	}
	return true;
	
}

//倒计时
var wait = 60;//获取验证码等待时间(秒)
$.lastTime = function(){
	if (wait == 0) {
		$("#getCode").attr("disabled",false).css("background-color","#fd7da8");
		$("#getCode").html("重新发送");
		wait = 60;
	} else {
		$("#getCode").attr("disabled",true).css("background-color","gray").css("font-size","10px");//倒计时过程中禁止点击按钮
		$('#getCode').html(wait + " 秒后重新获取");//改变按钮中value的值
		wait--;
		setTimeout(function() {
		$.lastTime();//循环调用
		},1000)
	}
}

//订单完成
$.confirmOrder = function(oid) {
	if(oid > 0){
		$.post(conOrder_url,{id:oid},function(res){
			if(res.code == 0){
				$.showSuccess(res.msg);
				window.location.reload();
			}else{
				$.showError(res.msg);
			}
		},'json');
	}
}

//删除订单
$.delOrder = function(oid) {
	if(oid > 0){
		$.post(delOrder_url,{id:oid},function(res){
			if(res.code == 0){
				alert(res.msg);
				window.location.reload();
			}else{
				$.showError(res.msg);
			}
		},'json');
	}
}

//取消订单
$.cancelOrder = function(oid) {
	if(oid > 0){
		$.post(canOrder_url,{id:oid},function(res){
			if(res.code == 0){
				$.showSuccess(res.msg);
				window.location.reload();
			}else{
				$.showError(res.msg);
			}
		},'json');
	}
}
