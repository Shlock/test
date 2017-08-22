@extends('admin._layouts.base')
@section('css')
<style type="text/css">
	td p{padding: 2px;}
</style>
@stop

@section('right_content')
	@yizan_begin
	<yz:list>
		@if($seller)
		<search> 
			<row>
				<item label="物业公司">
					{{$seller['name']}}
				</item>
				<item label="小区名称">
					{{$seller['district']['name']}}
				</item>
			</row>
		</search>
		@endif
		<table>
			<btns>
				<linkbtn type="add">
					<attrs>
						<url>{{ u('Property/puseredit',['puserId'=>$args['puserId'], 'sellerId'=>$args['sellerId']]) }}</url>
					</attrs>
				</linkbtn>
			</btns>
			<columns>
				<column code="door" label="门禁号">
					<p>{{ $list_item['door']['name'] }}</p>
				</column>
				<column code="endTime" label="门禁截止时间">
					{{ yzday($list_item['endTime']) }}
				</column>
				<actions width="100">
					<action label="编辑" >
						<attrs>
							<url>{{ u('Property/puseredit',['puserId'=>$args['puserId'], 'id'=> $list_item['id'], 'sellerId'=>$args['sellerId']]) }}</url>
						</attrs>
					</action>
					<action label="删除" css="red">
						<attrs>
							<click>$.RemoveItem(this, '{!!u('Property/puserdestroydoor',['puserId'=>$args['puserId'], 'id'=>$list_item['id'], 'sellerId'=>$args['sellerId']])!!}', '你确定要删除该门禁吗？');</click>
						</attrs>
					</action>
				</actions>
			</columns>
		</table>
	</yz:list>
	@yizan_end
@stop

@section('js')

@stop