@extends('admin._layouts.base')
@section('right_content')
@yizan_begin
	<yz:form id="yz_form" action="edit"> 
		<yz:fitem name="data" label="申请协议" type="textarea"></yz:fitem>  
	</yz:form>
@foreach ($errors->all() as $error)
    <p class="error">{{ $error }}</p>
 @endforeach
 
@yizan_end

@stop