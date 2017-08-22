@extends('admin._layouts.base')
@section('css')

@stop
@section('right_content')
	@yizan_begin
		<yz:list>
			<btns>
				<linkbtn label="添加城市" url="{{ u('City/create') }}"></linkbtn>
			</btns>
			<table pager="no">
				<columns>
					<column code="id" label="编号"></column>
					<column code="name" label="城市名称"></column>
					<column code="firstChar" label="城市首字母"></column>
					<column code="sort" label="排序"></column>
					<column label="默认城市">
						<!-- @if($list_item['isDefault']==true) -->
						是
						<!-- @else -->
						-
						<!-- @endif -->
					</column>
					<actions>
						@if($list_item['isDefault']==false)
						<action label="设为默认" click="isdefault({{$list_item['id']}})" css="blu"></action>
						@else
						<action label="设为默认" click="javascript:;" style="color:#ccc;cursor:default"></action>
						@endif
						<action type="destroy" css="red"></action>
					</actions>
				</columns>
			</table>
		</yz:list>
	@yizan_end
@stop

@section('js')
<script type="text/javascript">
	function isdefault (id) {
		$.post("{{ u('City/isdefault') }}",{'id':id},function(result){
			window.location.reload();
		});
	}
</script>
@stop
