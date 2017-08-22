@extends('wap.community._layouts.base')
@section('show_top')
    @if($user)
    <div class="y-header">
        <h1>我的</h1>
        <a class="y-sjr ui-btn-right" href="{{ u('UserCenter/logout') }}">退出</a>
        <div class="y-wdtx" onclick="$.href('{{u('UserCenter/info') }}')">
            <div class="y-wdtximg"><img src="@if(!empty($user['avatar'])) {{formatImage($user['avatar'],64,64)}} @else {{ asset('wap/community/client/images/wdtt.png') }} @endif"></div>
            <p class="f16">{{$user['name']}}</p>
            <p class="f16">{{$user['mobile']}}</p>
        </div>
    </div>
    @else
    <div class="y-header">
        <h1>我的</h1>
        <div class="y-wdtx">
            <div class="y-wdtximg"><img src="{{ asset('wap/community/client/images/wdtx-wzc.png') }}"></div>
            <p>
                <a href="{{ u('User/reg') }}">注册</a><a href="{{ u('User/login') }}">登录</a>
            </p>
        </div>
    </div>
    @endif
@stop

@section('content')
    <div role="main" class="y-margintop">
        <div class="y-wdlst">
            <ul data-role="listview" class="y-wdlsts">
                <li data-icon="false"><a href="{{ u('UserCenter/message') }}" data-ajax="false"><img src="{{ asset('wap/community/client/images/ico/sz5.png') }}" /><span>系统消息@if($user)({{$counts['newMsgCount']}})@endif</span><i class="x-rightico"></i></a></li>
                <li data-icon="false"><a href="{{ u('UserCenter/collect') }}" data-ajax="false"><img src="{{ asset('wap/community/client/images/ico/sz6.png') }}" /><span>我的收藏@if($user)({{$counts['collectCount']}})@endif</span><i class="x-rightico"></i></a></li>
                <li data-icon="false"><a href="{{ u('UserCenter/address') }}" data-ajax="false"><img src="{{ asset('wap/community/client/images/ico/sz7.png') }}" /><span>地址管理@if($user)({{$counts['addressCount']}})@endif</span><i class="x-rightico"></i><!--<i class="yy"></i>--></a>
                <li data-icon="false"><a href="{{ u('UserCenter/config') }}" data-ajax="false"><img src="{{ asset('wap/community/client/images/ico/sz8.png') }}" /><span>设置</span><i class="x-rightico"></i></a></li>
                @if(empty($seller))
                <li data-icon="false"><a href="@if($user){{ u('Seller/reg') }}@else {{u('User/login')}} @endif" data-ajax="false"><img src="{{ asset('wap/community/client/images/ico/sz9.png') }}" /><span>我要开店</span><i class="x-rightico"></i></a></li>
                @endif
            </ul>
        </div>
        <div class="y-khrx">
            <p class="f14 c-green">客户服务热线：<a href="tel:{{$site_config['wap_service_tel']}}" style="text-decoration:underline; color:#38c;">{{$site_config['wap_service_tel']}}</a></p>
            <p class="f14 c-green">服务时间：{{$site_config['wap_service_time']}}</p>
        </div>
    </div>
@include('wap.community._layouts.bottom')
@stop

