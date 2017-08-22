@extends('wap.community._layouts.base')

@section('css')
    <style type="text/css">

    </style>
@stop
@section('js')
    <script src="{{ asset('static/infinite-scroll/jquery.infinitescroll.js') }}"></script>
@stop

@section('show_top')
    <div data-role="header" data-tap-toggle="false" data-position="fixed" class="x-header">
        <h1>评价</h1>
        <a href="javascript:history.back(-1);" data-iconpos="notext" class="x-back ui-nodisc-icon" data-shadow="false"></a>
        {{--<span class="x-sjr ui-btn-right"><i class="x-sjsc collect_it @if($data['iscollect']) on @endif" data-id="{{$data['id']}}"></i></span>--}}
    </div>
@stop

@section('content')
    <div role="main" class="ui-content x-sorta" style="padding:1em 0 0;">
        @include('wap.community.goods.sellergoodshead')
        @section('notice')@stop
        <div class="x-pjtotal">
            <div class="x-pjtl">
                <p class="c-red">{{ $count['star'] }}</p>
                <p class="f14">总体评价</p>
            </div>
            <div class="star-rank">
                <!-- 五颗星总长85px，此时星级的长度用百分比控制 -->
                <div class="star-score" style="width:{{$count['star'] * 20}}%;"></div>
            </div>
        </div>

        <div data-role="tabs" id="tabs" data-corners="false" class="x-pjtab">
            <div data-role="navbar">
                <ul>
                    <li onclick="location.href='{{ u('Goods/comment',['id'=>$args['sellerId']]) }}'">
                        <a href="#one" data-ajax="false" @if($args['type'] == 0) class="ui-btn-active" @endif>全部({{$count['totalCount']}})</a>
                    </li>
                    <li onclick="location.href='{{ u('Goods/comment',['id'=>$args['sellerId'],'type'=>1]) }}'">
                        <a href="#two" data-ajax="false" @if($args['type'] == 1) class="ui-btn-active" @endif>好评({{$count['goodCount']}})</a>
                    </li>
                    <li onclick="location.href='{{ u('Goods/comment',['id'=>$args['sellerId'],'type'=>2]) }}'">
                        <a href="#three" data-ajax="false" @if($args['type'] == 2) class="ui-btn-active" @endif>中评({{$count['neutralCount']}})</a>
                    </li>
                    <li onclick="location.href='{{ u('Goods/comment',['id'=>$args['sellerId'],'type'=>3]) }}'">
                        <a href="#four" data-ajax="false" @if($args['type'] == 3) class="ui-btn-active" @endif>差评({{$count['badCount']}})</a>
                    </li>
                </ul>
            </div>
            <div class="baisbg" style="height: 100%;background-color:#fff;">
                <div class="x-pjlstct">
                    <ul id="clist">
                        @include('wap.community.goods.comment_item')
                    </ul>
                </div>
            </div>
        </div>
    </div>
    @include('wap.community._layouts.swiper')
    <script type="text/javascript">
        $(function() {
            $(".x-pjtab").css("min-height",$(window).height()-135);
            $.SwiperInit('#clist', 'li', "{!! u('Goods/comment',$args) !!}");
        })
    </script>
@stop 
 