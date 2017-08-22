@extends('admin._layouts.base')
@section('css')
@stop
@section('right_content')
@yizan_begin
	<yz:form id="yz_form" action="update"> 
		<yz:fitem name="cityId" label="显示城市"></yz:fitem> 
		<yz:fitem name="app" label="App类型"></yz:fitem> 
		<yz:fitem name="name" label="名称"></yz:fitem> 
		<yz:fitem name="icon" label="图标" type="image"></yz:fitem> 
		<yz:fitem name="bgColor" label="背景颜色"></yz:fitem>  
		<yz:fitem name="type" label="动作类型"></yz:fitem> 
		<yz:fitem name="arg" label="动作参数"></yz:fitem> 
		<yz:fitem name="sort" label="排序" ></yz:fitem> 
	</yz:form>
@foreach ($errors->all() as $error)
    <p class="error">{{ $error }}</p>
 @endforeach
 
@yizan_end

@stop  
