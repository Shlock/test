@extends('seller._layouts.base')
@section('css')
<style>
	p{word-wrap:break-word; word-break:normal;}
	.tds tr{background-color: #fff;}
</style>
@stop
@section('content')
	<div>
		<div class="m-zjgltbg">
			<div class="p10">						
				<p class="f-bhtt f14 clearfix">
					<span class="ml15 fl">服务类订单详情</span>
                    @if($data['orderType'] == 1)
                        <a href="{{ u('Order/index') }}" class="fr mr15 btn f-bluebtn" style="margin-top:8px;">返回</a>
                    @else
                        <a href="{{ u('ServiceOrder/index') }}" class="fr mr15 btn f-bluebtn" style="margin-top:8px;">返回</a>
                    @endif
				</p>
				<p class="f-bhtt mt20 f14" >
					<span class="ml15">{{ yzTime($data['createTime']) }}</span>
					<span class="ml15">订单编号：{{ $data['sn'] }}</span>					
				</p>
				<!-- 类型1 -->
				<?php $width=(100/count($data['statusNameDate'])).'%'; $_width = ((100/count($data['statusNameDate']))-1).'%';?> 
				@if($data['statusFlowImage'])
					<div class="m-porbar clearfix">
						<img src="{{ asset('images/'.$data['statusFlowImage'].'.png') }}" alt=" " class="mt20 pt10 clearfix" width="750">
						<ul class="m-barlst clearfix tc mt20 pt10" style="width:770px;">
						@foreach($data['statusNameDate'] as $key => $value)
							@if($data['statusFlowImage'] == 'statusflow_2' && $key == 0)
								<?php $color = '#efbe3b'; ?>
							@elseif($data['statusFlowImage'] == 'statusflow_2_1' && $key == 2)
								<?php $color = 'red'; ?>
							@elseif($data['statusFlowImage'] == 'statusflow_0' && $key == 1)
								<?php $color = 'red'; ?>
							@else
								@if($value['date']==0)
									<?php $color = '#ccc'; ?>
								@else
									<?php $color = '#7abd54'; ?>
								@endif
							@endif
							<li style="width:{{$width}};*width:{{$_width}};color:{{$color}}">
								<p class="tc">{{$value['name']}}</p>
								<p class="tc">{{ $value['date'] > 0 ? yztime($value['date']) : '' }}</p>
							</li>
						@endforeach
						</ul>
					</div>
				@endif
                @if($data['status'] == ORDER_STATUS_REFUND_AUDITING)
					<a href="javascript:;" class="fr btn mb10 hsbtn-78 mt10 ml20" onclick="$.orderVerify(2)">取消退款</a>
					<a href="javascript:;" class="fr btn mb10 hsbtn-78 mt10 ml20" onclick="$.orderVerify(3)">确认退款</a>
				@endif
                @if($data['isCanCancel'])
					<a href="javascript:;" class="fr btn mb10 hsbtn-78 mt10 ml20" onclick="$.orderVerify(0)">取消订单</a>
				@endif
				@if($data['isCanAccept'])
					<a href="javascript:;" class="fr btn mb10 hsbtn-78 mt10 ml20" onclick="$.orderVerify(1)">接单</a>
				@endif
		        @if($data['isCanStartService'])
		            <a href="javascript:;" class="fr btn mb10 hsbtn-78 mt10 ml20" onclick="$.orderStart(1)">开始服务</a>
		        @endif
		        @if($data['isCanFinish'])
		            <a href="javascript:;" class="fr btn mb10 hsbtn-78 mt10 ml20" onclick="$.orderFinish(1)">完成订单</a>
		        @endif
				<!-- 订单详情 -->
        		<div class="m-ordergk">
        			<div class="u-tt clearfix">
        				<span class="fl f14">订单概况</span>
        			</div>
        			<div class="clearfix">
        				<div class="fl m-taborder" style="width:100%;background: #FFF;">
        					<table>
        						<tr>
        						    <td style="padding-left:10px">
	        						    <p class=" f14">会 员 名：{{ $data['user']['name'] }}</p>
										<p class=" f14">服务地址：{{ $data['name'] }}，{{ $data['mobile'] }}，{{$data['province']}}{{$data['city']}}{{$data['area']}}{{ $data['address'] }}</p>
										<p class=" f14">服务时间：{{ $data['appTime']}}</p>
										@if($data['payType'])
											<p class=" f14">支付方式：{{ $data['payType'] }}</p>
										@endif


									</td>
        						</tr>
                                <tr>
                                    <td style="padding-left:10px">
                                        <p class=" f14">订单备注：{{ $data['buyRemark']}}</p>
                                        @if($data['invoiceRemark'])
                                            <p class=" f14">发票抬头：{{ $data['invoiceRemark'] }}</p>
                                        @endif
                                        @if($data['giftRemark'])
                                            <p class=" f14">贺卡内容：{{ $data['giftRemark'] }}</p>
                                        @endif
                                        @if($data['cancelRemark'])
                                            <p class=" f14">取消原因：{{ $data['cancelRemark'] }}</p>
                                        @endif
                                    </td>
                                </tr>




        						<tr>
        						    <td style="padding-left:10px">
	        						    <p class=" f14">服务人员：{{ $data['staff']['name']}}    {{ $data['staff']['mobile']}} 
	        						    @if( $data['status'] < ORDER_STATUS_FINISH_STAFF)
	        						    	<a href="javascript:;" class="fr mr15 btn f-bluebtn" id="isReceivabilitySeller" style="margin-top:8px;">点击重新指派</a></p>
	        						    @endif
									</td>
        						</tr>
        					</table>
        				</div>        				
        			</div>
        			@include('seller.serviceorder.snack')
				</div>
			</div>
		</div>
@stop

@section('js')
<script type="text/tpl" id="WithdrawMoney"> 
	<div style="width:500px;text-align:center;padding:10px;">
		<textarea name='disposeRemark' id='content' placeholder='请务必填写退款备注。' style="width:480px;height:100px;border:1px solid #EEE"></textarea> 
	</div>
</script>
<script type="text/tpl" id="serviceContent"> 
	<div style="width:500px;text-align:center;padding:10px;">
		<textarea name='disposeRemark' id='content' placeholder='请务必填写取消订单的理由备注。' style="width:480px;height:100px;border:1px solid #EEE"></textarea> 
	</div>
</script>
<script type="text/tpl" id="pais">
<div style="width:100%;text-align:center;padding:10px;">
	<ul class="x-rylst">
        @foreach ($staff as $key=>$val)
		  <li data-id="{{ $val['id'] }}" @if($val['id'] == $data['sellerStaffId']) class="on" @endif>{{ $val['name'] }}<i></i></li>
        @endforeach
		<!-- <li class="btn f-bluebtn suiji">随机指派</li> -->
        <div class="clearfix"></div>
	</ul>
    <p style="margin-bottom: 40px;"></p>
</div>
<script type="text/javascript">
var staffId = "";
  $(".x-rylst li").click(function(){
    	if($(this).hasClass("on")){
    		$(this).removeClass("on");
            staffId = "";
    	}else{
            $(".x-rylst li").each(function(){
                $(this).removeClass("on");
            });
    		$(this).addClass("on");
            staffId = $(this).data("id");
    	}
    });   
    </script>
</script>
<script type="text/javascript"> 
var id = {{$data['id']}};

	$(function(){			
			$("#isReceivabilitySeller").click(function(){			
			var dialog = $.zydialogs.open($("#pais").html(), {
		        boxid:'SET_GROUP_WEEBOX',
		        width:300,
		        title:'指派人员',
		        showClose:true,
		        showButton:true,
		        showOk:true,
		        showCancel:true,
		        okBtnName: '确认指派',
				cancelBtnName: '取消返回',
		        contentType:'content',
		        onOk: function(){
			        if(staffId == ""){
			        	$.ShowAlert("没有选择指定的人员");
			        	return false;
				    }
		        	$.post("{{ u('Order/designate') }}",{'staffId':staffId,'orderId':id},function(res){
		        		$.ShowAlert(res.msg);
	    				if(res.code==80000) {
	    					window.location.reload();		    				
		    			}
	    			},'json');
		        },
		        onCancel:function(){ 
		            $.zydialogs.close("SET_GROUP_WEEBOX");
		        }		       
	    	});
			/* $(".suiji").click(function(){
				 var serviceContent = $(".textarea").val();
		         var money = $("#money").val();		
				 $.post("{{ u('Order/reassign') }}",{'orderId':id},function(res){
	        		$.ShowAlert(res.msg);
    				if(res.code == 80000) {
    					window.location.reload();		    				
	    			}
    			},'json');
				// $.ShowAlert("开发中，请使用指定派发");
	        });*/
		});
		//取消订单
		$.orderCancel = function() {
			var msg = "确认取消该订单吗？";
			$.ShowConfirm(msg, $.orderCancelOk);
		}
		$.orderCancelOk = function() {
			// alert('取消订单')
			$.refundRemark({{ORDER_STATUS_USER_CANCEL}});
			
		}
		//接单 拒绝接单
		$.orderVerify = function(status) {
			var msg = "拒绝接单吗？";
			var okfun = $.orderVerifyFalse;
			if(status==1){
				msg = "确认接单？";
				okfun = $.orderVerifyTrue;
			}else if(status==2){
				msg = "确认取消退款？";
				okfun = $.orderVerify2;
			}else if(status==3){
				msg = "确认退款？";
				okfun = $.orderVerify3;
			}
			$.ShowConfirm(msg, okfun);
		}
		$.orderVerifyTrue = function (){
			// alert('确认接单');
			$.refundRemark({{ORDER_STATUS_AFFIRM_SELLER}},1);
		}
		$.orderVerifyFalse = function() {
			// alert('拒绝接单');
			$.refundRemark({{ORDER_STATUS_CANCEL_SELLER}},0);
		}
		$.orderVerify2 = function (){
			// alert('取消退款');
			$.refundRemark({{ORDER_REFUND_SELLER_REFUSE}},2);
		}
		$.orderVerify3 = function() {
			// alert('确认退款');
			$.refundRemark({{ORDER_REFUND_SELLER_AGREE}},3);
		}

        //开始订单
        $.orderStart = function() {
            var msg = "确认开始服务吗？";
            $.ShowConfirm(msg, $.orderStartOk);
        } 

        $.orderStartOk = function() { 
            $.refundRemark({{ORDER_STATUS_START_SERVICE}}, 1); 
        }
        //完成订单
        $.orderFinish = function() {
            var msg = "确认完成订单吗？";
            $.ShowConfirm(msg, $.orderFinishOk);
        } 

        $.orderFinishOk = function() { 
            $.refundRemark({{ORDER_STATUS_FINISH_STAFF}}, 1); 
        }
		$.refundRemark = function(status,type){
			if(type != 1 && type != 3){
				if(type == 2){
					var open =  "#WithdrawMoney";
				}else{
					var open = "#serviceContent";
				}
					var dialog = $.zydialogs.open($(open).html(), {
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
		    	    			dialog.setLoading();
		    	    			$.post("{{ u('Order/refundRemark') }}",{'id':id,'status':status,'refuseContent':refuseContent},function(res){
		    	    				$.ShowAlert(res.msg);
		    	    				dialog.setLoading(false); 
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
			}else{
				if(!status) {
	    			$.ShowAlert("参数错误");
	    		}else{
	    			$.post("{{ u('Order/refundRemark') }}",{'id':id,'status':status},function(res){
	    				$.ShowAlert(res.msg);
	    				if(res.status==true) {
	    					window.location.reload();
	    				}
	    			},'json');
	    		}
			}
		}

	})
	
</script>
@stop