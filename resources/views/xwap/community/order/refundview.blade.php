@extends('xwap.community._layouts.base')

@section('show_top')
    <header class="bar bar-nav">
        <a class="button button-link button-nav pull-left pageloading back" href="@if(!empty($nav_back_url)) {{ $nav_back_url }} @else javascript:$.back(); @endif" data-transition='slide-out'>
            <span class="icon iconfont">&#xe600;</span>
        </a>
        <h1 class="title f16">退款详情</h1>
    </header>
@stop

@section('content')
    <div class="content" id=''>
        <ul class="y-orderlst f14 y-refund c-gray">
            <li>退款金额：<strong class="c-red">￥{{$data['money']}}</strong></li>
            <li>退款时间：{{$data['time']}}</li>
            <li>退回账户：{{$data['payment']}}</li>
            <li>退款状态：{{$data['status']}}</li>
        </ul>
        <div class="y-refunddel">
            <div class="y-refundtit f12 c-gray">退款进度详情</div>
            <ul class="y-tkck">
                <li @if($data['stepOne']['status'] == 1)class="on"@endif>
                    <i></i>
                    <div class="y-tkxql"></div>
                    <p class="f14 mb5 @if($data['stepOne']['status'] == 1) c-red @endif">{{$data['stepOne']['name']}}</p>
                    <p class="c-gray f12">{{$data['stepOne']['brief']}}</p>
                    <p class="c-gray f12">{{$data['stepOne']['time']}}</p>
                </li>
                <li @if($data['stepTwo']['status'] == 1)class="on"@endif>
                    <i></i>
                    <div class="y-tkxql"></div>
                    <p class="f14 mb5 @if($data['stepTwo']['status'] == 1) c-red @endif">{{$data['stepTwo']['name']}}</p>
                    <p class="c-gray f12">{{$data['stepTwo']['brief']}}</p>
                    <p class="c-gray f12">{{$data['stepTwo']['time']}}</p>
                </li>
                <li @if($data['stepThree']['status'] == 1)class="on"@endif>
                    <i></i>
                    <div class="y-tkxql"></div>
                    <p class="f14 mb5 c-red">{{$data['stepThree']['name']}}</p>
                    <p class="c-gray f12">{{$data['stepThree']['brief']}}</p>
                    <p class="c-gray f12">{{$data['stepThree']['time']}}</p>
                </li>
            </ul>
        </div>
    </div>
@stop