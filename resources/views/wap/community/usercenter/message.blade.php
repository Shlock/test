@extends('wap.community._layouts.base')
@section('show_top')
    <div data-role="header" data-position="fixed" class="x-header">
        <h1>系统消息</h1>
        <a href="@if(!empty($nav_back_url)) {{ $nav_back_url }} @else javascript:history.back(-1); @endif" data-iconpos="notext" class="x-back ui-nodisc-icon" data-shadow="false"></a>
    </div>
@stop
@section('content')
    <div role="main" class="" style="background:#fff;">
        <ul class="y-xtxx y-xtxxnew">
            @if(!empty($list))
                @include('wap.community.usercenter.message_item')
            @else
                <div class="x-serno c-green">
                    <img src="{{  asset('wap/community/client/images/ico/cry.png') }}"  />
                    <span>暂时没有消息</span>
                </div>
            @endif
        </ul>

    </div>
@include('wap.community._layouts.swiper')
@stop
@section('js')
    <script src="{{ asset('static/infinite-scroll/jquery.infinitescroll.js') }}"></script>
    <script>
        $(function() {
            $.SwiperInit('.y-xtxxnew', 'li', "{{ u('UserCenter/message',$args) }}");
            $(".y-xtxx").css("min-height",$(window).height()-45);
        })
    </script>

@stop