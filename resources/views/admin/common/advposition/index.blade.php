@extends('admin._layouts.base')

@section('right_content')
	@yizan_begin
	<yz:list> 
		<btns>
			<linkbtn type="add" url="create"></linkbtn>
		</btns>
		<table pager="no">
			<columns> 
				<column code="code" label="广告位代码"></column>
				<column code="name" label="广告位名称"></column>
				@yizan_yield('list_column')
				<column code="clientType" label="类型">
					{{ Lang::get('admin.type.'.$data['clientType']) }}
				</column>
				@yizan_stop
				<column code="width"  label="宽度" width="80"></column>  
				<column code="height" label="高度" width="80"></column>
				<actions width="70"> 
					<action type="edit" css="blu"></action>
					@if($list_item['isSystem'] == 0)
					<action type="destroy" css="red"></action>
					@endif
				</actions> 
			</columns>
		</table>
	</yz:list>
	@yizan_end
@stop
