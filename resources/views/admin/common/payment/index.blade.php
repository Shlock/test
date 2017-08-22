@extends('admin._layouts.base')
@section('css')
<style type="text/css">
	.status{border-top: 1px solid #eee;text-align: center;}
	.config{width: 400px;padding: 10px 0px  5px  20px}
	.config input{padding: 5px;border: 1px solid #eee;width: 90%}
</style>
@stop
@section('right_content')
@yizan_begin 
<yz:list>
	<table pager="no">
		<columns>				 
			<column code="code" label="支付代码" width="280" ></column> 
			<column code="name" label="支付名称"  width="200">
				{{ $list_item['name'] }}
				<textarea style="display:none">{{ json_encode($list_item['config']) }}</textarea>
			</column>
			<column code="status" label="状态"  width="200" type="status"></column>   
			<actions>
				@if( $list_item['code'] != 'cashOnDelivery')
					<action label="更新" css="payments blu" click="$.UpdatePayment(this, '{{ $list_item['code'] }}')"></action>
				@endif
			</actions>
		</columns>  
	</table>
</yz:list>  
<script type="text/tpl" id="alipayTpl"> 
<yz:form id="alipay_form" noajax="1" nobtn="1" action="update">
	<yz:fitem name="sellerId" label="卖家支付宝账号" val="@{{=it.sellerId}}"></yz:fitem>
	<yz:fitem name="partnerId" label="合作者身份ID" val="@{{=it.partnerId}}"></yz:fitem>
	<yz:fitem name="partnerKey" label="安全校验码(Key)" val="@{{=it.partnerKey}}"></yz:fitem>
	<yz:fitem name="partnerPubKey" type="textarea" label="RSA加密公钥" val="@{{=it.partnerPubKey}}"></yz:fitem>
	<yz:fitem name="partnerPrivKey" type="textarea" label="RSA加密私钥" val="@{{=it.partnerPrivKey}}"></yz:fitem>
</yz:form>
</script>
<script type="text/tpl" id="weixinTpl"> 
<yz:form id="weixin_form" noajax="1" nobtn="1" action="update" titlewidth="68px">
	<yz:fitem name="partnerId" label="商户号" val="@{{=it.partnerId}}"></yz:fitem>
	<yz:fitem name="appId" label="应用ID" val="@{{=it.appId}}"></yz:fitem>
	<yz:fitem name="appSecret" label="应用密钥" val="@{{=it.appSecret}}"></yz:fitem>
	<yz:fitem name="partnerKey" label="API密钥" val="@{{=it.partnerKey}}"></yz:fitem>
</yz:form>
</script>
@yizan_end
@stop

@section('js')
<script type="text/javascript"> 
$.UpdatePayment = function(obj, code) {
	var config = JSON.parse($(obj).parents('tr').find('textarea').val());
	var width = 350;
	var html = '';
	var form = '';
	if (code == 'alipay' || code == 'alipayWap') {
		form = "alipay_form";
		html = $("#alipayTpl").html();
		width = 580;
	} else if (code == 'weixin' || code == 'weixinJs') {
		form = "weixin_form";
		html = $("#weixinTpl").html();
	}
	$("#" + form).remove();

	var dialog = $.zydialogs.open($.Template(html, config), {
        boxid:'PAYMENT_WEEBOX',
        width:width,
        title:'相关参数配置',
        showClose:true,
        showButton:true,
        showOk:true,
        showCancel:true,
        okBtnName: '更新',
		cancelBtnName: '取消',
        contentType:'content',
        onOk: function(){
        	var payment = new Object();
	        payment.code = code;
	        payment.config = new Object();
	        $.each($("#" + form).serializeArray(), function(i, item){
				payment.config[item.name] = item.value;
			});
	        
       		dialog.setLoading();
        	$.post("{{ u('Payment/update') }}", payment,function(result){ 
            	dialog.setLoading(false);  
            	if(result.status == true){
                	$.ShowAlert(result.msg);
               	 	window.location.reload();
            	}else{
                	$.ShowAlert(result.msg);
                	$.zydialogs.close("PAYMENT_WEEBOX");
            	}
            },'json'); 
        },
        onCancel:function(){
            $.zydialogs.close("PAYMENT_WEEBOX");
        }
	});
}
/*function payments(code) {   
		var config = new Object();
		var html = "";
		var k = "";
		var statu = "";
		$('td[code="code"]').each(function(i,e){   
	  		if($(this).text() == code){  
	  			config = $(this).parents('tr').find('td[code="config"]').find('p');
	  			config.each(function(k,v){ 
	  				html+= '<div class="u-fitem clearfix realName-form-item">';
			        html+= '<span class="f-tt">';
			        html+= "参数名称:"+$(this).find('span:eq(0)').text();
			        html+= '</span>';
			        html+= '<div class="f-boxr">';
			        html+= '<input class="config'+k+'" type="text" name="'+$(this).find('span:eq(0)').text()+'" id="realName" class="u-ipttext" value="'+$(this).find('span:eq(1)').text()+'">';
			        html+= '</div>';
			        html+= '</div>'; 
	  			});
	  			statu = $(this).parents('tr').find('td[code="status"] i').attr("status"); 
	  		}		 
        });
		var dialog = $.zydialogs.open($("#payment").html(), {
	        boxid:'SET_GROUP_WEEBOX',
	        width:300,
	        title:'相关参数配置',
	        showClose:true,
	        showButton:true,
	        showOk:true,
	        showCancel:true,
	        okBtnName: '更新',
			cancelBtnName: '不更新',
	        contentType:'content',
	        onOk: function(){
	        	var configs = new Object();
	            var query =  {};   
	          	$(".config input").each(function() { 
	          		query[$(this).attr("name")] = $(this).val(); 
			    });  
		        configs.code = code;
		        configs.config = query;
		        // configs.status = "";
		        // $('input[name="statuss"]').each(function(){
		          	// configs.status = $("input[name='statuss']:checked").val(); 
		        // });   
           		dialog.setLoading();
            	$.post("{{ u('Payment/update') }}",configs,function(result){ 
                	dialog.setLoading(false);  
                	if(result.status == true){
                    	$.ShowAlert(result.msg);
                   	 	window.location.reload();
                	}else{
                    	$.ShowAlert(result.msg);
                    $.zydialogs.close("SET_GROUP_WEEBOX");
                }
	            },'json'); 
	        },
	        onCancel:function(){
	            $.zydialogs.close("SET_GROUP_WEEBOX");
	        }
    	});
		statu -= 1;
		$('input[name="statuss"]').each(function(){
           	if($(this).val() == statu ){
           		$(this).attr("checked",true);  
           	}
        });
	    $(".config").append(html);
	}  */
</script>
@stop 

