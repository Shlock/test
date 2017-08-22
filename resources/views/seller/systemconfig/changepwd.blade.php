@extends('seller._layouts.base')
@section('css')
@stop
@section('content')
	<div class="p20">
		<div class="m-zjgltbg">
			<div class="p10">						
				<p class="f-bhtt f14 clearfix">
					<span class="ml15 fl">修改密码</span>
					<a href="{{ u('SystemConfig/index') }}" class="fr mr15 btn f-bluebtn" style="margin-top:8px;">返回</a>
				</p>
				<div class="m-quyu1">
					<div class="m-inforct" style="padding-top:78px;width:500px;">
					@yizan_begin
					<yz:form id="yz_form" action="updatepwd">
						<yz:fitem name="idcardSn" label="身份证号码" type="text">
							{{ substr_replace($data['authenticate']['idcardSn'],'**********',7,10) }}
						</yz:fitem> 
						<yz:fitem name="idcardSn" label="证件信息" append="1">
							<span class="ts">请填写完整的身份证号码</span>
						</yz:fitem>
						<yz:fitem name="mobile" label="手机号码"></yz:fitem>  
						<yz:fitem name="name" label="短信验证码" > 
							<div class="f-boxr">
								<div class="u-iptboxct fl">
									<input type="text" name="verifyCode" style="width:100px;margin-top:0px;" placeholder="验证码">
								</div>
								<a href="javascript:;" class="btn f-btn fl ml10 verify" style="width: 120px;margin-top:1px;line-height:28px;">获取验证码</a>
							</div> 
						</yz:fitem> 
						<yz:fitem name="pwd" label="新密码" append="1">
							<span class="ts">请输入新密码</span>
						</yz:fitem>
						<yz:fitem name="pwdold" label="确认密码" append="1">
							<span class="ts">请再次输入密码</span>
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
		$.post("{{ u('public/verify') }}",{mobile:mobile,type:'repwd',vertype:'mobileverify'},function(result){ 
		},'json');
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
