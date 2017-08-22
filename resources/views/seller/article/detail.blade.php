@extends('seller._layouts.base')
@section('css')
@stop
@section('content')
@yizan_begin
<yz:form id="yz_form" action="update"> 
	<yz:fitem name="title" label="标题"></yz:fitem>  
	<yz:fitem label="选择分类">
		<yz:select name="cate_id" options="0,1,2" texts="哈哈,呵呵,嘿嘿"></yz:select>
	</yz:fitem>  
	<yz:fitem name="image" label="文章图片" type="image"></yz:fitem>
	<yz:fitem name="content" label="文章内容"> 
		<yz:Editor name="content" value="{{ $data['content'] }}"></yz:Editor> 
	</yz:fitem>
	<yz:fitem name="brief" label="简介">
		<textarea name="brief"> {{ $data['brief'] }} </textarea>
	</yz:fitem>  
	<yz:fitem name="sort" label="排序"></yz:fitem>    
	<yz:fitem label="状态">
		<yz:radio name="status" options="0,1" texts="关闭,正常" checkbox="{{ $data['status'] }}"></yz:radio>
	</yz:fitem>    
</yz:form>
 
@yizan_end 
@stop 