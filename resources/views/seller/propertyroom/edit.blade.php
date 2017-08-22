@extends('seller._layouts.base')
@section('content')
<div>
        <div class="m-zjgltbg">                 
            <div class="p10">
                <div class="g-fwgl">
                    <p class="f-bhtt f14 clearfix">
                        <span class="ml15 fl">房间管理</span>
                    </p>
                </div>
                <div class="m-tab m-smfw-ser pt20">
                    @yizan_begin
                        <yz:form id="yz_form" action="save">
                            <input type="hidden" name="buildId" value="{{$build['id']}}">
                            <yz:fitem name="build" label="楼栋号">
                                {{ $build['name'] }}
                            </yz:fitem>
                            <yz:fitem name="roomNum" label="房间号"></yz:fitem>
                            <yz:fitem name="owner" label="业主"></yz:fitem>
                            <yz:fitem name="mobile" label="联系电话"></yz:fitem>
                            <yz:fitem name="propertyFee" label="物业费"></yz:fitem>
                            <yz:fitem name="structureArea" label="建筑面积" tip="平方米"></yz:fitem>
                            <yz:fitem name="roomArea" label="套内面积" tip="平方米"></yz:fitem>
                            <yz:fitem name="intakeTime" label="入住时间" type="date"></yz:fitem>
                            <yz:fitem label="备注" name="remark"></yz:fitem>
                        </yz:form>
                    @yizan_end
                </div>
            </div>
        </div>
    </div>
@stop