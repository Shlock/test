@extends('admin._layouts.base')
@section('css')
@stop
@section('right_content')
<!-- <h1>添加、编辑商品</h1>
	<form action="{{ url('goods/create') }}" method="post">
		<p>服务人员<input type="text" name="sellerId"></p>
		<p>服务名称<input type="text" name="name"></p>
		<p>价格<input type="text" name="price"></p>
		<p>门店价格<input type="text" name="marketPrice"></p>
		<p>分类编号<input type="text" name="cateId"></p>
		<p>简介<input type="text" name="brief"></p>
		<p>图片数组<input type="text" name="images"></p>
		<p>时长（秒）<input type="text" name="duration"></p>
		<p>状态<input type="text" name="status"></p>
		<p>排序<input type="text" name="sort"></p>
		<p><input type="submit"></p>
	</form> -->
	@yizan_begin
		<yz:form id="yz_form" action="create">
			<yz:fitem name="sellerId" label="服务人员">
				<attrs>
				    <attr></attr>
				</attrs>
			</yz:fitem>
			<yz:fitem name="name" label="服务名称" type="password"></yz:fitem>
			<yz:fitem name="price" label="价格"></yz:fitem>
			<yz:fitem name="marketPrice" label="门店价格"></yz:fitem>
			<yz:fitem label="分类">
				<yz:select name="cateId" options="0,1,2,3" texts="a,b,c,d"></yz:select>
			</yz:fitem>
			<yz:fitem label="简介" name="brief" type="textarea"></yz:fitem>
			<yz:fitem name="images" label="图片" type="image"></yz:fitem>
			<yz:fitem name="duration" label="时长(小时)" id="spinner"></yz:fitem>
			<yz:fitem name="sort" label="排序"></yz:fitem>
		</yz:form>
	@yizan_end
@stop

@section('js')
@stop
