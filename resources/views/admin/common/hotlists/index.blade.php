@extends('admin._layouts.base')
@section('css')
@stop
@section('right_content')
	@yizan_begin
	<yz:list>
		<search> 
			<row>
				<item name="hotwords" label="热搜词" ></item>
				<item name="city" label="城市" ></item> 
				<btn type="search"></btn>
			</row>
		</search>
		<btns>
			<linkbtn type="create" label="添加热搜词" url="{{ u('HotLists/create') }}"></linkbtn>
			<btn type="destroy" label="删除"></btn>
		</btns>
		<table checkbox="1">
			<columns> 
				<column code="id" label="编号" width="40"></column> 
				<column code="hotwords" label="热搜词" align="left"></column>  
				<column label="城市" align="center">
					{{ $list_item['city']['name'] }}
				</column> 
				<column code="sort" label="排序"></column> 
				<column code="status" label="状态" type="status"></column> 
				<actions> 
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
