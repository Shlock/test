@extends('admin._layouts.base')
@section('right_content')
    @yizan_begin
        <yz:form id="yz_form" action="save">
            <input type="hidden" name="sellerId" value="{{$sellerId}}">
            <yz:fitem label="物业公司">
            	{{$seller['name']}}
            </yz:fitem>
            <yz:fitem label="小区名称">
            	{{$seller['district']['name']}}
            </yz:fitem>
            <yz:fitem name="name" label="楼栋号"></yz:fitem>
            <yz:fitem label="备注" name="remark"></yz:fitem>
        </yz:form>
    @yizan_end
@stop