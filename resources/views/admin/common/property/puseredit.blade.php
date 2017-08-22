@extends('admin._layouts.base')
@section('css')
@stop
@section('right_content')
@yizan_begin
	<yz:form id="yz_form" action="pusersave"> 
		<input type="hidden" name="sellerId" value="{{$args['sellerId']}}">
		<input type="hidden" name="puserId" value="{{$args['puserId']}}">
		<yz:fitem name="doorId" label="可用门禁">
        	<yz:select name="doorId" options="$doorIds" valuefield="id" textfield="name" selected="$data['doorId']"></yz:select>
        </yz:fitem>
		<yz:fitem name="endTime" label="截止时间" type="date"></yz:fitem>
	</yz:form>
@yizan_end
@stop 