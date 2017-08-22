@extends('admin._layouts.base')
@section('css') 
@stop
@section('right_content')
	@yizan_begin 
		<yz:form id="yz_form" action="save"> 
			<yz:fitem name="name" label="报修类型"></yz:fitem>   
			<yz:fitem name="sort" label="编号"></yz:fitem> 
		</yz:form> 
	@yizan_end
	<script type="text/javascript"> 
	</script>
@stop