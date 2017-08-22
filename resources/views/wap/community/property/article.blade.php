@extends('wap.community._layouts.base')

@section('show_top')
    <div data-role="header" data-position="fixed" class="x-header">
        <h1>社区公告</h1>
        <a href="@if(!empty($nav_back_url)) {!! $nav_back_url !!} @else {{ u('Property/index',['districtId'=>$args['districtId']])}} @endif" data-iconpos="notext" class="x-back ui-nodisc-icon" data-shadow="false"></a>
    </div>
@stop

@section('content')
    <div role="main" data-role="content">
        <div class="x-mt-1em"></div>
        @if($list)
        <ul class="x-bgfff2">
            @foreach($list as $v)
            <li class="x-bxdetail1" onclick="window.location.href='{!! u('Property/articledetail', ['id'=>$v['id']]) !!}'">
                <p class="f14 x-sqnoticett"><span class="@if(!$v['readTime']) on @endif c-bg"></span>{{$v['title']}}</p>
                <p class="f12 c-green x-sqnoticem mt5">{!! mb_substr($v['content'], 0, 20) !!}......</p>
                <p class="f12 c-green mt10">{{ $v['createTime'] }}<span class="fr">点击查看<i class="x-rightico ml5 x-sqnotices"></i></span></p>
            </li>
            @endforeach
        </ul>
        @else
            <div class="x-serno c-green">
                <span>暂时没有公告</span>
            </div>
        @endif
    </div>
@stop
@section('js')

@stop