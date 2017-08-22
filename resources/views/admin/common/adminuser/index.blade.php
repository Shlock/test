@extends('admin._layouts.base')
@section('right_content')
<link rel="stylesheet" type="text/css" href="{{ asset('static/kindeditor/themes/default/default.css') }}">
<script src="{{ asset('static/kindeditor/kindeditor-min.js') }}"></script>
<script src="{{ asset('static/kindeditor/lang/zh_CN.js') }}"></script>
	@yizan_begin
	<yz:list>
		<btns>
			<linkbtn type="add" url="{{ u('AdminUser/create') }}"></linkbtn>
		</btns>
		<table>
			<columns>
				<column code="name" label="管理员"></column>
				<column code="role.name" label="所属组"></column>
				<column code="loginTime" label="最后登录时间">
					{{ yztime( $list_item['loginTime'] ) }}
				</column>
				<column code="loginIp" label="最后登录IP"></column>
				<column code="loginCount" label="登录次数"></column>
				<column code="createTime" label="创建时间">
					{{ yzday( $list_item['createTime'] ) }}
				</column>
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