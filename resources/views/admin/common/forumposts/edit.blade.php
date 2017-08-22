@extends('admin._layouts.base')
@section('css')
<style type="text/css">
	.ts1{color: #ccc;margin-left: 5px;}
</style>
@stop
@section('right_content')
	@yizan_begin
		@if($data['pid'] == 0)
		<yz:form id="yz_form" action="save">
			<yz:fitem name="title" label="标题" attr="maxlength='50'"></yz:fitem> 
			<yz:fitem name="content" label="详细内容"> 
				<yz:Editor name="content" value="{{ $data['content'] }}"></yz:Editor> 
			</yz:fitem>
			<yz:fitem label="图片">
				<yz:imageList name="images." images="$data['images']"></yz:imageList>
			</yz:fitem> 
		</yz:form>
		@else
		<yz:form id="yz_form" action="save">
			<!-- <yz:fitem name="title" label="标题" attr="maxlength='50'"></yz:fitem>  -->
			<yz:fitem name="content" label="评论内容"> 
				<yz:Editor name="content" value="{{ $data['content'] }}"></yz:Editor> 
			</yz:fitem>
		</yz:form>
		@endif
	@yizan_end
@stop