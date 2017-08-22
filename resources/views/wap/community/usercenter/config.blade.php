@extends('wap.community._layouts.base')
@section('show_top')
    <div data-role="header" data-position="fixed" class="x-header">
        <h1>设置</h1>
        <a href="@if(!empty($nav_back_url)) {{ $nav_back_url }} @else javascript:history.back(-1); @endif" data-iconpos="notext" class="x-back ui-nodisc-icon" data-shadow="false"></a>
    </div>
@stop
@section('content')
    <div role="main">
        <div class="y-wdlst" style="border-top:0; margin-top:0">
            <ul data-role="listview" class="y-wdlsts">
                <li data-icon="false"><a href="{{ u('UserCenter/feedback') }}"><img src="{{ asset('wap/community/client/images/ico/sz1.png') }}" /><span>意见反馈</span><i class="x-rightico"></i></a></li>
                <li data-icon="false"><a href="{{ u('UserCenter/userhelp') }}"><img src="{{ asset('wap/community/client/images/ico/sz2.png') }}" /><span>新手帮助</span><i class="x-rightico"></i></a></li>
                <li data-icon="false"><a href="{{ u('UserCenter/aboutus') }}"><img src="{{ asset('wap/community/client/images/ico/sz3.png') }}" /><span>关于我们</span><i class="x-rightico"></i></a></li>
            </ul>
        </div>
        <div class="y-end y-endbgc">
            <a href="{{ u('UserCenter/logout') }}" class="ui-btn">退出登录</a>
        </div>
    </div>
@stop
