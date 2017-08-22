@extends('xwap.community._layouts.base')

@section('show_top')
    <header class="bar bar-nav">
        <a class="button button-link button-nav pull-left pageloading" href="{{u("UserCenter/index")}}" data-transition='slide-out'>
            <span class="icon iconfont">&#xe600;</span>
        </a>
        <h1 class="title f16">我的小区</h1>
        <a class="button button-link button-nav pull-right open-popup pageloading" data-popup=".popup-about" href="{{u('District/add')}}">添加</a>
    </header>
@stop


@section('content')
    <div class="content" id=''>
        @foreach($list as $item)
        <div class="card m0 mt10 x-mycom">
            <div class="card-content"  onclick="$.href('{!! u('District/detail', ['districtId'=>$item['id']]) !!}')">
                <div class="card-content-inner">
                    <p>
                        <span class="c-black">{{$item['name']}}</span>
                        <i class="icon iconfont c-gray fr">&#xe602;</i>
                    </p>
                    <p class="mt5">
                        <i class="icon iconfont c-gray f18">&#xe60d;</i>
                        <span class="f12 c-gray">{{$item['address']}}</span>
                    </p>
                </div>
            </div>
            @if($item['isEnter'])
                <div class="card-footer">
                    <div class="tr w100">
                        <a href="{{ u('Property/index', ['districtId'=>$item['id']])}}" class="x-btn">物业</a>
                    </div>
                </div>
            @endif
        </div>
        @endforeach
    </div>

    <!-- @include('wap.community._layouts.swiper') -->
@stop

@section($js) 
    <!-- <script src="{{ asset('static/infinite-scroll/jquery.infinitescroll.js') }}"></script> -->
@stop 