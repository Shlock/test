@extends('admin._layouts.base')
@section('css')
@stop
@section('right_content')
	@yizan_begin
	<yz:list>
		<table>
			<columns>  
				<column label="SN" width="150" align="center">
					{{ $list_item['sn'] }}
				</column>
				<column label="商家信息" width="150" align="left">
					<div>名称：{{ $list_item['seller']['name'] }}</div>
					<div>手机：{{ $list_item['seller']['mobile'] }}</div> 
				</column>
				<column label="金额" width="40" align="center">
					{{ $list_item['money'] }}
				</column>
				<column label="描述" align="left">
					{{ $list_item['content'] }}
				</column>
				<column label="状态" width="40" align="center">
					{{ Lang::get('admin.sellerPayType.'.$list_item['status']) }}
				</column>				<column label="创建时间" align="left">
					{{  yzTime($list_item['createTime']) }}
				</column> 
				<column label="支付时间" align="left">
					{{  yzTime($list_item['payTime']) }}
				</column>
			</columns>
		</table>
	</yz:list>
	@yizan_end
@stop

@section('js')

@stop
