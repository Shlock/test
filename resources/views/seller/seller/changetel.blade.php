@extends('seller._layouts.base')
@section('css')
<style type="text/css">
	.ts{margin-left: 5px;color: #999}
</style>
@stop
@section('content')
	<div class="p20">
		<div class="m-zjgltbg">
			<div class="p10">						
				<p class="f-bhtt f14 clearfix">
					<span class="ml15 fl">手机号码更换</span>
					<a href="{{ u('Seller/index') }}" class="fr mr15 btn f-bluebtn" style="margin-top:8px;">返回</a>
				</p>
				<div class="m-quyu1">
					<div class="m-inforct" style="padding-top:78px;width:500px;"> 
						@yizan_begin
						<yz:form id="yz_form" action="updatetel">
							<yz:fitem label="手机号码" type="text">
								{{ substr_replace($data['mobile'],'****',3,4) }}
							</yz:fitem>  
							<yz:fitem name="mobile" label="原手机号"></yz:fitem>  
							<yz:fitem name="pwd" type="password" label="密码确认" append="1">
								<span class="ts">请输入你的密码</span>
							</yz:fitem>
							<yz:fitem name="newMobile" label="更换手机号码" append="1">
								<span class="ts">请输入你的新手机号码</span>
							</yz:fitem> 
							<yz:fitem name="name" label="短信验证码" > 
								<div class="f-boxr">
									<div class="u-iptboxct fl">
										<input type="text" name="verifyCode" style="width:100px;margin-top:0px;" placeholder="验证码">
									</div>
									<a href="javascript:;" class="btn f-btn fl ml10 verify" style="width: 120px;margin-top:1px;line-height:28px;">获取验证码</a>
								</div> 
							</yz:fitem> 
						</yz:form> 					
						@yizan_end 
					</div>
				</div>
			</div>
		</div>
	</div>
@stop

@section('js')
<script type="text/javascript">   
	$('input[name=mobile]').val('');

	$(".verify").click(function(){
		mobile = $("input[name=mobile]").val();
		if(mobile != ""){
			var reg = /^1[\d+]{10}$/;
			if(!reg.test(mobile)){
				alert('请输入正确的手机号码'); 
				return false;
			}
		}else{
			alert("手机号码不能为空");
			return false;
		}  
		time();
		$.post("{{ u('public/verify') }}",{mobile:mobile,type:'reg_check',vertype:'mobileverify'},function(result){ 
			
		},'json');
	});
	var partten = /^\d+$/;  
	$('input[name="mobileold"]').keyup(function(){
		 if(!partten.test($(this).val())){
		    $(this).val(''); 
		  }
	});  
	$('input[name="mobile"]').keyup(function(){
		 if(!partten.test($(this).val())){
		    $(this).val(''); 
		  }
	});  
	var wait = 60; 
	function time() {    
	    if (wait == 0) { 
			$(".verify").removeAttr("disabled") ;
	        $(".verify").text("免费获取验证码"); 
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
@include('seller._layouts.alert')
@stop
