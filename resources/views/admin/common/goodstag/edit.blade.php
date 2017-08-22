@extends('admin._layouts.base')
@section('css')
<style type="text/css">
	.ts1{color: #ccc;margin-left: 5px;}
</style>
@stop
@section('right_content')
	@yizan_begin
		<yz:form id="yz_form" action="save">
			<yz:fitem name="name" label="标签名称" attr="maxlength='20'"></yz:fitem> 
			<yz:fitem name="sort" label="排序" val="100"></yz:fitem>
			<yz:fitem label="状态">
				<php> $status = isset($data['status']) ? $data['status'] : 1 </php>
				<yz:radio name="status" options="1,0" texts="开启,关闭" checked="$status"></yz:radio>
			</yz:fitem>
		</yz:form>
	@yizan_end
@stop 