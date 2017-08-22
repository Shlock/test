@extends('seller._layouts.base')
@section('content')
<div>
        <div class="m-zjgltbg">                 
            <div class="p10">
                <!-- 楼宇管理 -->
                <div class="g-fwgl">
                    <p class="f-bhtt f14 clearfix">
                        <span class="ml15 fl">楼宇管理</span>
                    </p>
                </div>
                <!-- 楼宇管理 -->
                <div class="m-tab m-smfw-ser pt20">
                    @yizan_begin
                        <yz:form id="yz_form" action="save">
                            <yz:fitem name="name" label="楼栋号"></yz:fitem>
                            <yz:fitem label="备注" name="remark"></yz:fitem>
                        </yz:form>
                    @yizan_end
                </div>
            </div>
        </div>
    </div>
@stop