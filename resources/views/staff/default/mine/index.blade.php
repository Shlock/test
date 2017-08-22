@extends('staff.default._layouts.base')
@section('title'){{$title}}@stop
@section('show_top')@stop
@section('show_refresh')@stop
@section('contentcss')hasbottom @stop
@section('content')
    <div class="pcenter-head">
        <div class="headimg"><div><img src="{{$staff['avatar']}}"></div></div>
        <a class="gotocenter" href="#" onclick="JumpURL('{{u('Mine/account')}}','#mine_account_view',2)">{{$staff['name']}}<i class="iconfont">&#xe64b;</i></a>
    </div>
    <div class="blank050"></div>
    @if($role == 2)
    {{--<div class="item">--}}
        {{--<a href="">--}}
            {{--<i class="iconfont left bjff9667">&#xe669;</i>--}}
            {{--<i class="iconfont right">&#xe64b;</i>--}}
            {{--<div class="con">--}}
                {{--我的佣金--}}
            {{--</div>--}}
        {{--</a>--}}
        {{--<a href="">--}}
            {{--<i class="iconfont left bjff7e7e">&#xe66a;</i>--}}
            {{--<i class="iconfont right">&#xe64b;</i>--}}
            {{--<div class="con">--}}
                {{--业务统计--}}
            {{--</div>--}}
        {{--</a>--}}
        {{--<a href="">--}}
            {{--<i class="iconfont left bj52cfe1">&#xe66b;</i>--}}
            {{--<i class="iconfont right">&#xe64b;</i>--}}
            {{--<div class="con">--}}
                {{--请假--}}
            {{--</div>--}}
        {{--</a>--}}
    {{--</div>--}}
    {{--<div class="blank050"></div>--}}
    @endif
    <div class="item">
        <a href="#" onclick="JumpURL('{{u('Mine/message')}}','#mine_message_view',2)" class=" @if($hasNewMsg) newstip @endif" href="">
            <i class="iconfont left bj7cbbfe">&#xe666;</i>
            <i class="iconfont right">&#xe64b;</i>
            <div class="con">
                消息通知
                <span class="memo"></span>
            </div>
        </a>
        <a  href="#" onclick="JumpURL('{{u('More/detailAll',['code'=>7])}}','#more_detailAll_view',2)">
            <i class="iconfont left bj87ce4c">&#xe667;</i>
            <i class="iconfont right">&#xe64b;</i>
            <div class="con">
                使用帮助
                <span class="memo"></span>
            </div>
        </a>
    </div>
    <div class="blank050"></div>
    <div class="item">
        <a onclick="JumpURL('{{u('Mine/set')}}','#mine_set_view',2)" >
            <i class="iconfont left bjffa70f">&#xe668;</i>
            <i class="iconfont right">&#xe64b;</i>
            <div class="con">
                设置
            </div>
        </a>
    </div>
@stop
@section('preloader')@stop