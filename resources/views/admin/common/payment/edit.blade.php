@extends('admin._layouts.base')
@section('css')
@stop
@section('right_content')
@yizan_begin
<yz:form id="yz_form" action="update">
	<yz:fitem name="code" label="支付代码"></yz:fitem>
	<yz:fitem name="name" label="支付名称"></yz:fitem>
	<yz:fitem name="config" label="相关支付参数"> 
		<yz:Editor name="config" value="{{ $data['config'] }}"></yz:Editor>
	</yz:fitem> 
</yz:form>

@foreach ($errors->all() as $error)
    <p class="error">{{ $error }}</p>
 @endforeach
@yizan_end

@stop


