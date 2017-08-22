@extends('admin._layouts.base')
@section('css')
<style type="text/css">
	.m-taborder{width:100%;}
	.btn-y-n{padding: 5px 0px;text-align: center;}
	.btn-y-n a{margin: 10px 10px;}
	.synchronization{vertical-align: middle;}
	.ts{margin-left: 5px;color: #ccc;}
	table tr td p{word-break:break-all;}
</style>
@stop
@section('return_link')
<a href="{{ u('User/index') }}" class="btn mb10 fr"><i class="fa fa-reply mr10"></i>返回会员列表</a>
@stop
@section('right_content')
	@yizan_begin
		<div class="m-ordergk">
			<div class="clearfix">
				<div class="fl m-taborder">
					<table>
						<tr>
							<td width="15%">
								<p class="tc f14">
									服务名称
								</p>
							</td>
							<td width="*">
								<p class="pl20">
									【{{ $data['id'] }}】 {{ $data['name'] }}
								</p>
							</td>
						</tr>
						<tr>
							<td width="15%">
								<p class="tc f14">
									服务分类
								</p>
							</td>
							<td width="*">
								<p class="pl20">
									{{ $cate[$data['cate']['id']]['levelrel'] }}
								</p>
							</td>
						</tr>
						<tr>
							<td>
								<p class="tc f14">
									服务时长
								</p>
							</td>
							<td>
								<p class="pl20">
									{{ sprintf("%.2f", $data['duration'] / 3600 ) }} 小时
								</p>
							</td>
						</tr>
						<tr>
							<td>
								<p class="tc f14">
									服务价格
								</p>
							</td>
							<td>
								<p class="pl20">
									￥{{ $data['price'] or 0 }}
								</p>
							</td>
						</tr>
						<tr>
							<td>
								<p class="tc f14">
									市场价格
								</p>
							</td>
							<td>
								<p class="pl20 clearfix">
									￥{{ $data['marketPrice'] or 0 }}
								</p>
							</td>
						</tr>
						<tr>
							<td>
								<p class="tc f14">
									服务图片
								</p>
							</td>
							<td>
								<p class="pl20 clearfix pt20">
									@foreach( $data['images'] as $key => $value )
										<a href="{{ $value }}" target="_blank"><img src="{{ formatImage($value,0,100,0) }}" alt="" height="100"></a>
									@endforeach
								</p>
							</td>
						</tr>
						<tr>
							<td>
								<p class="tc f14">
									服务描述
								</p>
							</td>
							<td>
								<p class="pl20 clearfix">
									{{ $data['brief'] or 0 }}
								</p>
							</td>
						</tr>
						<tr>
							<td>
								<p class="tc f14">
									服务详细
								</p>
							</td>
							<td>
								<p class="pl20 clearfix">
									{!! $data['detail'] !!}
								</p>
							</td>
						</tr>
						<tr>
							<td>
								<p class="tc f14">
									服务人员信息
								</p>
							</td>
							<td>
								<div class="clearfix">
									<p class="pl20 pt20 fl">
										<a href="{{ $data['seller']['logo'] }}" target="_blank"><img src="{{ formatImage($data['seller']['logo'],0,100,0) }}" alt="" height="100"></a>
									</p>
									<div class="pl20 fl ml5 pt10">
										<p>名称：{{ $data['seller']['name'] or '-' }}</p>
										<p>电话：{{ $data['seller']['mobile'] or '-' }}</p>
										<p>地址：{{ $data['seller']['address'] or '-' }}</p>
									</div>
								</div>
							</td>
						</tr>
						<tr>
							<td>
								<p class="tc f14">
									服务人员简介
								</p>
							</td>
							<td>
								<p class="pl20 clearfix">
									{{ $data['seller']['brief'] }}
								</p>
							</td>
						</tr>
						<tr>
							<td>
								<p class="tc f14">
									同步到通用服务库
								</p>
							</td>
							<td  id="is_tb_common">
								<p class="pl20 clearfix">
									<yz:radio name="is_common" options="1,0" texts="是,否" checked="0"></yz:radio>
								</p>
								<yz:form id="cityPriceForm" nobtn="1" titlewidth="auto" action="dispose" method="post">
									<!-- <div id="setcity_price" class="clearfix">
										@foreach( $city as $key => $value)
										<div style="float:left; border:solid 1px #ccc; padding:5px; margin:0 10px 10px 0;" class="city_price_box">
											{{$value['name']}} 
											<input type="text" name="cityPrices[{!! $value['id'] !!}][price]" value="{{ $data['price'] or 0 }}" class="u-ipttext price" placeholder='服务价格' style="width:60px;">
											<span class="ts">元</span>
											<input type="text" name="cityPrices[{!! $value['id'] !!}][marketPrice]" value="{{ $data['marketPrice'] or 0 }}" class="u-ipttext marketPrice" placeholder='门店价格' style="width:60px;">
											<span class="ts">元</span>
										</div>
										@endforeach
									</div> -->
									<input type="hidden" name="id" value="{{ $data['id'] }}">
									<input type="hidden" name="type" value="Y">
									<input type="hidden" name="isSystem" value="0">
								</yz:form>
							</td>
						</tr>
					</table>
				</div>
			</div>
		</div>
		<div class="btn-y-n">
			<a href="javascript:;" class="btn btn-green" onclick="Audit_Y({{ $data['id'] }})"><i class="fa fa-check mr10"></i>通过</a>
			<a href="javascript:;" class="btn" onclick="Audit_N({{ $data['id'] }})"><i class="fa fa-times mr10"></i>拒绝</a>
		</div>
	@yizan_end
@stop

@section('js')
<script type="text/tpl" id="WithdrawMoney"> 
	<div style="width:500px;text-align:center;padding:10px;">
		<textarea name='disposeRemark' id='disposeRemark' placeholder='拒绝原因' style="width:480px;height:100px;border:1px solid #EEE"></textarea> 
	</div>
</script> 
<script type="text/javascript">
	$(function(){
		$("#is_tb_common input[name='is_common']").change(function(){
			//同步
			if( $(this).val() == 1 ){
				$("#set_city_price").show();
			}
			//取消
			else{
				$("#set_city_price").hide();
			}
		});
	});

	function Audit_Y (id) {
		$.ShowConfirm("确定该服务通过审核？",function(){
	        var isSystem_val = $("#is_tb_common span.checked").find("input[name='is_common']").val();
	        $("#cityPriceForm").find("input[name='isSystem']").val( isSystem_val );
	        $("#cityPriceForm").submit();
	        // var query = new Object();
	        // query.type = "Y";
	        // query.id = id;
			// $.post("{{ u('GoodsAudit/dispose') }}",query,function(result){ 
			// 	$.ShowAlert(result.msg);
			// 	if(result.status == true){
			// 		window.location.href="{{ u('GoodsAudit/index') }}";
			// 	}
	  	//       },'json');
		},function(){},"操作提示");
	}

	function Audit_N (id) {
		var dialog = $.zydialogs.open($("#WithdrawMoney").html(), {
	        boxid:'SET_GROUP_WEEBOX',
	        width:300,
	        title:'拒绝原因备注',
	        showClose:true,
	        showButton:true,
	        showOk:true,
	        showCancel:true,
	        okBtnName: '拒绝',
			cancelBtnName: '取消',
	        contentType:'content',
	        onOk: function(){
	            var query = new Object();
	            query.type = "N";
	            query.id = id;
	            query.disposeResult = $("#disposeRemark").val();
	            if(query.disposeResult == ""){
	            	$.ShowAlert("拒绝原因不能为空");
	            }else{
		            dialog.setLoading();
		            	$.post("{{ u('GoodsAudit/dispose') }}",query,function(result){ 
		                	dialog.setLoading(false);  
		                	$.ShowAlert(result.msg);
		                	if(result.status == true){
		                   	 	window.location.href="{{ u('GoodsAudit/index') }}";
		                	}else{
		                    	$.zydialogs.close("SET_GROUP_WEEBOX");
			                }
			            },'json');
	            }
	        },
	        onCancel:function(){ 
	            $.zydialogs.close("SET_GROUP_WEEBOX");
	        }
    	});
	}

</script>
@stop
