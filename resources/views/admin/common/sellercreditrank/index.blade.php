@extends('admin._layouts.base')
@section('css')
@stop
@section('right_content')
@yizan_begin 
		<yz:list>  
		<table pager="no" checkbox="1">
		<btns> 
			<linkbtn type="add" url="create"></linkbtn>
			<btn type="destroy" label="删除"></btn> 
		</btns>
		<columns>				  
			<column code="name" label="名称" ></column> 
			<column code="icon" label="图标" >
				<img src="{{ $list_item['icon'] }}" style="max-width:90px">
			</column> 
			<column code="minScore" label="最低分"></column> 
			<column code="maxScore" label="最高分"></column>
			<actions width="90">
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