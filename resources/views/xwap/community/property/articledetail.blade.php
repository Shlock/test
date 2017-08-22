@extends('xwap.community._layouts.base')

@section('css')
<style type="text/css">
    .x-titdel{word-break:keep-all; white-space:nowrap; overflow: hidden; text-overflow:ellipsis;}
</style>
@stop

@section('show_top')
    <header class="bar bar-nav">
        <a class="button button-link button-nav pull-left back" href="#" data-transition='slide-out'>
            <span class="icon iconfont">&#xe600;</span>
        </a>
        <h1 class="title f16">社区公告详情</h1>
    </header>
@stop

@section('content')
    <div class="content c-bgfff" id=''>
        <p class="tc p10 x-titdel f16"><b>{{$data['title']}}</b></p>
        <p class="tc f12 c-gray">{{ yzday($data['createTime']) }}</p>
        <div class="f14 mt5 pl10 pr10">
            <p class="tt">{!! $data['content'] !!}</p>
        </div>
    </div>
@stop

@section($js)
@stop
