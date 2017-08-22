@extends('xwap.community._layouts.base')

@section('css')
@stop

@section('show_top')
@stop

@section('content')
    @include('xwap.community._layouts.bottom')
    <!-- new  -->
    <div class="content y-wdcontent" id=''>
        <header class="bar bar-nav y-bar">
            @if($user)
                <a class="button button-link button-nav pull-left" href="{{ u('UserCenter/config') }}">
                    <span class="icon iconfont">&#xe64a;</span>
                </a>
                <a class="button button-link button-nav pull-right" href="{{ u('UserCenter/message') }}">
                    <i class="icon iconfont">&#xe660;@if($user && $counts['newMsgCount'] > 0)<span class="y-xxcont">{{$counts['newMsgCount']}} </span>@endif</i>
                </a>
                <h1 class="title f16">我的</h1>
                <div class="y-wdtx"  onclick="$.href('{{ u('UserCenter/info') }}')">
                    <div class="y-wdtximg">
                        <img src="@if(!empty($user['avatar'])) {{formatImage($user['avatar'],64,64)}} @else {{ asset('wap/community/client/images/wdtt.png') }} @endif">
                    </div>
                    <p class="f16">{{$user['name']}}</p>
                </div>
                <ul class="y-balancetop clearfix">
                    <li onclick="$.href('{{ u('UserCenter/balance') }}')">
                        <span class="f16">{{$balance or '0.00'}}元</span>
                        <p class="f14">我的余额</p>
                    </li>
                    <li class="pageloading" onclick="$.href('{{ u('Coupon/index') }}')">
                        <span class="f16">{{$counts['proCount'] or '0'}}</span>
                        <p class="f14">优惠券</p>
                    </li>
                </ul>
            @else
                <h1 class="title f16">我的</h1>
                <div class="y-wdtx">
                    <div class="y-wdtximg"><img src="{{ asset('wap/community/client/images/wdtx-wzc.png') }}"></div>
                    <p class="f16">
                        <a href="{{ u('User/reg') }}">注册</a>
                        <a href="{{ u('User/login') }}">登录</a>
                    </p>
                </div>
            @endif
        </header>
        <div class="list-block media-list">
            <ul class="y-wd">
                <li>
                    <a href="{{ u('Order/index') }}" class="item-link item-content " data-no-cache="true">
                        <div class="item-media icon iconfont y-wdicon1">&#xe640;</div>
                        <div class="item-inner">
                            <div class="item-title-row">
                                <div class="item-title f16">我的订单@if($user)({{$counts['orderCount']}})@endif</div>
                                <div class="fr">
                                    <i class="icon iconfont c-gray2">&#xe602;</i>
                                </div>
                            </div>
                        </div>
                    </a>
                </li>
            </ul>
            <ul class="y-wd">
                <li>
                    <a href="{{ u('UserCenter/collect') }}" class="item-link item-content">
                        <div class="item-media icon iconfont y-wdicon2">&#xe653;</div>
                        <div class="item-inner">
                            <div class="item-title-row">
                                <div class="item-title f16">我的收藏@if($user)({{$counts['collectCount']}})@endif</div>
                                <div class="fr">
                                    <i class="icon iconfont c-gray2">&#xe602;</i>
                                </div>
                            </div>
                        </div>
                    </a>
                </li>
                <li>
                    <a href="{{ u('UserCenter/address') }}" class="item-link item-content">
                        <div class="item-media icon iconfont y-wdicon3">&#xe60d;</div>
                        <div class="item-inner">
                            <div class="item-title-row">
                                <div class="item-title f16">收货地址@if($user)({{$counts['addressCount']}})@endif</div>
                                <div class="fr">
                                    <i class="icon iconfont c-gray2">&#xe602;</i>
                                </div>
                            </div>
                        </div>
                    </a>
                </li>
                <li>
                    <a href="{{ u('District/index') }}" class="item-link item-content">
                        <div class="item-media icon iconfont y-wdicon4">&#xe629;</div>
                        <div class="item-inner">
                            <div class="item-title-row">
                                <div class="item-title f16">我的小区</div>
                                <div class="fr">
                                    <span class="c-gray2 f12">@if(count($district) > 0) {{ $district[0]['name'] }} @else 请选择小区 @endif</span>
                                    <i class="icon iconfont c-gray2">&#xe602;</i>
                                </div>
                            </div>
                        </div>
                    </a>
                </li>
            </ul>
            <ul class="y-wd">
                @if(empty($seller) || $seller["isCheck"] != 1)
                    <li>
                        <a href="@if($user){{ u('Seller/reg') }} @else {{u('User/login')}} @endif" class="item-link item-content">
                            <div class="item-media icon iconfont y-wdicon5">&#xe634;</div>
                            <div class="item-inner">
                                <div class="item-title-row">
                                    <div class="item-title f16">我要开店</div>
                                    <div class="fr">
                                        <i class="icon iconfont c-gray2">&#xe602;</i>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </li>
                @endif
                @if(!empty($share_active))
                    <li id="share">
                        <a href="#" class="item-link item-content">
                            <div class="item-media icon iconfont y-wdicon6">&#xe616;</div>
                            <div class="item-inner">
                                <div class="item-title-row">
                                    <div class="item-title f16">邀请好友</div>
                                    <div class="fr">
                                        <i class="icon iconfont c-gray2">&#xe602;</i>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </li>
                @endif
            </ul>
        </div>
        <div class="y-khrx">
            <p class="f12 c-red"><i class="icon iconfont f18 mr5">&#xe609;</i>客服：<a href="tel:{{$site_config['wap_service_tel']}}" class="c-red">{{$site_config['wap_service_tel']}}</a></p>
            <p class="f12 c-gray">服务时间：{{$site_config['wap_service_time']}}</p>
        </div>
    </div>
