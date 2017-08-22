@extends('staff.default._layouts.base')
@section('title'){{$title}}@stop
@section('show_top')
    <header class="bar bar-nav">
        <a class="button button-link button-nav pull-left" href="#" onclick="JumpURL('{{u('Mine/index')}}','#mine_index_view',2)" data-transition='slide-out'>
            <span class="icon iconfont">&#xe64c;</span>
        </a>
        <h1 class="title">{{$title}}</h1>
    </header>
@stop
@section('contentcss')bcf infinite-scroll infinite-scroll-bottom pull-to-refresh-content @stop
@section('distance')data-distance="20"  data-ptr-distance="20"@stop
@section('content')
    @include('staff.default._layouts.refresh')
    @if($list)
        <!-- 下面是正文 -->
        <div class="message">
            <ul class="message-list lists_item_ajax">
                @include('staff.default.mine.message_item')
            </ul>
        </div>
    @else
        <div class="x-null tc"  style="top:60%">
            <i class="icon iconfont">&#xe60c;</i>
            <p>很抱歉，消息通知</p>
        </div>
    @endif
@stop