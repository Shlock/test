@extends('admin._layouts.base')
@section('css')
<style type="text/css">
	.ts1{color: #ccc;margin-left: 5px;}
</style>
@stop
@section('right_content')
	@yizan_begin
		<yz:form id="yz_form" action="catesave">
			<yz:fitem name="sellerId" val="$data['sellerId']" type="hidden"></yz:fitem>
			<yz:fitem name="name" label="分类名称" attr="maxlength='20'"></yz:fitem>
			<!-- <yz:fitem label="所属分类">
				<yz:select name="pid" options="$cate" textfield="levelname" valuefield="id" attr="style='min-width:234px;width:auto'" selected="$data['pid']">
				</yz:select>
				<span class="ts ts1"></span>
			</yz:fitem>  -->
			<yz:fitem label="类型">
				<php> $type = isset($data['type']) ? $data['type'] : 1 </php>
				<yz:radio name="type" options="1,2" texts="商品,服务" checked="$type"></yz:radio>
			</yz:fitem>
			<!-- <yz:fitem name="logo" label="图标" type="image"></yz:fitem> -->
			<yz:fitem name="sort" label="排序" val="100"></yz:fitem>
			<yz:fitem label="状态">
				<php> $status = isset($data['status']) ? $data['status'] : 1 </php>
				<yz:radio name="status" options="1,0" texts="开启,关闭" checked="$status"></yz:radio>
			</yz:fitem>
		</yz:form>
	@yizan_end
@stop
@section('js')
@stop