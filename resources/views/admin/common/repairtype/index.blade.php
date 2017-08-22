@extends('admin._layouts.base')

@section('css')
@section('right_content')
	@yizan_begin
		<yz:list> 
			<btns>
				<linkbtn label="添加" url="{{ u('RepairType/create') }}" css="btn-green"></linkbtn>
			</btns>
			<table> 
				<columns> 
					<column code="name" label="报修类型"></column>
					<column code="sort" label="编号" ></column> 
					<actions width="60">
						<action label="编辑" type="edit" css="blu"></action>
						<action label="删除" type="destroy" css="red"></action> 
					</actions>
				</columns>
			</table>
		</yz:list>
	@yizan_end
@stop

@section('js')
@stop
