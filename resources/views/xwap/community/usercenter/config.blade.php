@extends('xwap.community._layouts.base')
@section('show_top')
    <header class="bar bar-nav">
        <a class="button button-link button-nav pull-left pageloading" href="@if(!empty($nav_back_url)) {{ $nav_back_url }} @else javascript:$.back(); @endif" data-transition='slide-out'>
            <span class="icon iconfont">&#xe600;</span>
        </a>
        <h1 class="title f16">设置</h1>
    </header>
@stop
@section('content')
    <div class="content" id=''>
            <div class="list-block media-list">
                <ul class="y-wd y-sz">
                    <!--li>
                        <a href="#" class="item-link item-content">
                            <div class="item-media icon iconfont y-wdicon7">&#xe637;</div>
                            <div class="item-inner">
                                <div class="item-title-row">
                                    <div class="item-title f16">接收系统消息</div>
                                    <label class="label-switch x-sliderbtn">
                                        <input type="checkbox" checked="true">
                                        <div class="checkbox"></div>
                                    </label>
                                </div>
                            </div>
                        </a>
                    </li -->
                    <li>
                        <a href="{{ u('UserCenter/feedback') }}" class="item-link item-content pageloading">
                            <div class="item-media icon iconfont y-wdicon8">&#xe642;</div>
                            <div class="item-inner">
                                <div class="item-title-row">
                                    <div class="item-title f16">意见反馈</div>
                                    <div class="fr">
                                        <i class="icon iconfont c-gray2">&#xe602;</i>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </li>
                    <li>
                        <a href="{{ u('UserCenter/userhelp') }}" class="item-link item-content pageloading">
                            <div class="item-media icon iconfont y-wdicon9">&#xe64e;</div>
                            <div class="item-inner">
                                <div class="item-title-row">
                                    <div class="item-title f16">新手帮助</div>
                                    <div class="fr">
                                        <i class="icon iconfont c-gray2">&#xe602;</i>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </li>
                    <li>
                        <a href="{{ u('UserCenter/aboutus') }}" class="item-link item-content pageloading">
                            <div class="item-media icon iconfont y-wdicon10">&#xe62f;</div>
                            <div class="item-inner">
                                <div class="item-title-row">
                                    <div class="item-title f16">关于我们</div>
                                    <div class="fr">
                                        <i class="icon iconfont c-gray2">&#xe602;</i>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </li>
                    <!--li>
                        <a href="javascript:$.versions()" class="item-link item-content">
                            <div class="item-media icon iconfont y-wdicon11">&#xe65b;</div>
                            <div class="item-inner">
                                <div class="item-title-row">
                                    <div class="item-title f16">版本检测</div>
                                    <div class="fr">
                                        <span class="c-gray f12">当前版本V1.0</span>
                                        <i class="icon iconfont c-gray2">&#xe602;</i>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </li -->
                </ul>
            </div>
            <p class="y-bgfff y-szbtn"><a class="y-paybtn f16" href="{{ u('UserCenter/logout') }}">退出登录</a></p>
            <div id="sb">
                
            </div>
        </div>
            
@stop


@section($js)
<script type="text/javascript">
    $(function(){
        //APP版本检测
        // arg1 > arg2, return 1;      // 当前版本大于最新版本（部分测试版）
        // arg1 == arg2, return 0;     // 当前版本等于最新版本（无更新）
        // arg1 < arg2, return -1;     // 当前版本小于最新版本（有更新）
        // $.alert( $.compareVersion('$arg1', '$arg2') );
        var v = $.compareVersion('1.0', '1.0')
        $.versions = function(){
            if( v == -1){
                $.alert("有新版本是否确认更新", "新版检测",function(){
                    //更新操作
                });
            }else{
                $.alert("暂无新版本！");
            }
        }
        
        //设备信息
        var html =  "(安卓系统)android: " + $.device.android + "<br/>" +
                    "(谷歌浏览器)androidChrome: " + $.device.androidChrome + "<br/>" +
                    "(ios)ios: " + $.device.ios + "<br/>" +
                    "(ipad)ipad: " + $.device.ipad + "<br/>" +
                    "(iphone)iphone: " +  $.device.iphone + "<br/>" +
                    "(微信浏览器)isWeixin: " + $.device.isWeixin + "<br/>" +
                    "(系统)os: " + $.device.os + "<br/>" +
                    "(系统版本)osVersion: " + $.device.osVersion + "<br/>" +
                    "pixelRatio: " + $.device.pixelRatio + "<br/>" +
                    "statusBar: " + $.device.statusBar + "<br/>" +
                    "webView: " + $.device.webView;
        // $("#sb").html(html);
    })
</script>
@stop