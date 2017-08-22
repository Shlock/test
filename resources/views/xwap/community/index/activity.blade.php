@extends('wap.run._layouts.base')

@section('show_top')
    <!-- header start -->
    <div data-role="header" data-position="fixed" class="d-header">
        <h1>活动查看</h1>
        <a href="" data-rel="back" data-ajax="false" data-iconpos="notext" class="d-back ui-nodisc-icon" data-shadow="false"></a>
    </div>
    <!-- header end -->
@stop

@section('content')
    <!-- content start -->
    <div role="main" class="ui-content">
        <div class="x-activity">
            <img src="{{ asset('wap/run/client/images/a1.png') }}" />
            <div class="x-atvr">
                <p class="at1">XXXXXX活动</p>
                <p>活动内容：每日前100单免费</p>
                <p>活动范围：跑腿服务</p>
                <p>活动日期：2012.05.11 -  2015.06.06</p>
            </div>
        </div>
        <div class="x-activity">
            <img src="{{ asset('wap/run/client/images/a2.png') }}" />
            <div class="x-atvr">
                <p class="at1">XXXXXX活动</p>
                <p>活动内容：每日前100单免费</p>
                <p>活动范围：跑腿服务</p>
                <p>活动日期：2012.05.11 -  2015.06.06</p>
            </div>
        </div>
        <div class="x-activity">
            <img src="{{ asset('wap/run/client/images/a3.png') }}" />
            <div class="x-atvr">
                <p class="at1">XXXXXX活动</p>
                <p>活动内容：每日前100单免费</p>
                <p>活动范围：跑腿服务</p>
                <p>活动日期：2012.05.11 -  2015.06.06</p>
            </div>
        </div>
        <div class="x-activity">
            <img src="{{ asset('wap/run/client/images/a4.png') }}" />
            <div class="x-atvr">
                <p class="at1">XXXXXX活动</p>
                <p>活动内容：每日前100单免费</p>
                <p>活动范围：跑腿服务</p>
                <p>活动日期：2012.05.11 -  2015.06.06</p>
            </div>
        </div>
        <div class="x-activity">
            <img src="{{ asset('wap/run/client/images/a5.png') }}" />
            <div class="x-atvr">
                <p class="at1">XXXXXX活动</p>
                <p>活动内容：每日前100单免费</p>
                <p>活动范围：跑腿服务</p>
                <p>活动日期：2012.05.11 -  2015.06.06</p>
            </div>
        </div>
    </div>
@stop
