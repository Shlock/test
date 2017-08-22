@extends('admin._layouts.base')
@section('css')
@stop
@section('right_content')
@yizan_begin
<yz:form id="yz_form" action="save"> 
	<yz:fitem name="title" label="标题"></yz:fitem>  
	<yz:fitem label="选择分类">
		<yz:select name="cateId" options="$cate" textfield="levelname" valuefield="id" attr="style='min-width:234px;width:auto'" selected="$data['cateId']">
		</yz:select>
	</yz:fitem>  
	<yz:fitem name="image" label="文章图片" type="image"></yz:fitem>
	<yz:fitem name="content" label="文章内容"> 
		<yz:Editor name="content" value="{{ $data['content'] }}"></yz:Editor> 
	</yz:fitem>
	<yz:fitem name="brief" label="简介" type="textarea"></yz:fitem>   
	<yz:fitem name="sort" label="排序"></yz:fitem>    
	<yz:fitem label="状态">
		<php> $status = isset($data['status']) ? $data['status'] : 1 </php>
		<yz:radio name="status" options="0,1" texts="关闭,正常"  checked="$status"></yz:radio>
	</yz:fitem> 
</yz:form>
@yizan_end 
@stop 