@extends('wap.community._layouts.base')
@section('show_top')
    <div data-role="header" data-position="fixed" class="x-header">
        <h1>我的消息</h1>
        <a href="@if(!empty($nav_back_url)) {{ $nav_back_url }} @else javascript:history.back(-1); @endif" data-iconpos="notext" class="x-back ui-nodisc-icon" data-shadow="false"></a>
    </div>
@stop

@section('content')
    <div role="main" class="ui-content">
        @include("wap.community.usercenter.msgshow_item")
    </div>
    @include('wap.community._layouts.swiper')
@stop
@section('js')
    <script src="{{ asset('static/infinite-scroll/jquery.infinitescroll.js') }}"></script>
    <script>
        $(function() {
            $.SwiperInit('.ui-content', '.msg-list', "{{ u('UserCenter/msgshow',$args) }}");
        })
    </script>

@stop