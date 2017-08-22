@extends('wap.community._layouts.base')
@section('js')
@stop
@section('show_top')
    <div data-role="header" data-position="fixed" class="x-header">
        <h1>报修记录</h1>
        <a href="@if(!empty($nav_back_url)) {{ $nav_back_url }} @else javascript:history.back(-1); @endif" data-iconpos="notext" class="x-back ui-nodisc-icon" data-shadow="false"></a>
    </div>
@stop
@section('content')
    <div role="main" data-role="content">
        <div class="x-mt-1em"></div>
        <div class="x-bxdetail1">
            <span class="f12">{{ $data['createTime'] }}</span>
            <b class="c-red fr f14">{{ $data['statusStr'] }}</b>
        </div>
        <div class="x-bxdetail">
            <p class="t1">{{$data['repairType']}}</p>
            <p class="f14 mb20">{{$data['content']}}</p>
            @if($data['images'])
            @foreach($data['images'] as $item)
            <img src="{{$item}}">
            @endforeach
            @endif
        </div>
    </div>
@stop