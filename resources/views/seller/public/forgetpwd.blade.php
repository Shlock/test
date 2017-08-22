@extends('seller._layouts.sign_base')
@section('css')
<style type="text/css">
.system_msg{display: block;text-align: center;line-height: 19px; }
</style>
@stop
@section('content') 
	<div class="p20">
		<div class="m-zjgltbg">
			<div class="p10">						
				<p class="f-bhtt f14 clearfix">
					<span class="ml15 fl">忘记密码</span>
					<a href="{{u('Public/login')}}" class="fr mr15 btn f-bluebtn" style="margin-top:8px;">返回登录</a>
				</p>
				<div class="m-quyu1">
					<div class="m-inforct" style="padding-top:78px;width:417px;">
						<form action="{{ u('Public/checkpwd') }}" id="yz_form">
							<fieldset>
								<ul class="m-spboxlst"> 
									<li class="clearfix">
										<span class="f-tt fl">
											证件号码：
										</span>
										<div class="f-boxr">
											<div class="u-iptboxct fl">
												<input type="text" maxlength="18" onkeyup="this.value=this.value.replace(/\D/g,'')" onafterpaste="this.value=this.value.replace(/\D/g,'')" name="idcardSn" style="width:245px;margin-top:0px;">
											</div>
										</div>
									</li>
									<li class="clearfix">
										<span class="f-tt fl">
											手机号码：
										</span>
										<div class="f-boxr">
											<div class="u-iptboxct fl">
												<input type="text" maxlength="11"  onkeyup="this.value=this.value.replace(/\D/g,'')" onafterpaste="this.value=this.value.replace(/\D/g,'')" name="mobile" style="width:245px;margin-top:0px;">
											</div>
										</div>
									</li>
									<li class="clearfix">
										<span class="f-tt fl">
											短信验证码：
										</span>
										<div class="f-boxr">
											<div class="u-iptboxct fl">
												<input type="text" name="verify" maxlength="6" onkeyup="this.value=this.value.replace(/\D/g,'')" onafterpaste="this.value=this.value.replace(/\D/g,'')"  style="width:100px;margin-top:0px;" placeholder="验证码">
											</div>
											<button class="btn f-btn fl ml10 verify" style="width:135px;margin-top:1px;line-height:28px;">获取验证码</a>
										</div>
									</li>				
									<b class="system_msg"></b> 							
									<li class="clearfix">
										<span class="f-tt fl">
											新密码：
										</span>
										<div class="f-boxr">
											<div class="u-iptboxct fl">
												<input type="password"  name="pwd" style="width:245px;margin-top:0px;" placeholder="请输入新密码" class="u-ipttext">
											</div>
										</div>
									</li>
									<li class="clearfix">
										<span class="f-tt fl">
											确认密码：
										</span>
										<div class="f-boxr">
											<div class="u-iptboxct fl">
												<input type="password" name="pwdold" style="width:245px;margin-top:0px;"  placeholder="请再次输入密码" class="u-ipttext">
											</div>
										</div>
									</li>
								</ul>
								<p class="tc">
									<a href="javascript:;" class="btn f-170btn ml20" id="check">提交</a>
								</p>
							</fieldset>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>
@stop

@section('js')
<script type="text/javascript"> 
var obj = new Object(); 
	obj.mobile  =  "";
	obj.pwd		=  "";   
	obj.verifyCode = ""; 
	obj.idcardSn = ""; 
var  verifynum =  0;
	$(function(){
		$("#title").text("忘记密码");
		$(".verify").click(function(){
			verifynum ++;
			obj.mobile = $("input[name=mobile]").val();
			if(obj.mobile != ""){
				var reg = /^1[\d+]{10}$/;
				if(!reg.test(obj.mobile)){
					alert('请输入正确的手机号码'); 
					return false;
				}
			}else{
				alert("手机号码不能为空");
				return false;
			}  
			time();
			$.post("{{ u('public/verify') }}",{mobile:obj.mobile,type:'repwd',vertype:'mobileverify'},function(result){
				$(".system_msg").text(result.msg);
				$(".system_msg").css("margin-top","-17px");
			},'json');
		});
		$('.f-170btn').click(function(){ 
			obj.verifyCode = $("input[name=verify]").val();
			obj.pwd	       = $("input[name=pwd]").val(); 
			obj.idcardSn   = $("input[name=idcardSn]").val();  
			obj.mobile = $("input[name=mobile]").val();
			obj.pwdold = $("input[name=pwdold]").val()
			obj.type = "back"; 
			if(obj.mobile != ""){ 
				if(!/^(13[0-9]|14[0-9]|15[0-9]|17[0-9]|18[0-9])\d{8}$/i.test(obj.mobile)){ 
					alert('请输入正确的手机号码'); 
					return false;
				}
			}else{
				alert("手机号码不能为空");
				return false;
			}  
			if(verifynum == 0){				
				alert("你还没有获取验证码");
			}else{
				if(obj.idcardSn == ""){
					alert("证件不能为空");
					return false;
				}else{
					var regs = /(^\d{15}$)|(^\d{18}$)|(^\d{17}(\d|X|x)$)/;  
					   if(regs.test(obj.idcardSn) === false)  
					   {  
					       alert("证件输入不合法");  
					       return  false;  
					   }
				}
				if(obj.verifyCode == ""){
					alert("验证码不能为空");
					return false;
				}
				if(obj.verifyCode.length != 6){
					alert("验证码错误");
					return false;
				}
				if(obj.pwd==""){
					alert("密码不能为空");
					return false;
				}else{
					if(obj.pwd.length < 6 ){
						alert("密码长度至少6位");
						return false;
					}else if(obj.pwd != obj.pwdold){
						alert("两次输入密码不一致");
						return false;
					} 
				}
				$.post("{{ u('public/checkpwd') }}",obj,function(result){    
					if(result['code'] == 0 ){
						window.location="{{ u('public/checkpwds') }}";
					}else {
						alert(result.msg);
					}
			},'json');
			}
		});
	});
	var wait = 60; 
	function time() {    
	    if (wait == 0) { 
			$(".verify").removeAttr("disabled") ;
	        $(".verify").text("免费获取验证码"); 	        
			$(".system_msg").text("");
			$(".system_msg").css("margin-top","");
	        wait = 60; 
	    } else { 
	        $(".verify").attr('disabled',"true");
	        $(".verify").text(wait + "秒后获取验证码");  
	        wait--; 
	        setTimeout(function () {
	            time();
	        },
	        1000)
	    }
	}
</script> 
@stop
