@extends('staff.default._layouts.base')
@section('title'){{$title}}@stop
@section('show_top')
    <header class="bar bar-nav">
        <a class="button button-link button-nav pull-left" href="#" onclick="JumpURL('{{u('Seller/index')}}','#seller_index_view',2)"  data-transition='slide-out'>
            <span class="icon iconfont">&#xe64c;</span>
        </a>
        <h1 class="title">{{$title}}</h1>
    </header>
@stop
@section('css')
@stop

@section('contentcss')infinite-scroll-bottom pull-to-refresh-content @stop
@section('distance') data-ptr-distance="20" @stop

@section('content')
    @include('staff.default._layouts.refresh')
    <div class="evaluation-fixed">
        <div class="w_b score_all">
            <div class="left">
                <span>{{$evaluation['score']}}</span>
                <p>总体评价:</p>
            </div>
            <div class="right">
                <div class="start-b down">
                    <i class="icon iconfont">&#xe645;</i>
                    <i class="icon iconfont">&#xe645;</i>
                    <i class="icon iconfont">&#xe645;</i>
                    <i class="icon iconfont">&#xe645;</i>
                    <i class="icon iconfont">&#xe645;</i>
                    <div class="start-b top" style=" width:{{$evaluation['score'] / 5 * 100}}%;">
                        <i class="icon iconfont">&#xe645;</i>
                        <i class="icon iconfont">&#xe645;</i>
                        <i class="icon iconfont">&#xe645;</i>
                        <i class="icon iconfont">&#xe645;</i>
                        <i class="icon iconfont">&#xe645;</i>
                    </div>
                </div>

            </div>
        </div>
        <!--评价块结束-->
        <div class="content-block o2o">
            <div class="buttons-row ">
                <a onclick="JumpURL('{{u('Seller/evaluation',['type' => 1])}}','#seller_evaluation_view_1',2)" href="#"  class="pageloding tab @if($args['type'] == '1')active @endif button ">未回复（<span class="unReply"> {{$evaluation['unReply']}}</span>）</a>
                <a onclick="JumpURL('{{u('Seller/evaluation',['type' => 2])}}','#seller_evaluation_view_2',2)" href="#"  class="pageloding tab @if($args['type'] == '2')active @endif button ">已回复（<span class="reply">{{$evaluation['reply']}}</span>）</a>
            </div>
        </div>
        <!--tab结束-->
    </div>
    @if($evaluation['eva'])
        <div class="tabs evaluation-parent">
            <div id="tab1" class="tab active">
                <ul class="reply_list lists_item_ajax">
                    @include("staff.default.seller.rate_item")
                </ul>
            </div>
        </div>
    @else
        <div class="x-null tc" style="top:40%">
            <i class="icon iconfont">&#xe60c;</i>
            <p>很抱歉，暂无评价</p>
        </div>
    @endif
@stop