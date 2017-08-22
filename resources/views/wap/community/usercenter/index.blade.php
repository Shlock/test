@extends('wap.community._layouts.base')
@section('css')
    <style type="text/css">
        .x-tkbg{width: 100%; height: 100%; background: rgba(0,0,0,.66); position: fixed; top: 0; left: 0; z-index: 1001;}
        .x-cltk {  width: 100%;  height: 100%;  position: absolute;  top: 0;  left: 0;}
        .x-xmsm .open{color: #e9bf99; position: absolute; right: -10px; bottom: 0; padding: 2px 10px; z-index: 1;}
        .x-share{background: #fff; position: absolute; left: 0; bottom: 0;width: 100%; text-align: center; padding: 25px 0;}
        .x-share ul{margin-top: 30px; padding: 0 10px;}
        .x-share ul li{width: 33.3%; float: left; text-align: center;}
        .x-share ul li a{color: #313233 !important;}
        .x-share ul li img{width: 52px; height: 52px;}
        .x-sharepic{width: 45%; float: right; margin: 5px 10px 0 0;}
        .bdshare-button-style0-16 a{width:52px !important; height:52px !important; float:none !important; margin:0 auto 5px !important; display:block; padding:0 !important;}
        .bdshare-button-style0-16 .bds_tsina {background-image:url('{{ asset('wap/community/client/images/web.png') }}');background-position:0 0 !important; background-size:100% 100%; }
    </style>
@stop
@section('show_top')
    @if($user)
    <div class="y-header">
        <h1>我的</h1>
        <a href="{{ u('UserCenter/config') }}" class="y-back"></a>
        <a class="y-sjr ui-btn-right y-msg" href="{{ u('UserCenter/message') }}">@if($user && $counts['newMsgCount'] > 0)<span>{{$counts['newMsgCount']}} </span>@endif</a>
        <div class="y-wdtx"  onclick="window.location.href='{{ u('UserCenter/info') }}'">
            <div class="y-wdtximg"><img src="@if(!empty($user['avatar'])) {{formatImage($user['avatar'],64,64)}} @else {{ asset('wap/community/client/images/wdtt.png') }} @endif"></div>
            <p class="f16">{{$user['name']}}</p>
        </div>
       <ul class="y-balancetop clearfix">
            <li  onclick="window.location.href='{{ u('UserCenter/balance') }}'">
                <strong>{{$balance}}</strong>
                <p class="f14">我的余额</p>
            </li>
            <li onclick="window.location.href='{{ u('Coupon/index') }}'">
                <strong>{{$counts['proCount'] or '0'}}</strong>
                <p class="f14">优惠券</p>
            </li>
        </ul>
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
                <li data-icon="false"><a href="{{ u('Order/index') }}"><img src="{{ asset('wap/community/client/images/ico/sz10.png') }}" /><span>我的订单@if($user)({{$counts['orderCount']}})@endif</span><i class="x-rightico"></i></a></li>
            </ul>
            <ul data-role="listview" class="y-wdlsts">
                <li data-icon="false"><a href="{{ u('UserCenter/collect') }}"><img src="{{ asset('wap/community/client/images/ico/sz6.png') }}" /><span>我的收藏@if($user)({{$counts['collectCount']}})@endif</span><i class="x-rightico"></i></a></li>
                <li data-icon="false"><a href="{{ u('UserCenter/address') }}"><img src="{{ asset('wap/community/client/images/ico/sz7.png') }}" /><span>收货地址@if($user)({{$counts['addressCount']}})@endif</span><i class="x-rightico"></i><!--<i class="yy"></i>--></a></li>
                <li data-icon="false"><a href="{{ u('Coupon/index') }}"><img src="{{ asset('wap/community/client/images/ico/sz11.png') }}" /><span>优惠券@if($user && $counts['proCount'] > 0)({{$counts['proCount']}})@endif</span><i class="x-rightico"></i></a></li>
                
                @if($isExtensionWorker)
                <li data-icon="false"><a href="{{ u('UserCenter/extension') }}"><img src="{{ asset('wap/community/client/images/ico/sz13.png') }}" /><span>我的推广数据<b class="y-bbh f12 c-green"></b></span><i class="x-rightico"></i></a></li>
                @endif
                <!--<li data-icon="false"><a href="{{ u('District/index') }}"><img src="{{ asset('wap/community/client/images/ico/sz13.png') }}" /><span>我的小区<b class="y-bbh f12 c-green"></b></span><i class="x-rightico"></i></a></li>-->
            </ul>
            <!--
            <ul data-role="listview" class="y-wdlsts">
                @if(!empty($share_active))
                <li data-icon="false" id="share"><a href="" data-ajax="false"><img src="{{ asset('wap/community/client/images/ico/sz14.png') }}" /><span>分享有礼</span><i class="x-rightico"></i></a></li>
                @endif
                @if(empty($seller) || $seller["isCheck"] != 1)
                <li data-icon="false"><a href="@if($user){{ u('Seller/reg') }}@else {{u('User/login')}} @endif" data-ajax="false"><img src="{{ asset('wap/community/client/images/ico/sz9.png') }}" /><span>我要开店</span><i class="x-rightico"></i></a></li>
                @endif
            </ul>
            -->
        </div>
        <div class="y-khrx">
            <p class="f14 c-green">客户服务热线：<a href="tel:{{$site_config['wap_service_tel']}}" style="text-decoration:underline; color:#38c;">{{$site_config['wap_service_tel']}}</a></p>
            <p class="f14 c-green">服务时间：{{$site_config['wap_service_time']}}</p>
        </div>
    </div>
    <!-- 分享框 -->
    <div class="x-tkbg share-frame2 none">
        <div class="x-cltk"></div>
        <div class="x-share">
            <p class="f16">选择分享方式</p>
            <ul class="clearfix bdsharebuttonbox">
                <li class="weixin">
                    <a href="#"><img src="{{ asset('wap/community/client/images/wx.jpg') }}"></a>
                    <p>微信好友</p>
                </li>
                <li class="weixinpengyou">
                    <a href="#"><img src="{{ asset('wap/community/client/images/wx2.jpg') }}"></a>
                    <p>微信朋友圈</p>
                </li>
                <li>
                    <a href="#" class="bds_tsina" data-cmd="tsina"></a>
                    <p>新浪微博</p>
                </li>
            </ul>
        </div>
    </div>

    <!-- 分享到微信好友或朋友圈 -->
    <div class="x-tkbg sha-frame none">
        <div class="x-cltk"></div>
        <img src="{{ asset('wap/community/client/images/share.png') }}" class="x-sharepic">
    </div>
@include('wap.community._layouts.bottom')
    <script type="text/javascript">
        $(function(){

            $("#share").touchend(function(e){
                    $(".share-frame2").fadeIn().removeClass("none");
            })

            $(".x-cltk").touchend(function(){
                $(this).parent().fadeOut();
            });

            // 分享到微信好友或朋友圈
            $(".weixin").touchend(function(){
                $(".sha-frame").fadeIn().removeClass("none");
                $(".share-frame2").addClass("none");
            });

            // 分享到微信好友或朋友圈
            $(".weixinpengyou").touchend(function(){
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


