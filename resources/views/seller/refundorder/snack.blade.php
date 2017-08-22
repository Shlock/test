<div class="m-taborder" style="width:100%;margin-top:15px">
	<table>
		<tr style="background-color: #fff;">
			<td width="50%">
				<p class="tc f14">
					美食明细
				</p>
			</td>
			<td width="50%">
				<p class="tc f14">
					美食价格
				</p>
			</td>
		</tr>
		@foreach($data['OrderGoods'] as $vo )
			 <tr>
				<td width="50%">
					<p class="tc f14">
						{{ $vo['goods'][0]['name'] }}
					</p>
				</td>
				<td width="50%">
					<p class="tc f14">
						￥{{ $vo['goods'][0]['price'] }}
					</p>
				</td>
            </tr>	
		@endforeach
	</table>
</div>
