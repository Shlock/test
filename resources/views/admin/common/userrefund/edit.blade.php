@extends('admin._layouts.base')
@section('right_content')
@yizan_begin
	<yz:form id="yz_form" action="edit">  
		<yz:fitem name="type" label="类型"></yz:fitem> 
		<yz:fitem name="title" label="标题"></yz:fitem> 
		<yz:fitem name="content" label="内容"></yz:fitem>  
		<yz:fitem name="userType" label="选择会员"></yz:fitem> 
		<yz:fitem name="users" label="要推送的会员手机号"></yz:fitem> 
		<yz:fitem name="args" label="推送参数" ></yz:fitem> 
	</yz:form>
@foreach ($errors->all() as $error)
    <p class="error">{{ $error }}</p>
 @endforeach 
@yizan_end

@stop   