@stop

@section($js)
    <script type="text/javascript">
        $(function(){

            $(document).on("touchend","#share",function(e){
                $(".share-frame2").fadeIn().removeClass("none");
            })

            $(document).on("touchend",".x-cltk",function(){
                $(this).parent().fadeOut();
            });

            // 分享到微信好友或朋友圈
            $(document).on("touchend",".weixin",function(){
                $(".sha-frame").fadeIn().removeClass("none");
                $(".share-frame2").addClass("none");
            });

            // 分享到微信好友或朋友圈
            $(document).on("touchend",".weixinpengyou",function(){
                $(".sha-frame").fadeIn().removeClass("none");
                $(".share-frame2").addClass("none");
            });
        });
    </script>

    <script type="text/javascript" src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
    <script>
        //新浪
        var site_desc = "{{ str_replace(["\n","\r"],["\\n", "\\r"],$desc) }}";
        var img = "{{ $share_active['image'] }}";

        window._bd_share_config = {
            common : {
                bdText : site_desc,	//内容
                bdDesc : site_desc,	//摘要
                bdUrl : "{!! $link_url !!}",
                bdPic : img
            },
            share : [{
                "bdSize" : 16
            }],
            // image : [{
            //     viewType : 'list',
            //     viewPos : 'top',
            //     viewColor : 'black',
            //     viewSize : '16',
            //     viewList : ["tsina","sqq"]
            // }],
            selectShare : [{
                "bdContainerClass":null,
                "bdselectMiniList" : ["tsina","sqq"]
            }]
        }
        with(document)0[(getElementsByTagName('head')[0]||body).appendChild(createElement('script')).src='http://bdimg.share.baidu.com/static/api/js/share.js?cdnversion='+~(-new Date()/36e5)];
    </script>

    <script language="javascript">
        //微信分享配置文件
        wx.config({
            debug: false, // 调试模式
            appId: "{{$weixin['appId']}}", // 公众号的唯一标识
            timestamp: "{{$weixin['timestamp']}}", // 生成签名的时间戳
            nonceStr: "{{$weixin['noncestr']}}", // 生成签名的随机串
            signature: "{{$weixin['signature']}}",// 签名
            jsApiList: ['checkJsApi','onMenuShareAppMessage','onMenuShareTimeline','onMenuShareQQ'] // 需要使用的JS接口列表
        });

        wx.ready(function () {
            // 在这里调用 API
            wx.onMenuShareAppMessage({
                title: "{{$share_active['name']}}", // 分享标题
                desc: '{!! $desc !!}', // 分享描述
                link: "{!! $link_url !!}", // 分享链接
                imgUrl: "{{$share_active['image']}}", // 分享图标
                type: 'link', // 分享类型,music、video或link，不填默认为link
                dataUrl: '', // 如果type是music或video，则要提供数据链接，默认为空
                success: function () {
                    // 用户确认分享后执行的回调函数
                    alert('分享成功');
                    location.reload();
                },
                cancel: function () {
                    // 用户取消分享后执行的回调函数
                }
            });
            wx.onMenuShareTimeline({
                title: '{{$share_active['name']}}', // 分享标题
                link: '{!! $link_url !!}', // 分享链接
                imgUrl: '{{$share_active['image']}}', // 分享图标
                success: function () {
                    // 用户确认分享后执行的回调函数
                    alert('分享成功');
                    location.reload();
                },
                cancel: function () {
                    // 用户取消分享后执行的回调函数
                }
            });
            wx.onMenuShareQQ({
                title: '{{$share_active['name']}}', // 分享标题
                desc: '{!! $desc !!}', // 分享描述
                link: '{!! $link_url !!}', // 分享链接
                imgUrl: '{{$share_active['image']}}', // 分享图标
                success: function () {
                    // 用户确认分享后执行的回调函数
                    alert('分享成功');
                    location.reload();
                },
                cancel: function () {
                    // 用户取消分享后执行的回调函数
                }
            });
        });
        $(function(){
            $(document).on("touchend",".bds_weixin",function(){
                $(".wxshare").show();
            });

            $(document).on('touchend','.wxshare',function(){
                $(this).hide();
            });
        });
    </script>

@stop



