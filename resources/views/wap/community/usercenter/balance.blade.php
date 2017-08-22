@extends('wap.community._layouts.base')
@section('show_top')
    <div data-role="header" data-position="fixed" class="x-header">
        <h1>我的余额</h1>
        <a href="{{ u('UserCenter/index') }}" data-iconpos="notext" class="x-back ui-nodisc-icon" data-shadow="false"></a>
    </div>
@stop
@section('content')
    <div role="main">
        <div class="y-balance">
            <p class="c-green f12">账户余额（元）</p>
            <p class="tc c-red f18">{{$balance}}</p>
            <a href="{{ u('UserCenter/recharge') }}" class="ui-btn">充值</a>
        </div>
        <p class="y-beizhu c-green">余额交易记录</p>
        <ul class="y-records" id="logs">
            @if($data['paylogs'])
            @include('wap.community.usercenter.balance_item')
            @else
            <p style="line-height: 10px;padding: 10px;">没有交易记录</p>
            @endif
        </ul>
    </div>
@include('wap.community._layouts.swiper')
@stop
@section('js')
    <script src="{{ asset('static/infinite-scroll/jquery.infinitescroll.js') }}"></script>
    <script>
        $(function() {
            $.SwiperInit('#logs', 'li', "{{ u('UserCenter/balance',$args) }}");
            $(".y-records").css("min-height",$(window).height()-247);
        })
    </script>

@stop