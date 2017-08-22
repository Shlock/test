@extends('wap.community._layouts.base')

@section('show_top')
    <div data-role="header" data-position="fixed" class="x-header">
        <h1>我的小区</h1>
        <a href="@if(!empty($nav_back_url)) {!! $nav_back_url !!} @else {{u('UserCenter/index')}} @endif" data-iconpos="notext" class="x-back ui-nodisc-icon" data-shadow="false"></a>
        <a class="x-sjr ui-btn-right" href="{{u('District/add')}}">添加</a>
    </div>
@stop
@section('js') 
    <script src="{{ asset('static/infinite-scroll/jquery.infinitescroll.js') }}"></script> 
@stop 
@section('content')
    <div role="main">
        <ul class="y-wdxq">
            @foreach($list as $item)
            <li>
                <div class="y-addrtop" onclick="window.location.href='{!! u('District/detail', ['districtId'=>$item['id']]) !!}'">
                    <p class="f14 mb5">{{$item['name']}}<i class="x-rightico fr"></i></p>
                    <span class="f12 c-green"><img src="{{ asset('wap/community/client/images/ico/ico2.png') }}" width="12">{{$item['address']}}</span>
                </div>
                @if($item['isEnter'])
                <div class="y-addrbtm clearfix">
                    <a href="{{ u('Property/index', ['districtId'=>$item['id']])}}" class="ui-btn fr">物业</a>
                </div>
                @endif
            </li>
            @endforeach
        </ul>
    </div>
@include('wap.community._layouts.swiper')
@stop
@section('js')

@stop