@extends('admin._layouts.base')
@section('css')
<style type="text/css">
	.hscolor{color: #ccc}
</style>
@stop
<?php 
	$type = [
		['id'=>'1','name'=>'买赠叠加'],
	];
	$c = [
		'all' => '全车洗',
		'body' => '洗车身',
	];
	$t = [
		'money'	=> '优惠券',
		'offset' => '抵用券',
		'discount' => '折扣券',
	];
 ?>
@section('right_content')
	@yizan_begin
		<yz:list>
			<search> 
				<row>
					<item label="活动类型">
						<yz:select name="type" options="$type" textfield="name" valuefield="id" attr="style='min-width:100px;width:auto'" selected="$search_args['type']">
						</yz:select>
					</item>
					<item name="name" label="活动名称"></item>
					<btn type="search"></btn>
				</row>
			</search>
			<btns>
				<linkbtn label="新建促销活动" url="{{ u('ActivityTicket/create') }}" css="btn-green"></linkbtn>
			</btns>
			<table>
				<columns>
					<column code="id" label="编号" width="35"></column>
					<column label="活动类型">
						{{ $type[$list_item['type']-1]['name'] }}
					</column>
					<column code="name" label="活动名称"></column>
					<column label="活动时间" width="110">
						<p>开始：{{ yzday($list_item['beginTime']) }}</p>
						<p>结束：{{ yzday($list_item['endTime']) }}</p>
					</column>
					<column code="" label="活动信息" width="300" align="left">
						<p>
							购买：￥
							{{ $list_item['sellpromotion']['data'].
							$c[$list_item['sellpromotion']['conditionType']].
							$t[$list_item['sellpromotion']['type']] }}
							({{ $list_item['sellTicketNum'] }})
						</p>
						@if($list_item['type']==1)
							<p>
								赠送：￥
								{{ $list_item['giftpromotion']['data'].
								$c[$list_item['giftpromotion']['conditionType']].
								$t[$list_item['giftpromotion']['type']] }}
								({{ $list_item['giftTicketNum'] }})
							</p>
						@endif
					</column>
					<actions width="70">
						<action type="edit"></action>
						<action type="destroy"></action>
					</actions>
				</columns>
			</table>
		</yz:list>
	@yizan_end
@stop
@section('js')

@stop