<div class="u-tt clearfix">
	<span class="fl f14">服务列表</span>
</div>
<div class="m-taborder tds" style="width:100%;margin-top:15px">
	<table>
		<tr style="background-color: #F3F6FA;">
			<td width="40%">
				<p class="tc f14">
					服务名称
				</p>
			</td>
			<td width="30%">
				<p class="tc f14">
					数量
				</p>
			</td>
			<td width="20%">
				<p class="tc f14">
					总价
				</p>
			</td>
		</tr>
		@foreach($data['OrderGoods'] as $goods)
			<tr>
				<td width="40%">
					<p class="tc f14">
						{{ $goods['goodsName'] }}
					</p>
				</td>
				<td width="30%">
					<p class="tc f14">
						x{{ $goods['num'] }}
					</p>
				</td>
				<td width="20%">
					<p class="tc f14">
                        ￥{{(double)round(($goods['price'] * $goods['num']),2)}}
					</p>
				</td>
			</tr>
		@endforeach
		<tr>
			<td width="100%" colspan="4" >
				<p class="tr pr10 f14">
                    <span>总金额：￥{{$data['totalFee']}}</span>
                    <span>服务总金额：￥{{$data['goodsFee']}}</span>
                    <span>优惠金额：￥{{$data['discountFee']}}</span>
                    <span>支付金额：￥{{$data['payFee']}}</span>
                    <span>商家金额：￥{{$data['sellerFee']}}</span>
                    <span>抽成金额：￥{{(double)$data['drawnFee']}}</span>
				</p>
			</td>
		</tr>

	</table>
</div>