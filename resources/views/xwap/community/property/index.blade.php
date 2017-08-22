@extends('xwap.community._layouts.base')

@section('show_top')
    <header class="bar bar-nav">
        <a class="button button-link button-nav pull-left pageloading back" href="#" data-transition='slide-out'>
            <span class="icon iconfont">&#xe600;</span>
        </a>
        <h1 class="title f16">物业</h1>
        @if($data['countDistrict'] > 1)
            <a class="button button-link button-nav pull-right open-popup toedit pageloading changeTo" href="#" data-popup=".popup-about">切换</a>
        @endif
    </header>
@stop

@section('content')
    <div class="content" id=''>
        <!-- 未开通物业提示 -->
        @if(!$data)
            <div class="x-null pa w100 tc">
                <i class="icon iconfont">&#xe645;</i>
                <p class="f12 c-gray mt10">您需要先选择小区才可以申请物业</p>
                <a class="f14 c-white x-btn db pageloading" href="{{ u('District/index')}}">马上去选择</a>
            </div>
        @else
            @if($data[isProperty])
                <div class="x-null pa w100 tc">
                    <i class="icon iconfont">&#xe645;</i>
                    <p class="f12 c-gray mt10">很抱歉，{{$data['district']['name']}}未开通物业版块</p>
                    <a class="f14 c-white x-btn db pageloading" href="{{ u('District/index')}}">重新选择小区</a>
                </div>
            @endif

            @if($data['isVerify'])
                <div class="x-null pa w100 tc">
                    <i class="icon iconfont">&#xe645;</i>
                    <p class="f12 c-gray mt10">您未进行身份验证</p>
                    <a class="f14 c-white x-btn db pageloading" href="{{ u('District/userapply',['districtId'=>$data['districtId']])}}">去验证</a>
                </div>
            @endif

            @if($data['isCheck'])
                <div class="x-null pa w100 tc">
                    <i class="icon iconfont">&#xe645;</i>
                    <p class="f12 c-gray mt10">您的身份信息已提交审核，请耐心等待</p>
                    <a class="f14 c-white x-btn db pageloading" href="{{ u('Index/index')}}">去首页逛逛</a>
                </div>
            @endif

            @if(!$data[isProperty] && !$data['isVerify'] && !$data['isCheck'])
                <!-- 业主信息 -->
                <div class="list-block media-list x-property bfh0 mb0">
                    <ul>
                        <li>
                            <a href="#" class="item-link item-content">
                                <div class="item-media">
                                    <img src="@if(!empty($user['avatar'])) {{formatImage($user['avatar'],64,64)}} @else {{  asset('wap/community/client/images/wdtx-wzc.png') }} @endif">
                                </div>
                                <div class="item-inner"  onclick="$.href('{!! u('Property/detail', ['id'=>$data['id'],'districtId'=>$data['districtId']]) !!}')">
                                    <div class="item-title-row">
                                        <div class="item-title c-gray f14">业主：<span class="c-white">{{$data['name']}}</span></div>
                                    </div>
                                    <div class="item-subtitle c-gray f14">单元：<span class="c-white">{{$data['build']['name']}}#{{$data['room']['roomNum']}}</span>
                                        <i class="icon iconfont fr">&#xe602;</i>
                                    </div>
                                    <div class="item-text c-gray ha f14">电话：<span class="c-white">{{$data['mobile']}}</span></div>
                                </div>
                            </a>
                        </li>
                    </ul>
                </div>
                <ul class="x-lifepay tc f12">
                    <li onclick="$.href('{{u('Property/article', ['districtId'=>$data['districtId']])}}')">
                        <img src="{{  asset('wap/community/client/images/wyimg1.png') }}">
                        <p>社区公告</p>
                    </li>
                    <li>
                        <img src="{{  asset('wap/community/client/images/wyimg2.png') }}">
                        <p>物业缴费</p>
                    </li>
                    <li>
                        <img src="{{  asset('wap/community/client/images/wyimg3.png') }}">
                        <p>生活缴费</p>
                    </li>
                    <li onclick="$.href('{{u('Repair/index', ['districtId'=>$data['districtId']])}}')">
                        <img src="{{  asset('wap/community/client/images/wyimg4.png') }}">
                        <p>故障报修</p>
                    </li>
                    <li @if($data['accessStatus'] != 1) class="dooraccess" @endif>
                        <img src="{{ asset('wap/community/client/images/wyimg6.png') }}">
                        <p>门禁</p>
                    </li>
                    <li onclick="$.href('{{u('Property/brief', ['districtId'=>$data['districtId']])}}')">
                        <img src="{{  asset('wap/community/client/images/wyimg5.png') }}">
                        <p>物业介绍</p>
                    </li>
                </ul>
            @endif
        @endif
    </div>
@stop

@section($js)
<script type="text/javascript">
$(function() {
    var districtId = "{{$data['id']}}";
    $(document).on("touchend",".dooraccess",function(){
        $.confirm('您暂未开通手机智能开锁功能。点击确定申请开通门禁', '申请门禁', function () {
            $.doorAccess(districtId);
        });
    })
   
    $.doorAccess = function () {
       $.post("{{u('Property/applyaccess')}}",{'districtId':districtId},function(result){
            if(result.code == 0){
                $.router.load("{{ u('Property/index')}}", true);
            } else {
                $.alert(result.msg);
            }
        },'json');
    }

    //切换
    $(document).on("touchend",".changeTo",function(){
        $.router.load("{{ u('District/index')}}", true);
    })
})
</script>
@stop