@extends('admin._layouts.base_nomenu')
@section('css')
<style type="text/css">
	#refund_reason{color: #DA0809}
	.m-porbar .m-barlst li.on .f-lsbar{top: 9px;}
	.ts{text-align: center;color: #999}
	._gray{color: #ccc;}
</style>
@stop
@section('return_link')
<a href="{{ url('Order/index') }}" class="btn mb10 lsbtn-120"><i class="fa fa-reply mr10"></i>返回订单列表</a>
@stop
<?php 
//可申请退款 [0,3,4,5]
$refund = [
	ORDER_STATUS_PAY_SUCCESS,
	ORDER_STATUS_SELLER_ACCEPT,
	ORDER_STATUS_STAFF_ACCEPT,
	ORDER_STATUS_STAFF_SETOUT,
	ORDER_STATUS_START_SERVICE,
	ORDER_STATUS_FINISH_SERVICE
];
//退款中、结果 [8,9,10]
$refunding = [
	ORDER_STATUS_REFUND_AUDITING,
	ORDER_STATUS_REFUND_REFUSE,
	ORDER_STATUS_REFUND_HANDLE,
	ORDER_STATUS_REFUND_FAIL,
	ORDER_STATUS_REFUND_SUCCESS
];
dd($data);
 ?>
@section('right_content')
	<!-- @if($data) -->
	<div class="m-ddbgct">
		<!-- 进度条 -->
		<div class="m-ddh">
			<p class="f-tt">
				订单号：{{$data['sn']}}
				<span class="ml20">
					<!-- @if($data['isToStore']==0) -->
					服务类型：上门
					<!-- @elseif($data['isToStore']==1) -->
					服务类型：到店({{$data['reservationCode']}})
					<!-- @else -->
					服务类型：未知
					<!-- @endif -->
				</span>
				<span class="ml20" >支付状态：
					@if($data['payStatus']==1) 
					已支付 
					@else 
					<span class='_gray'>等待支付</span>
					@endif
				</span>
				<span class="ml20" >订单状态：{{$data['orderStatusStr']}}</span>
			</p>
			<?php $width=(100/count($data['statusNameDate'])).'%'; $_width = ((100/count($data['statusNameDate']))-1).'%';?> 
			@if($data['statusFlowImage'])
				<div class="m-porbar clearfix">
					<img src="{{ asset('images/'.$data['statusFlowImage'].'.png') }}" alt="" class="mt20 pt10 clearfix">
					<ul class="m-barlst clearfix tc mt20 pt10" style="width:900px;">
					@foreach($data['statusNameDate'] as $key => $value)
						@if($data['statusFlowImage'] == 'statusflow_2' && $key == 3)
							<?php $color = '#efbe3b'; ?>
						@elseif($data['statusFlowImage'] == 'statusflow_7' && $key == 7)
							<?php $color = '#eb6868'; ?>
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
		</div>
		<!-- @if( $data['payStatus'] == ORDER_PAY_STATUS_YES && in_array($data['status'],$refund) ) -->
			<!-- 可申请退款申请 -->
				<div class="m-ordergk">
					<div class="u-tt clearfix">
						<span class="fl f14">订单概况</span>
						<a href="javascript:;" class="fr btn mb10 hsbtn-78 mt10" id="refundOk">确认退款</a>
					</div>
					<div class="clearfix">
						<div class="fl m-taborder">
							<table>
								<tr>
									<td width="25%">
										<p class="tc f14">
											订单状态
										</p>
									</td>
									<td width="75%">
										<p class="pl20">
											{{$data['orderStatusStr']}}
										</p>
									</td>
								</tr>
								<tr>
									<td width="25%">
										<p class="tc f14">
											服务名称
										</p>
									</td>
									<td width="75%">
										<p class="pl20">
											【{{$data['goods']['id']}}】{{$data['goods']['name']}}
										</p>
									</td>
								</tr>
								<tr>
									<td width="25%">
										<p class="tc f14">
											服务价格
										</p>
									</td>
									<td width="75%">
										<p class="pl20 clearfix">
											<span class="fl">￥{{$data['totalFee']}}</span>
										</p>
									</td>
								</tr>
								<tr>
									<td width="25%">
										<p class="tc f14">
											实付金额
										</p>
									</td>
									<td width="75%">
										<p class="pl20 clearfix">
											<span class="fl">￥{{$data['payFee']}}（优惠￥{{$data['discountFee']}}）</span>
										</p>
									</td>
								</tr>
								@yizan_yield("deductAmount")
								<tr>
									<td width="25%">
										<p class="tc f14">
											提成金额
										</p>
									</td>
									<td width="75%">
										<p class="pl20 clearfix">
											<span class="fl">
												￥{{ $data['deductAmount'] or '0' }}
											</span>
										</p>
									</td>
								</tr>
								@yizan_stop
								@yizan_yield("seller")
								<tr>
									<td width="25%">
										<p class="tc f14">
											机构信息
										</p>
									</td>
									<td width="75%">
										<p class="pl20">
											{{$data['seller']['name']}}/{{$data['seller']['mobile']}}
										</p>
									</td>
								</tr>
								@yizan_stop
								@yizan_yield("staff")
								<tr>
									<td width="25%">
										<p class="tc f14">
											员工信息
										</p>
									</td>
									<td width="75%">
										<p class="pl20">
										</p>
									</td>
								</tr>
								@yizan_stop
								<tr>
									<td width="25%">
										<p class="tc f14">
											会员信息
										</p>
									</td>
									<td width="75%">
										<p class="pl20">
											{{$data['userName']}}/{{$data['mobile']}}
										</p>
									</td>
								</tr>
								<tr>
									<td width="25%">
										<p class="tc f14">
											会员地址
										</p>
									</td>
									<td width="75%">
										<p class="pl20">
											{{$data['address']}}
										</p>
									</td>
								</tr>
							</table>
						</div>
						<div class="fr m-mjbz fl m-taborder">
							<div class="fr m-mjbz">
								<p class="f-tt">
									退款备注
								</p>
								<div class="m-txtbox">
									<textarea name="" id="refundRemark" class="f-bztxt" placeholder="请填写退款备注"></textarea>
								</div>
							</div>
						</div>
					</div>
					<div class="u-tt clearfix">
						<p><span class="fl f14">订单备注</span></p>
					</div>
					<p>
						{{$data['buyRemark']}}
					</p>
				</div>
		<!-- @elseif( $data['payStatus'] == ORDER_PAY_STATUS_YES && in_array($data['status'],$refunding) ) -->
			<!-- 退款中、结果 -->
				<div class="m-ordergk">
					<div class="u-tt clearfix">
						<span class="fl f14">订单概况</span>
					</div>
					<div class="clearfix">
						<div class="fl m-taborder">
							<table>
								<tr>
									<td width="25%">
										<p class="tc f14">
											订单状态
										</p>
									</td>
									<td width="75%">
										<p class="pl20">
											{{$data['orderStatusStr']}}
										</p>
									</td>
								</tr>
								<tr>
									<td width="25%">
										<p class="tc f14">
											服务名称
										</p>
									</td>
									<td width="75%">
										<p class="pl20">
											【{{$data['goods']['id']}}】{{$data['goods']['name']}}
										</p>
									</td>
								</tr>
								<tr>
									<td width="25%">
										<p class="tc f14">
											服务价格
										</p>
									</td>
									<td width="75%">
										<p class="pl20 clearfix">
											<span class="fl">￥{{$data['totalFee']}}</span>
										</p>
									</td>
								</tr>
								<tr>
									<td width="25%">
										<p class="tc f14">
											实付金额
										</p>
									</td>
									<td width="75%">
										<p class="pl20 clearfix">
											<span class="fl">￥{{$data['payFee']}}（优惠￥{{$data['discountFee']}}）</span>
										</p>
									</td>
								</tr>
								@yizan_yield("deductAmount")
								<tr>
									<td width="25%">
										<p class="tc f14">
											提成金额
										</p>
									</td>
									<td width="75%">
										<p class="pl20 clearfix">
											<span class="fl">
												￥{{ $data['deductAmount'] or '0' }}
											</span>
										</p>
									</td>
								</tr>
								@yizan_stop
								@yizan_yield("seller")
								<tr>
									<td width="25%">
										<p class="tc f14">
											机构信息
										</p>
									</td>
									<td width="75%">
										<p class="pl20">
											{{$data['seller']['name']}}/{{$data['seller']['mobile']}}
										</p>
									</td>
								</tr>
								@yizan_stop
								@yizan_yield("staff")
								<tr>
									<td width="25%">
										<p class="tc f14">
											员工信息
										</p>
									</td>
									<td width="75%">
										<p class="pl20">
										</p>
									</td>
								</tr>
								@yizan_stop
								<tr>
									<td width="25%">
										<p class="tc f14">
											会员信息
										</p>
									</td>
									<td width="75%">
										<p class="pl20">
											{{$data['userName']}}/{{$data['mobile']}}
										</p>
									</td>
								</tr>
								<tr>
									<td width="25%">
										<p class="tc f14">
											会员地址
										</p>
									</td>
									<td width="75%">
										<p class="pl20">
											{{$data['address']}}
										</p>
									</td>
								</tr>
							</table>
						</div>
						<div class="fr m-mjbz fl m-taborder">
							<table>
								<tr>
									<td width="130px" height="81px">
										<p class="tc f14">
											退款备注
										</p>
									</td>
									<td>
										<p class="p5">
											{{$data['refund']['disposeRemark']}}
										</p>
									</td>
								</tr>
								<tr>
									<td width="130px" height="81px">
										<p class="tc f14">
											财务备注
										</p>
									</td>
									<td>
										<p class="p5 clearfix">
											{{$data['disposeRemark']}}
										</p>
									</td>
								</tr>
							</table>
						</div>
					</div>
					<div class="u-tt clearfix">
						<p><span class="fl f14">订单备注</span></p>
					</div>
					<p>
						{{$data['buyRemark']}}
					</p>
				</div>
		<!-- @else -->
			<!-- 不可操作的订单信息 -->
				<div class="m-ordergk">
					<div class="u-tt clearfix">
						<span class="fl f14">订单概况</span>
					</div>
					<div class="clearfix">
						<div class="fl m-taborder" style="width:460px;">
							<table>
								<tr>
									<td width="25%">
										<p class="tc f14">
											服务名称
										</p>
									</td>
									<td width="75%">
										<p class="pl20">
											【{{$data['goods']['id']}}】{{$data['goods']['name']}}
										</p>
									</td>
								</tr>
								<tr>
									<td width="25%">
										<p class="tc f14">
											订单状态
										</p>
									</td>
									<td width="75%">
										<p class="pl20">
											{{$data['orderStatusStr']}}
										</p>
									</td>
								</tr>
								<tr>
									<td width="25%">
										<p class="tc f14">
											支付状态
										</p>
									</td>
									<td width="75%">
										<p class="pl20">
											<!-- @if($data['payStatus']==0) -->
											等待支付
											<!-- @elseif($data['payStatus']==1) -->
											已支付
											<!-- @endif -->
										</p>
									</td>
								</tr>
								<tr>
									<td width="25%">
										<p class="tc f14">
											服务价格
										</p>
									</td>
									<td width="75%">
										<p class="pl20 clearfix">
											<span class="fl">￥{{$data['totalFee']}}</span>
										</p>
									</td>
								</tr>
								<tr>
									<td width="25%">
										<p class="tc f14">
											实付金额
										</p>
									</td>
									<td width="75%">
										<p class="pl20 clearfix">
											<span class="fl">￥{{$data['payFee']}}（优惠￥{{$data['discountFee']}}）</span>
										</p>
									</td>
								</tr>
								@yizan_yield("deductAmount")
								<tr>
									<td width="25%">
										<p class="tc f14">
											提成金额
										</p>
									</td>
									<td width="75%">
										<p class="pl20 clearfix">
											<span class="fl">
												￥{{ $data['deductAmount'] or '0' }}
											</span>
										</p>
									</td>
								</tr>
								@yizan_stop
							</table>
						</div>
						<!-- 右侧 -->
						<div class="fr m-taborder" style="width:460px;">
							<table>
								<tr>
									<td width="25%">
										<p class="tc f14">
											预约时间
										</p>
									</td>
									<td width="75%">
										<p class="pl20">
											{{yzhour($data['appointTime'])}}
										</p>
									</td>
								</tr>
								@yizan_yield("seller")
								<tr>
									<td width="25%">
										<p class="tc f14">
											机构信息
										</p>
									</td>
									<td width="75%">
										<p class="pl20">
											{{$data['seller']['name']}}/{{$data['seller']['mobile']}}
										</p>
									</td>
								</tr>
								@yizan_stop
								@yizan_yield("staff")
								<tr>
									<td width="25%">
										<p class="tc f14">
											员工信息
										</p>
									</td>
									<td width="75%">
										<p class="pl20">
											{{$data['staff']['name']}}/{{$data['staff']['mobile']}}
										</p>
									</td>
								</tr>
								@yizan_stop
								<tr>
									<td width="25%">
										<p class="tc f14">
											会员信息
										</p>
									</td>
									<td width="75%">
										<p class="pl20">
											{{$data['user']['name']}}/{{$data['user']['mobile']}}
										</p>
									</td>
								</tr>
								<tr>
									<td width="25%">
										<p class="tc f14">
											联系方式
										</p>
									</td>
									<td width="75%">
										<p class="pl20">
											{{$data['userName']}}/{{$data['mobile']}}
										</p>
									</td>
								</tr>
								<tr>
									<td width="25%">
										<p class="tc f14">
											服务地址
										</p>
									</td>
									<td width="75%">
										<p class="pl20">
											{{$data['address']}}
										</p>
									</td>
								</tr>
							</table>
						</div>
					</div>
					<div class="u-tt clearfix">
						<p><span class="fl f14">订单备注</span></p>
					</div>
					<p>
						{{$data['buyRemark']}}
					</p>
				</div>
		<!-- @endif -->
	</div>
	<!-- @else -->
		<div class="ts">未查询到相关订单</div>
	<!-- @endif -->
@stop
@section('js')
<script type="text/javascript">
	$(function(){
		$("#refundOk").click(function(){
			var regu = "^[ ]+$";
			var re = new RegExp(regu);
			var id = {{$data['id']}};
			var refundRemark = $("#refundRemark").val();
			if(refundRemark.length == 0 || re.test(refundRemark)) {
				$.ShowAlert("请填写退款备注");
			}else{
				$.post("{{ url('Order/refundRemark') }}",{"id":id,"remark":refundRemark},function(res){
					$.ShowAlert(res.msg);
					if(res.status==true) {
						window.location.reload();
					}
				},'json');
			}
		});
	})
	
</script>
@stop