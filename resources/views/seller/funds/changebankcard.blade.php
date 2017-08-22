@extends('seller._layouts.base')
@section('css')
<style type="text/css">
.parentCls {margin:-40px 110px 0;}
.js-max-input {border: solid 1px #ffd2b2;background: #fffae5;padding: 0 10px 0 10px;font-size:20px;font-weight: bold;color: #ff4400;}
.f-ipt{width: 330px;}
.m-tab table tbody td{padding: 10px 5px;font-size: 12px;text-align: left;} 
</style>
@stop
@section('content')
	<div>
		<div class="m-zjgltbg">
			<p class="f-bhtt f14 clearfix">
				<span class="ml15 fl">更换绑定银行卡号</span>
				<a href="{{ u('Funds/index') }}" class="fr mr15 btn f-bluebtn" style="margin-top:8px;">返回</a>
			</p>
			<div class="p10">
				<!-- 更换绑定银行卡号 -->
				<div class="clearfix mt10">
					<div class="m-yhk m-ghkh" style="width:770px;">
						@yizan_begin
						<yz:list> 
						<table pager="no">  
							<row> 
								<tr class="{{ $list_item_css }}" style="padding: 5px 0">
									<td width="100px;" style="text-align:center">户主</td>  
									<td>{{ $seller['name'] }}（登录后台的帐号，无需输入）</td>   
								</tr>
								<tr class="{{ $list_item_css }}" style="padding: 5px 0">
									<td style="text-align:center">新的银行帐号</td>  
									<td class="parentCls">
										<input type="text" name="bankNo" class="inputElem f-ipt" autocomplete = "off" placeholder="请输入新的银行卡号" maxlength="28" onkeyup="this.value=this.value.replace(/\D/g,'')" onafterpaste="this.value=this.value.replace(/\D/g,'')"/>
									</td>   
								</tr>
								<tr class="{{ $list_item_css }}" style="padding: 5px 0">
									<td style="text-align:center">开户银行</td>  
									<td>
										<input type="text" name="bank" class="inputElem f-ipt" autocomplete = "off" placeholder="请输入开户行" value="{{ $data['bank']}}" />
									</td>   
								</tr> 
								<tr class="{{ $list_item_css }}" style="padding: 5px 0">
									<td style="text-align:center">绑定手机</td>  
									<td>{{ $seller['mobile']}}（为帐号绑定手机，无需输入）</td>   
								</tr>
								<tr class="{{ $list_item_css }}" style="padding: 5px 0">
									<td style="text-align:center">验证码</td>
									<td class="tdtr"> 
										<input type="hidden" name="mobile" class="f-ipt fl" value="{{ $seller['mobile'] }}">
										<input type="text" style="width:100px;" name="verify" class="f-ipt fl" placeholder="请输入短信验证码">
										<button href="javascript:;" class="btn f-btn fl ml10 verify" style="width: 125px;margin-top:5px;line-height:28px;">获取验证码</button>
										<b class="system_msg"></b> 
									</td>
								</tr>
							</row> 
						</table>
						</yz:list>   
						@yizan_end    
						<p class="tc mt20 mb20">
							<a href="javascript:;" class="btn f-170btn f-170btnok">{{ $data['bankNo'] ? '更改绑定': '确定添加' }}</a>
						</p>
						<div class="f-bhtt f14 clearfix">
							<p class="ml10">提示:</p>
							<p class="pl20 pb10" style="line-height:18px;">
								1. 只能使用本人为户主的卡。<br>
								 2. 绑定手机为注册是绑定的手机号。
							</p>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
@stop

@section('js')
<script type="text/javascript" src="{{ asset('js/jsform.js') }}"></script>
<script type="text/javascript">
	$(function(){
		new TextMagnifier({
			inputElem: '.inputElem',
			align: 'top'
		});
		
	});

	var obj = new Object(); 
	obj.mobile  	=  "";
	obj.sellerId	=  "{{ $sellerId }}"; 
	obj.bankNo    =  "";
	obj.verifyCode  = "";
	obj.bank  = "";
	var verifynum = 0; 
	var bntclick = 0; 
	$(".verify").click(function(){ 
		verifynum ++;
		if(obj.sellerId !=　""){
			obj.mobile = $("input[name=mobile]").val();
			if(obj.mobile != ""){
				var reg = /^1[\d+]{10}$/;
				if(!reg.test(obj.mobile)){
					alert('请输入正确的手机号码'); 
					return false;
				}
			}else{
				alert("未获取到手机号码请刷新");
				return false;
			}  
			time();
			$.post("{{ u('Funds/bankverify') }}",{mobile:obj.mobile},function(result){ 
				$(".system_msg").text(result.msg);
			},'json');		
		}else{
			alert('服务人员ID不能为空'); 
			return false;
		} 
	});

	var wait = 60; 

	function time() {    
	    if (wait == 0) { 
			$(".verify").removeAttr("disabled") ;
	        $(".verify").text("免费获取验证码"); 
			$(".system_msg").text("");
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

	var partten = /^\d+$/;  
	$('input[name="bankNo"]').keyup(function(){
		 if(!partten.test($(this).val())){
		    $(this).val(''); 
		  }
	}); 

	$('input[name="verify"]').keyup(function(){
		 if(!partten.test($(this).val())){
		    $(this).val(''); 
		  }
	}); 

	$(".f-170btnok").click(function(){  
		obj.bankNo =  $("input[name=bankNo").val();
		obj.verifyCode =  $("input[name=verify").val();
		obj.bank =  $("input[name=bank").val();
	   	if(obj.bankNo == "")  
	   	{
	       alert("银行卡号不能为空");  
	       return  false;  
	   	}else if(obj.bankNo.length < 16 || obj.bankNo.length > 19 ){
	   		alert("银行卡号不合法");  
	       return  false;  
	   	}else if(obj.bank == ""){
	   		alert("开户行不能为空");  
	       return  false;  
	   	}else if(verifynum == 0){
			alert("你还没有获取验证码");
			return false;
		}else if(obj.verifyCode == ""){
	   		alert("验证码不能为空");  
	       return  false;  
	   	}else if(obj.sellerId == ""){
	   		alert("服务人员不能为空");  
	       return  false;  
	   	}else{
	   		bntclick += 1;
			$.post("{{ u('Funds/updabankcard') }}",obj,function(result){ 
				if(result.code == 0){
					alert("更换成功",1);
					window.location="{{ u('Funds/index') }}";
				}
				else{
					if(result.msg != ""){
						alert(result.msg);
					}else{
						alert("更换失败");
					}
					$(".verify").removeAttr("disabled") ;
			        $(".verify").text("免费获取验证码"); 
			        wait = 60; 
				}
			},'json');	   		
	   	}
	}); 
</script>
@include('seller._layouts.alert')
@stop