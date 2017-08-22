<?php $goodsTypeName = $data['orderType'] == 1 ? '商品' : '服务'; ?>
<div class="u-tt clearfix">
	<span class="fl f14">{{$goodsTypeName}}列表</span>
</div>
<div class="m-taborder tds" style="width:100%;margin-top:15px">
	<table>
		<tr style="background-color: #F3F6FA;">
			<td width="40%">
				<p class="tc f14">
                    {{$goodsTypeName}}名称
				</p>
			</td>
			<td width="30%">
				<p class="tc f14">
					数量
				</p>
			</td>
			<td width="20%">
				<p class="tc f14">
					单价
				</p>
			</td>
		</tr>
        @foreach($data['orderGoods'] as $val)
		<tr>
			<td width="40%">
				<p class="tc f14">
					{{$val['goodsName']}}@if($data['goodsNormsId'] > 0) - 【{{$data['goodsNorms']}}}】 @endif
				</p>
			</td>
			<td width="30%">
				<p class="tc f14">
					x{{$val['num']}}
				</p>
			</td>
			<td width="20%">
				<p class="tc f14">
					￥{{$val['price']}}
				</p>
			</td>
		</tr>
        @endforeach
		<tr>
			<td width="100%" colspan="4" >
				<p class="tr pr10 f14">
                    {{$goodsTypeName}}总金额：￥{{$data['goodsFee']}}
				</p>
			</td>
		</tr>

	</table>
</div>
