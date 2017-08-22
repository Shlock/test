@extends('admin._layouts.base')
<?php  
$type = [ 
	['key'=>'0','name'=>'全员推送'],
	['key'=>'1','name'=>'指定推送'],
];
?>
@section('right_content')
	@yizan_begin
	<yz:list>
	    <search> 
			<row>
				<item label="推送类型">
					<yz:select name="userType" options="$type" textfield="name" valuefield="key" attr="style='min-width:160px;width:auto'"  selected="$search_args['userType']">"></yz:select>
				</item>
				<item name="sendTime" label="推送时间" type="date"></item>
				<item name="endsendTime" label="截至时间" type="date"></item> 
				<btn type="search"></btn>
			</row>
		</search>
		<btns>
			<linkbtn type="add" url="create"></linkbtn> 
		</btns>
		<table>
			<rows>
				<headers>
					<header label="推送类型"></header> 
					<header label="会员类型"></header> 
					<header label="推送参数"></header> 
					<header label="推送时间"></header> 
				</headers>
				<row>
					<tr class="{{ $list_item_css }}" style="text-align:left;">
						<td>
							<p>{{$list_item['type'] == 'buyer' ? '买家' : '卖家'}}</p>
						</td>  
						<td>
							<p>{{$list_item['userType'] == 0 ? '所有' : '指定'}}</p>
						</td>  
						<td style="text-align:left">
							<p>{{$list_item['args']}}</p>  
						</td> 
						<td> 
							<p>{{ yztime( $list_item['sendTime'] ) }}</p>
						</td> 
						<td rowspan="4">
							<actions> 
								<action type="destroy" css="red"></action>
							</actions>
						</td>
					</tr>
					<tr class="{{ $list_item_css }}" style="text-align:left;">	 
						<td colspan="4" style="text-align:left;">	
							<p><b>标　　题：</b>{{ $list_item['title'] }}</p>				
							<p><b>推送内容：</b>{{ yzHtmlSpecialcharsDecode( $list_item['content'] ) }}</p> 
						</td>  
					</tr>
					<tr class="{{ $list_item_css }}" style="text-align:left;">	
						<td colspan="4" style="text-align:left;">					
							<p style='text-align:left;display:block;word-break: break-all;word-wrap: break-word;'> 
								<b>推送类型</b>：@if($list_item['sendType'] == 1)
								 普通消息 
								 @elseif($list_item['sendType'] == 2)
								 html页面
								 @elseif($list_item['sendType'] == 3)
								 订单消息
								 @endif 
							</p> 
							<p style='text-align:left;display:block;word-break: break-all;word-wrap: break-word;'> 
								<b>推送参数</b>：{{$list_item['args']}}
							</p> 
						</td> 
					</tr>
					<tr class="{{ $list_item_css }}" style="text-align:left;">	
					@if($list_item['userType'] == 1)
						<td colspan="5" style="text-align:left;">					
							<p style='text-align:left;display:block;word-break: break-all;word-wrap: break-word;'> 
								<b>推送手机</b>：{{$list_item['users']}}
							</p> 
						</td> 
					@endif
					</tr>
					
				</row>
			</rows>
		</table>
	</yz:list>
	@yizan_end
@stop

@section('js')

@stop