@extends('admin._layouts.base')
@section('css')
@stop
@section('right_content')
@yizan_begin

<yz:form id="yz_form" action="update">
	<yz:fitem name="sellerId" label="服务人员编号"></yz:fitem>
	<yz:fitem name="remark" label="备注信息">
		<yz:Editor name="remark"></yz:Editor>
	</yz:fitem> 
	<yz:fitem name="status" label="认证状态">
		 <yz:radio name="status" options="0,1" texts="未认证,认证" checkbox="0"></yz:radio>
	</yz:fitem> 
</yz:form>

@foreach ($errors->all() as $error)
    <p class="error">{{ $error }}</p>
 @endforeach
 
@yizan_end

@stop