@extends('wap.community._layouts.base_order')
@section('show_top')
    <div data-role="header" data-position="fixed" class="x-header">
        <h1>退款详情</h1>
        <a href="@if(!empty($nav_back_url)) {{ $nav_back_url }} @else javascript:history.back(-1); @endif" data-iconpos="notext" class="x-back ui-nodisc-icon" data-shadow="false"></a>
    </div>
@stop
@section('content')
    <div role="main" class="ui-content">
        <ul class="y-orderlst f14 x-refund">
            <li>退款金额：<strong class="c-red">￥{{$data['money']}}</strong></li>
            <li>退款时间：{{$data['time']}}</li>
            <li>退回账户：{{$data['payment']}}</li>
            <li>退款状态：{{$data['status']}}</li>
        </ul>
        <div class="x-refunddel">
            <div class="x-refundtit f12 c-green">退款进度详情</div>
            <ul class="x-tkck">
                <li @if($data['stepOne']['status'] == 1)class="on"@endif>
                    <i></i>
                    <div class="x-tkxql"></div>
                    <p class="f14 mb5 @if($data['stepOne']['status'] == 1) c-red @endif">{{$data['stepOne']['name']}}</p>
                    <p class="c-green f12">{{$data['stepOne']['brief']}}</p>
                    <p class="c-green f12">{{$data['stepOne']['time']}}</p>
                </li>
                <li @if($data['stepTwo']['status'] == 1)class="on"@endif>
                    <i></i>
                    <div class="x-tkxql"></div>
                    <p class="f14 mb5 @if($data['stepTwo']['status'] == 1) c-red @endif">{{$data['stepTwo']['name']}}</p>
                    <p class="c-green f12">{{$data['stepTwo']['brief']}}</p>
                    <p class="c-green f12">{{$data['stepTwo']['time']}}</p>
                </li>
                <li @if($data['stepThree']['status'] == 1)class="on"@endif>
                    <i></i>
                    <div class="x-tkxql"></div>
                    <p class="f14 mb5 @if($data['stepThree']['status'] == 1) c-red @endif">{{$data['stepThree']['name']}}</p>
                    <p class="c-green f12">{{$data['stepThree']['brief']}}</p>
                    <p class="c-green f12">{{$data['stepThree']['time']}}</p>
                </li>
            </ul>
        </div>

    </div>
@stop