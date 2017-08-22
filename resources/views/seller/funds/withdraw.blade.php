@extends('seller._layouts.base')
@section('css')
<style type="text/css">
.parentCls {margin:-40px 110px 0;}
.js-max-input {border: solid 1px #ffd2b2;background: #fffae5;padding: 0 10px 0 10px;font-size:20px;font-weight: bold;color: #ff4400;}
.f-ipt{width: 330px;}
.m-tab table tbody td{padding: 10px 5px;font-size: 12px;text-align: left;  }
.msg{color:#ccc}
</style>
@stop
@section('content')
	<div>
		<div class="m-zjgltbg">
			<p class="f-bhtt f14 clearfix">
				<span class="ml15 fl">提现申请</span>
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
									<td width="100px;" style="text-align:center">选择银行</td> 
									<td class="tdtr">
										<input type="text" style="width:100px;" name="id" class="f-ipt fl mr15 id" readonly placeholder="编号">
										<button href="javascript:;" class="btn f-btn banks fl ml10y" style="width: 125px;margin-top:5px;line-height:28px;">选择银行</button> 
										<input type="hidden" style="width:100px;" name="ids" class="f-ipt fl mr15 ids">
									</td>								
								</tr>
							    <tr class="{{ $list_item_css }}" style="padding: 5px 0">
									<td width="100px;" style="text-align:center">户主</td>
									<td><p class="msg"></p><span class="name"></span></td>
								</tr>
								<tr class="{{ $list_item_css }}" style="padding: 5px 0">
									<td style="text-align:center">开户银行</td>  
									<td><p class="msg"></p><span class="bank"></span></td> 
								</tr>
								<tr class="{{ $list_item_css }}" style="padding: 5px 0">
									<td style="text-align:center">提现卡号</td>
									<td><p class="msg"></p><span class="bankNo"></span></td>  
								</tr> 
								<tr class="{{ $list_item_css }}" style="padding: 5px 0">
									<td style="text-align:center">绑定手机</td>
									<td><p class="msg"></p><span class="mobile"></span></td>  
								</tr>
								<tr class="{{ $list_item_css }}" style="padding: 5px 0">
									<td style="text-align:center">验证码</td>
									<td class="tdtr"> 
										<input type="hidden" name="mobile" class="f-ipt fl"> 
										<input type="text" style="width:100px;" onkeyup="this.value=this.value.replace(/\D/g,'')" onafterpaste="this.value=this.value.replace(/\D/g,'')"  name="verify" class="f-ipt fl" placeholder="请输入短信验证码">
										<button href="javascript:;" class="btn f-btn fl ml10 verify" style="width: 125px;margin-top:5px;line-height:28px;">获取验证码</button> 
										<b class="system_msg"></b>
									</td>
								</tr>								
								<tr class="{{ $list_item_css }}" style="padding: 5px 0">
									<td style="text-align:center">提现金额</td>  
									<td>
										<input type="text" style="width:100px;" onkeyup="this.value=this.value.replace(/\D/g,'')" onafterpaste="this.value=this.value.replace(/\D/g,'')"  name="money" class="f-ipt fl" placeholder="请输入提现金额">
										<p class="ml20 mt10" style="color: rgb(130, 130, 130);">（提现金额必须是整数 参考可提现金额 ：<b>{{(int)$money}} </b>;当前余额 ：<b>{{$money}}</b> ） </p>
									</td>   
								</tr>
							</row> 
						</table>
						</yz:list>   
						@yizan_end    
						<p class="tc mt20 mb20">
							<a href="javascript:;" class="btn f-170btn f-170btnok">申请提现</a>
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

<script type="text/tpl" id="pais">
	<div style="width:100%;text-align:center;padding:10px;">
		<ul class="x-rylst">
	        @foreach ($bank as $key=>$val)
			  <li data-id="{{ $val['id'] }}">
				<p class="key1">{{ $val['bank'] }}</p>
			  	<i></i>
			  </li>
	        @endforeach
	        <div class="clearfix"></div>
		</ul>
	</div>
	<script type="text/javascript">
    var id = 0;
	  $(".x-rylst li").click(function(){
	    	if($(this).hasClass("on")){
	    		$(this).removeClass("on");
	             id = 0;
	    	}else{
	            $(".x-rylst li").each(function(){
	                $(this).removeClass("on");
	            });
	    		$(this).addClass("on");
	            id = $(this).data("id");
	    	}
	    });   
    </script>
</script>
<script type="text/javascript">
	
	$(function(){
		$(".msg").html("请选择银行卡");
		$(".banks").click(function(){			
			var dialog = $.zydialogs.open($("#pais").html(), {
		        boxid:'SET_GROUP_WEEBOX',
		        width:300,
		        title:'选择银行卡',
		        showClose:true,
		        showButton:true,
		        showOk:true,
		        showCancel:true,
		        okBtnName: '确认',
				cancelBtnName: '取消',
		        contentType:'content',
		        onOk: function(){
     				dialog.setLoading();
		        	$.post("{{ u('Funds/get') }}",{'id':id},function(res){
		        		if(res.code != 0){
							$.ShowAlert("获取银行卡失败");	
              				dialog.setLoading(false);
			        	}else{
							if(res.data['id'] > 0 ){
								$(".msg").remove();
								$(".id").val("编号"+res.data['id']);
								$(".ids").val(res.data['id']);
								$(".name").text(res.data['name']);
								$(".bank").text(res.data['bank']);
								$(".bankNo").text(res.data['bankNo']);
								$(".mobile").text(res.data['mobile']);
								$("input[name=mobile]").val(res.data['mobile']);
		            			$.zydialogs.close("SET_GROUP_WEEBOX");
							}else{
								$.ShowAlert("获取银行卡失败");	
              					dialog.setLoading(false);
							}
					    }   				
	    			},'json');
		        },
		        onCancel:function(){ 
              		dialog.setLoading(false);
		            $.zydialogs.close("SET_GROUP_WEEBOX");
		        }		       
	    	});
		});

		//接单 拒绝接单
		$.orderVerify = function(status) {
			var msg = "拒绝接单吗？";
			var okfun = $.orderVerifyFalse;
			$.ShowConfirm(msg, okfun);
		} 
		$.orderVerifyFalse = function() {
			// $.ShowAlert(拒绝接单');
			$.refundRemark({{ ORDER_SELLER_REFUSE_SERVICE }});
		}

		$.refundRemark = function(status){
			var dialog = $.zydialogs.open($("#serviceContent").html(), {
		        boxid:'SET_GROUP_WEEBOX',
		        width:300,
		        title:'拒绝理由',
		        showClose:true,
		        showButton:true,
		        showOk:true,
		        showCancel:true,
		        okBtnName: '确认理由',
				cancelBtnName: '取消',
		        contentType:'content',
		        onOk: function(){
			        var  refuseContent = $("#content").val();
			        
			        if(refuseContent != ""){
	    	        	if(!status) {
	    	    			$.ShowAlert("参数错误");
	    	    		}else{
	    	    			$.post("{{ u('Order/refundRemark') }}",{'id':id,'status':status,'refuseContent':refuseContent},function(res){
	    	    				$.ShowAlert(res.msg);
	    	    				if(res.status==true) {
	    	    					window.location.reload();
	    	    				}
	    	    			},'json');
	    	    		}
		    		}else{
		    			$.ShowAlert("请输入理由");
			    	}
		        },
		        onCancel:function(){ 
		            $.zydialogs.close("SET_GROUP_WEEBOX");
		        }		       
	    	});
		}

		new TextMagnifier({
			inputElem: '.inputElem',
			align: 'top'
		});

		var obj = new Object(); 
		obj.mobile  	=  "";
		obj.sellerId	=  "{{$sellerId}}"; 
		obj.bankNo    =  "";
		obj.verifyCode  = "";
		obj.money  		= ""; 
		obj.bank  = "";
		var  verifynum =  0;
		$(".verify").click(function(){
			verifynum ++;
			if(obj.sellerId !=　""){
				obj.mobile = $("input[name=mobile]").val();
				if(obj.mobile != ""){
					var reg = /^1[\d+]{10}$/;
					if(!reg.test(obj.mobile)){
						$.ShowAlert('请输入正确的手机号码'); 
						return false;
					}
				}else{
					$.ShowAlert("未获取到手机号码请刷新");
					return false;
				}  
				time();
				$.post("{{ u('Funds/userverify') }}",{mobile:obj.mobile},function(result){ 
					$(".system_msg").text(result.msg);
				},'json');
			}else{
				$.ShowAlert('服务人员ID不能为空'); 
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
		$(".f-170btnok").click(function(){
			obj.bankNo =  $(".bankNo").text();
			obj.bank =  $(".bank").text();
			obj.id =  $("input[name=ids").val(); 
			obj.verifyCode =  $("input[name=verify").val(); 
			obj.money =  $("input[name=money").val(); 
		   	if(obj.bankNo == "")  
		   	{
		       $.ShowAlert("银行卡号不能为空");  
		       return  false;  
		   	}else if(obj.bankNo.length < 16 || obj.bankNo.length > 19 ){
		   		$.ShowAlert("银行卡号不合法");  
		       return  false;  
		   	}else if(verifynum == 0){
				$.ShowAlert("你还没有获取验证码");
				return false;
			}else if(obj.verifyCode == ""){
		   		$.ShowAlert("验证码不能为空");  
		       return  false;  
		   	}else if(obj.sellerId == ""){
		   		$.ShowAlert("服务人员不能为空");  
		       return  false;  
		   	}else if(obj.money == ""){
		   		$.ShowAlert("请输入提现金额");  
		       return  false;  
		   	}else{
				$.post("{{ u('Funds/ajaxwithdraw') }}",obj,function(result){  
					if(result.code == 0){
						$.ShowAlert("提现申请成功,请等待审核结果");
						function g()
						{
						    window.location="{{ u('Funds/index') }}";
						}
						setInterval(g,2000);
					}
					else{
						if(result.msg != ""){
							$.ShowAlert(result.msg);
						}else{
							$.ShowAlert("提现失败");
						}
						$(".verify").removeAttr("disabled") ;
				        $(".verify").text("免费获取验证码"); 
				        wait = 60; 
					}
				},'json');	   		
		   	}
		}); 

	})
</script> 
@include('seller._layouts.alert')
@stop

