@extends('admin._layouts.base')
@section('css')
    <style type="text/css">
        .ts{color: #999;margin-left: 5px;vertical-align:middle;}
    </style>
@stop
@section('right_content')
    @yizan_begin
    <yz:form id="yz_form" action="save">
        <yz:fitem name="name" label="活动名称"></yz:fitem>
        <yz:fitem name="startTime" label="开始时间" type="date"></yz:fitem>
        <yz:fitem name="endTime" label="结束时间" type="date"></yz:fitem>
        <yz:fitem label="赠送券">
            <yz:select name="promotionId" options="$promotionList" textfield="name" valuefield="id" selected="$data['promotionId']"></yz:select>
        </yz:fitem>
        <yz:fitem name="num" label="赠送券数量" append="1">
            <span class="ts">张</span>
        </yz:fitem>
        <yz:fitem label="状态">
            <php> $data['status'] = isset($data['status']) ? $data['status'] : 1; </php>
            <yz:radio name="status" options="1,0" texts="启用,禁用" checked="$data['status']"></yz:radio>
        </yz:fitem>
    </yz:form>
    @yizan_end
@stop
@section('js')

@stop
