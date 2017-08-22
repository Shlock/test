@extends('admin._layouts.base_nomenu')
@section('css')

@stop
@section('return_link')
<a href="" class="btn mb10 lsbtn-120"><i class="fa fa-reply mr10"></i>返回订单列表</a>
@stop

@section('right_content')
	<!-- 退款申请 -->
	<div class="m-ddbgct">
		<div class="m-ddh">
			<p class="f-tt">
				订单号：CX202015216546541
			</p>
			<?php $width=(100/4).'%'; ?> 
			<div class="m-porbar clearfix">
				<p class="f-bar"></p>
				<ul class="m-barlst clearfix">
					<li class="on" style="width:{{$width}}">
						<p class="f-lsbar"></p>
						<span class="num">1</span>
						<p class="tc mt5">已付款</p>
						<p class="tc">2015-07-12 12:02:55</p>
					</li>
					<li class="on" style="width:{{$width}}">
						<p class="f-lsbar"></p>
						<span class="num">2</span>
						<p class="tc mt5">申请退款</p>
						<p class="tc">2015-07-12 12:02:55</p>
					</li>
					<li style="width:{{$width}}">
						<p class="f-lsbar"></p>
						<span class="num">3</span>
						<p class="tc mt5">退款中</p>
						<p class="tc">2015-07-12 12:02:55</p>
					</li>
					<li style="width:{{$width}}">
						<p class="f-lsbar"></p>
						<span class="num">4</span>
						<p class="tc mt5">已退款/拒绝退款</p>
						<p class="tc">2015-07-12 12:02:55</p>
					</li>
				</ul>
			</div>
		</div>
		<!-- 订单概况 -->
		<div class="m-ordergk">
			<div class="u-tt clearfix">
				<p><span class="fl f14">申请原因</span></p>
			</div>
			<p>
				外地出差外地出差外地出差外地出差外地出差外地出差外地出差外地出差外地出差外地
			</p>
			<div class="u-tt clearfix">
				<span class="fl f14">订单概况</span>
				<a href="javascript:;" class="fr btn mb10 hsbtn-78 mt10" id="qrfhbtn">确认退款</a>
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
									已出发
								</p>
							</td>
						</tr>
						<tr>
							<td width="25%">
								<p class="tc f14">
									商品名称
								</p>
							</td>
							<td width="75%">
								<p class="pl20">
									【81】上门洗发服务
								</p>
							</td>
						</tr>
						<tr>
							<td width="25%">
								<p class="tc f14">
									商品价格
								</p>
							</td>
							<td width="75%">
								<p class="pl20 clearfix">
									<span class="fl">￥1652.00</span>
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
									<span class="fl">￥1652.00（优惠￥15.00）</span>
								</p>
							</td>
						</tr>
						<tr>
							<td width="25%">
								<p class="tc f14">
									服务信息
								</p>
							</td>
							<td width="75%">
								<p class="pl20">
									Fanwe/13000000000
								</p>
							</td>
						</tr>
						<tr>
							<td width="25%">
								<p class="tc f14">
									会员信息
								</p>
							</td>
							<td width="75%">
								<p class="pl20">
									张大宝/158632562323
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
									两路口新干线大厦B座
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
							<textarea name="" id="" class="f-bztxt">暂无备注</textarea>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<!-- 退款 -->
	<div class="m-ddbgct">
		<div class="m-ddh">
			<p class="f-tt">
				订单号：CX202015216546541
			</p>
			<?php $width=(100/4).'%'; ?> 
			<div class="m-porbar clearfix">
				<p class="f-bar"></p>
				<ul class="m-barlst clearfix">
					<li class="on" style="width:{{$width}}">
						<p class="f-lsbar"></p>
						<span class="num">1</span>
						<p class="tc mt5">已付款</p>
						<p class="tc">2015-07-12 12:02:55</p>
					</li>
					<li class="on" style="width:{{$width}}">
						<p class="f-lsbar"></p>
						<span class="num">2</span>
						<p class="tc mt5">申请退款</p>
						<p class="tc">2015-07-12 12:02:55</p>
					</li>
					<li style="width:{{$width}}">
						<p class="f-lsbar"></p>
						<span class="num">3</span>
						<p class="tc mt5">退款中</p>
						<p class="tc">2015-07-12 12:02:55</p>
					</li>
					<li style="width:{{$width}}">
						<p class="f-lsbar"></p>
						<span class="num">4</span>
						<p class="tc mt5">已退款/拒绝退款</p>
						<p class="tc">2015-07-12 12:02:55</p>
					</li>
				</ul>
			</div>
		</div>
		<!-- 订单概况 -->
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
									已出发
								</p>
							</td>
						</tr>
						<tr>
							<td width="25%">
								<p class="tc f14">
									商品名称
								</p>
							</td>
							<td width="75%">
								<p class="pl20">
									【81】上门洗发服务
								</p>
							</td>
						</tr>
						<tr>
							<td width="25%">
								<p class="tc f14">
									商品价格
								</p>
							</td>
							<td width="75%">
								<p class="pl20 clearfix">
									<span class="fl">￥1652.00</span>
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
									<span class="fl">￥1652.00（优惠￥15.00）</span>
								</p>
							</td>
						</tr>
						<tr>
							<td width="25%">
								<p class="tc f14">
									服务信息
								</p>
							</td>
							<td width="75%">
								<p class="pl20">
									Fanwe/13000000000
								</p>
							</td>
						</tr>
						<tr>
							<td width="25%">
								<p class="tc f14">
									会员信息
								</p>
							</td>
							<td width="75%">
								<p class="pl20">
									张大宝/158632562323
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
									两路口新干线大厦B座
								</p>
							</td>
						</tr>
					</table>
				</div>
				<div class="fr m-mjbz fl m-taborder">
					<table>
						<tr>
							<td width="130px" height="121px">
								<p class="tc f14">
									申请原因
								</p>
							</td>
							<td width="*">
								<p class="p5" style="line-height:1">
									外地出差外地出差外地出差外
								</p>
							</td>
						</tr>
						<tr>
							<td width="130px" height="81px">
								<p class="tc f14">
									退款备注
								</p>
							</td>
							<td>
								<p class="p5">
									允许退款
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
									退款成功
								</p>
							</td>
						</tr>
					</table>
				</div>
			</div>
		</div>
	</div>

	<!-- 订单信息 -->
	<div class="m-ddbgct mt20">
		<div class="m-ddh">
			<p class="f-tt">
				订单号：CX202015216546541
			</p>
			<?php $width=(100/6).'%'; ?> 
			<div class="m-porbar clearfix">
				<p class="f-bar"></p>
				<ul class="m-barlst clearfix">
					<li class="on" style="width:{{$width}}">
						<p class="f-lsbar"></p>
						<span class="num">1</span>
						<p class="tc mt5">已付款/未付款</p>
						<p class="tc">2015-07-12 12:02:55</p>
					</li>
					<li class="on" style="width:{{$width}}">
						<p class="f-lsbar"></p>
						<span class="num">2</span>
						<p class="tc mt5">已出发</p>
						<p class="tc">2015-07-12 12:02:55</p>
					</li>
					<li style="width:{{$width}}">
						<p class="f-lsbar"></p>
						<span class="num">3</span>
						<p class="tc mt5">已服务</p>
						<p class="tc">2015-07-12 12:02:55</p>
					</li>
					<li style="width:{{$width}}">
						<p class="f-lsbar"></p>
						<span class="num">4</span>
						<p class="tc mt5">已完成</p>
						<p class="tc">2015-07-12 12:02:55</p>
					</li>
					<li style="width:{{$width}}">
						<p class="f-lsbar"></p>
						<span class="num">5</span>
						<p class="tc mt5">确定完成</p>
						<p class="tc">2015-07-12 12:02:55</p>
					</li>
					<li style="width:{{$width}}">
						<p class="f-lsbar"></p>
						<span class="num">6</span>
						<p class="tc mt5">已评价</p>
						<p class="tc">2015-07-12 12:02:55</p>
					</li>
				</ul>
			</div>
			
		</div>
		<!-- 订单概况 -->
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
									订单状态
								</p>
							</td>
							<td width="75%">
								<p class="pl20">
									已出发
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
									已支付
								</p>
							</td>
						</tr>
						<tr>
							<td width="25%">
								<p class="tc f14">
									商品价格
								</p>
							</td>
							<td width="75%">
								<p class="pl20 clearfix">
									<span class="fl">￥1652.00</span>
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
									<span class="fl">￥1652.00（优惠￥15.00）</span>
								</p>
							</td>
						</tr>
					</table>
				</div>
				<!-- 右侧 -->
				<div class="fr m-taborder" style="width:460px;">
					<table>
						
						<tr>
							<td width="25%">
								<p class="tc f14">
									商品名称
								</p>
							</td>
							<td width="75%">
								<p class="pl20">
									【81】上门洗发服务
								</p>
							</td>
						</tr>
						<tr>
							<td width="25%">
								<p class="tc f14">
									服务信息
								</p>
							</td>
							<td width="75%">
								<p class="pl20">
									Fanwe/13000000000
								</p>
							</td>
						</tr>
						<tr>
							<td width="25%">
								<p class="tc f14">
									会员信息
								</p>
							</td>
							<td width="75%">
								<p class="pl20">
									张大宝/158632562323
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
									两路口新干线大厦B座
								</p>
							</td>
						</tr>
					</table>
				</div>
			</div>
		</div>
	</div>
@stop