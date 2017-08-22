@extends('admin._layouts.base')
@section('css')
<style type="text/css">
	.m-spboxlst .f-tt{width: 165px;}
</style>
@stop
@section('right_content')
	@yizan_begin
	<yz:form id="yz_form" action="edit">
		@foreach($data as $key => $value)
			<php> 
				$code = $value['code']; 
				$showType = $value['showType']; 
				$val = $value['val']; 
				$name = $value['name']; 
			</php>
			@if( $showType == 'image' )
			<yz:fitem name="{!! $code !!}" label="{{ $name }}" val="{{$val}}" type="image"></yz:fitem>
			@elseif( $showType == 'textarea' )
			<yz:fitem name="{!! $code !!}" label="{{ $name }}" val="{{$val}}" type="textarea"></yz:fitem>
			@elseif( $showType == 'radio' )
			<yz:fitem name="{!! $code !!}" label="{{ $name }}">
				<yz:radio name="{!! $code !!}" options="0,1" texts="否,是" checked="$val"></yz:radio>
			</yz:fitem>
			@elseif( $showType == 'editor' )
			<yz:fitem name="{!! $code !!}" label="{{ $name }}">
				<yz:editor name="{!! $code !!}" value="{{$val}}"></yz:editor>
			</yz:fitem>
			@else
			<yz:fitem name="{!! $code !!}" label="{{ $name }}" val="{{$val}}"></yz:fitem>
			@endif
		@endforeach
		</yz:form>
	@yizan_end
@stop

@section('js')

@stop
