@extends('admin._layouts.base')
@section('css')
@stop
@section('right_content')
	@yizan_begin
		<yz:list>
			@yizan_yield("search")
			<search> 
				<row>
					<item name="name" label="商家名"></item>
					<item label="审核状态">
						<yz:select name="isCheck" options="2,1" texts="待审核,拒绝" selected="$search_args['isCheck']"></yz:select>
					</item>
					<btn type="search"></btn>
				</row>
			</search>
			@yizan_stop
			@yizan_yield("btn")
			<btns>
			</btns>
			@yizan_stop
			<table >
				<columns>
					<column code="id" label="编号" width="40"></column>
					<column code="name" label="商家名" align="left">
					</column>
					<column code="contacts" label="联系人">
					{{ $list_item['contacts'] ? $list_item['contacts'] : $list_item['name']}}
					</column>
					<column code="mobile" label="电话" align="left" width="100">
					</column>
					<column code="createTime" label="申请时间">
						{{ yztime($list_item['createTime']) }}
					</column>
					<column code="isCheck" label="状态" width="40">
						@if($list_item['isCheck'] == 1)
						<p>已通过</p>
						@elseif($list_item['isCheck'] == -1)
						<p>拒绝</p>
						@else
						<p>待审核</p>
						@endif
					</column>
					<column code="checkVal" label="操作理由"></column>
					<actions width="60">
						<action type="edit" css="blu"></action>
						<action type="destroy" css="red"></action>
					</actions>
				</columns>
			</table>
		</yz:list>
	@yizan_end
@stop
@section('js')
@stop

