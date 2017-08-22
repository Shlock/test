@extends('wap.community._layouts.base')

@section('show_top')
    <div data-role="header" data-position="fixed" class="x-header">
        <h1>物业</h1>
        <a href="@if(!empty($nav_back_url)) {!! $nav_back_url !!} @else {{ u('Forum/index')}} @endif" data-iconpos="notext" class="x-back ui-nodisc-icon" data-shadow="false"></a>
        @if($data['countDistrict'] > 1)
        <a class="x-sjr ui-btn-right" href="{{ u('District/index')}}">切换</a>
        @endif
    </div>
@stop

@section('content')
    <div role="main" class="ui-content">
        @if(!$data)
        <div class="x-null">
            <img src="{{  asset('wap/community/client/images/ico/error.png') }}" >
            <p class="f12 c-green mt10">您需要先选择小区才可以申请物业</p>
            <a class="ui-btn redbtn" href="{{ u('District/index')}}">马上去选择</a>
        </div>
        @else
            @if($data[isProperty])
            <div class="x-null">
                <img src="{{  asset('wap/community/client/images/ico/error.png') }}" >
                <p class="f12 c-green mt10">很抱歉，{{$data['district']['name']}}未开通物业版块</p>
                <a class="ui-btn redbtn" href="{{ u('District/index')}}">重新选择小区</a>
            </div>
            @endif
            @if($data['isVerify'])
            <div class="x-null">
                <img src="{{  asset('wap/community/client/images/ico/error.png') }}" >
                <p class="f12 c-green mt10">您未进行身份验证</p>
                <a class="ui-btn redbtn" href="{{ u('District/userapply',['districtId'=>$data['districtId']])}}">去验证</a>
            </div>
            @endif
            @if($data['isCheck'])
            <div class="x-null">
                <img src="{{  asset('wap/community/client/images/ico/error.png') }}" >
                <p class="f12 c-green mt10">您的身份信息已提交审核，请耐心等待</p>
                <a class="ui-btn redbtn" href="{{ u('Index/index')}}">去首页逛逛</a>
            </div>
            @endif
            @if(!$data[isProperty] && !$data['isVerify'] && !$data['isCheck'])
            <div class="y-wytop">
                <div class="y-wylogo fl"><img src="@if(!empty($user['avatar'])) {{formatImage($user['avatar'],64,64)}} @else {{  asset('wap/community/client/images/wdtx-wzc.png') }} @endif"></div>
                <div class="y-wymsg" onclick="window.location.href='{!! u('Property/detail', ['id'=>$data['id'],'districtId'=>$data['districtId']]) !!}'">
                    <p>业主：<span>{{$data['name']}}</span></p>
                    <p>单元：<span>{{$data['build']['name']}}#{{$data['room']['roomNum']}}</span></p>
                    <p>电话：<span>{{$data['mobile']}}</span></p>
                    <i class="x-rightico"></i>
                </div>
            </div>
            <ul class="y-wy">
                <li><a href="{{u('Property/article', ['districtId'=>$data['districtId']])}}"><img src="{{  asset('wap/community/client/images/wyimg1.png') }}"><p>社区公告</p></a></li>
                <!-- <li><a href="javascript:;"><img src="{{  asset('wap/community/client/images/wyimg2.png') }}"><p>物业缴费</p></a></li>
                <li><a href="javascript:;"><img src="{{  asset('wap/community/client/images/wyimg3.png') }}"><p>水电缴费</p></a></li> -->
                <li><a href="{{u('Repair/index', ['districtId'=>$data['districtId']])}}"><img src="{{  asset('wap/community/client/images/wyimg4.png') }}"><p>故障报修</p></a></li>
                <li><a href="javascript:;" @if($data['accessStatus'] != 1) class="dooraccess" @endif><img src="{{  asset('wap/community/client/images/wyimg6.png') }}"><p>门禁</p></a></li>
                <li><a href="{{u('Property/brief', ['districtId'=>$data['districtId']])}}"><img src="{{  asset('wap/community/client/images/wyimg5.png') }}"><p>物业介绍</p></a></li>
            </ul>
            @endif
        @endif
    </div>
@stop
@section('js')
<script type="text/javascript">
$(function() {
    var districtId = "{{$data['id']}}";
    $(document).on("touchend",".dooraccess",function(){
        $.showOperation('您暂未开通手机智能开锁功能。点击确定申请开通门禁',"javascript:$.doorAccess(" + districtId + ");",'申请门禁');
    })
   
    $.doorAccess = function () {
       // window.location.reload();
       $.post("{{u('Property/applyaccess')}}",{'districtId':districtId},function(result){
            if(result.code == 0){
                window.location.href="{{ u('Property/index')}}";
            } else {
                $.showError(result.msg);
            }
        },'json');
    }

    $(".x-null").css("padding-top",$(window).height()*0.2);
})
</script>
@stop