@extends('admin._layouts.base')
@section('css')
@stop
@section('right_content')
	@yizan_begin
	<yz:list>
		<btns>
			<linkbtn label="添加管理员组" url="{{ u('AdminRole/create') }}"></linkbtn>
		</btns>
		<table>
			<columns>
				<column code="id" label="编号"></column>
				<column code="name" label="组名称"></column>
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
