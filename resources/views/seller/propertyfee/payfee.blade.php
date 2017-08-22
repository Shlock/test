@extends('seller._layouts.base')
@section('content')
<div>
        <div class="m-zjgltbg">                 
            <div class="p10">
                <div class="g-fwgl">
                    <p class="f-bhtt f14 clearfix">
                        <span class="ml15 fl">物业费缴费</span>
                    </p>
                </div>
                <div class="m-tab m-smfw-ser pt20">
                    @yizan_begin
                        <yz:form id="yz_form" action="paysave">
                            <yz:fitem name="buildId" label="楼栋号">
                                {{$data['build']['name']}}
                            </yz:fitem>
                            <yz:fitem  name="roomId" label="房间号">
                                {{$data['room']['roomNum']}}
                            </yz:fitem>
                            <yz:fitem name="name" label="业主">
                                {{$data['name']}}
                            </yz:fitem>
                            <yz:fitem name="payFee" label="实缴金额"></yz:fitem>
                            <yz:fitem name="payTime" label="实缴日期" type="date"></yz:fitem>
                        </yz:form>
                    @yizan_end
                </div>
            </div>
        </div>
    </div>
@stop