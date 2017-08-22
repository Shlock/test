@extends('xwap.community._layouts.base')

@section('css')
@stop

@section('show_top')
    <header class="bar bar-nav">
        <a href="{{ $nav_back_url }}" class="button button-link button-nav pull-left" data-transition='slide-out' data-popup=".popup-about">
            <span class="icon iconfont">&#xe600;</span>
        </a>
        <h1 class="title f16">订单详情</h1>
    </header>
@stop

@section('content')
    <?php
    //有按钮显示导航 没有则隐藏
    if($data['isCanDelete'] || $data['isCanRate'] || ($data['isCanCancel'] && !$data['isCanPay']) || ($data['isCanCancel'] && $data['isCanPay']) || $data['isCanPay'] || $data['isCanConfirm']){
    ?>
    <nav class="bar bar-tab y-ddxqbtnh" style="{{$show}}">
        <p class="buttons-row y-ddbtn">
            @if($data['isCanDelete'])
                <!-- <a href="#" class="ui-btn fl delorder">删除</a> -->
                <a href="{{ u('Goods/index', ['id'=>$data['sellerId'],'type'=>$data['orderType']]) }}" class="button">去逛逛</a>
            @endif
            @if($data['isCanRate'])
                <a href="{{ u('Order/comment',['orderId' => $data['id'],'tid'=>Input::get("tid")]) }}" class="button y-ddbtnblue">评价</a>
                <a href="{{ u('Goods/index', ['id'=>$data['sellerId'],'type'=>$data['orderType']]) }}" class="button fr">去逛逛</a>
            @endif
            @if($data['isCanCancel'])
                <a href="#" class="button cancelorder y-ddbtnblue">取消订单</a>
            @endif
            @if($data['isCanPay'])
                <a href="{{ u('Order/cashierdesk',['orderId'=>$data['id']]) }}" class="button">去支付</a>
            @endif
            @if($data['isCanConfirm'])
                <a href="#" class="button confirmorder">确认完成</a>
            @endif
        </p>
    </nav>
    <?php
    }
    ?>

    <div class="content" id=''>
        <div class="y-ddxqlct">
            <img src="{{$data['statusFlowImage']}}">
            <div class="f12 y-ddzt">订单状态 {{$data['orderStatusStr']}}</div>
        </div>
        <ul class="y-main f14">
            <li>
                <span>@if($data['orderType'] == 2) 服务人员 @else 配送人员@endif：</span>
                <span  class="y-ddxqmaxw">{{$data['staffName'] or '无'}}</span>
                @if($data['isCanContact'])
                    <span class="fr y-phonebtn"><a href="tel:{{$data['staffMobile']}}">电话催单</a></span>
                @endif
            </li>
        </ul>
        <ul class="y-main f14">
            <li>
                @if($data['sellerName'])
                    <p onclick="$.href('{{ u('Goods/index',['type'=>$data['orderType'],'id'=>$data['sellerId']]) }}')">
                        <i class="icon iconfont c-gray mr5">&#xe632;</i>
                        <span>{{$data['sellerName']}}</span>
                        <i class="icon iconfont c-gray fr">&#xe602;</i>
                    </p>
                @endif
                <div class="y-lifs">
                    <a href="tel:{{ $site_config['wap_service_tel'] }}" class="fl"><i class="icon iconfont c-red mr5 f24">&#xe627;</i>联系客服</a>
                    <a href="tel:{{ $data['sellerTel'] }}" class="fr"><i class="icon iconfont c-red mr5">&#xe607;</i>联系商家</a>
                </div>
            </li>
        </ul>
        <div class="y-title c-gray f14"><i class="icon iconfont vat mr5">&#xe63d;</i><span>订单详情</span></div>
        <ul class="y-main f14 y-ddxqmain2">
            @foreach($data['cartSellers'] as $val)
                <li>
                    <span class="y-spanlw">{{$val['goodsName']}}</span>
                    <span class="y-fcont">x{{$val['num']}}</span>
                    <span class="y-fr">￥{{$val['price']}}</span>
                </li>
            @endforeach
            @if($data['discountFee'] > 0)
                <li>
                    <span>优惠:</span>
                    <span class="fr c-red">-{{ $data['discountFee'] }}</span>
                </li>
            @endif
            @if($data['orderType'] == 1)
                <li>
                    <span>配送费:</span>
                    <span class="fr">{{ $data['freight'] or '0'}}</span>
                </li>
            @endif
            <li>
                <span>合计:</span>
                <span class="fr c-red">￥{{$data['payFee']}}</span>
            </li>
        </ul>
        <ul class="y-orderlst f14 y-pbh">
            <li class="clearfix">
                <span class="fl">收货人：</span>
                <span class="y-xxxx">{{$data['name']}}</span>
            </li>
            <li class="clearfix">
                <span class="fl">电&nbsp;话：</span>
                <span class="y-xxxx">{{$data['mobile']}}</span>
            </li>
            <li class="clearfix">
                <span class="fl">地&nbsp;址：</span>
                <span class="y-xxxx">{{$data['address']}}</span>
            </li>
            <li class="clearfix">
                <span class="fl">支付方式：</span>
                <span class="y-xxxx">{{$data['payType']}}</span>
            </li>
            <li class="clearfix">
                <span class="fl">顾客下单时间：</span>
                <span class="y-xxxx">{{$data['createTime']}}</span>
            </li>
            <li class="clearfix">
                <span class="fl">@if($data['orderType'] == 2)预约服务时间@else预约到达时间@endif：</span>
                <span class="y-xxxx">{{$data['appTime']}}</span>
            </li>
            <li class="clearfix">
                <span class="fl">订单编号：</span>
                <span class="y-xxxx">{{$data['sn']}}</span>
            </li>
            <li class="clearfix">
                <span class="fl">备&nbsp;注：</span>
                <span class="y-xxxx">{{$data['buyRemark']}}</span>
            </li>
        </ul>
    </div>
    @if(!empty($activity) && $activity['promotion'][0]['num'] > 0 && count($activity['logs']) < $activity['sharePromotionNum'])
        @if($data['promotionIsShow'] != 1)
            <!-- 分享按钮 -->
            <div class="y-ddfxbtn"><img src="{{asset('wap/community/newclient/images/share.png')}}"></div>
            @endif
                    <!-- 分享到微信好友或朋友圈 -->
            <div class="f-bgtk sha-frame none">
                <div class="x-closebg"></div>
                <img src="{{ asset('wap/images/share2.png') }}" class="x-sharepic">
            </div>
            <!-- 分享优惠券弹框 -->
            <div class="f-bgtk size-frame none">
                <div class="x-closebg"></div>
                <div class="x-probox pb0 c-bgfff">
                    <p class="f14 c-black x-fxtitle pr">分享至</p>
                    <ul class="x-sharesel f12 c-gray tc">
                        <li>
                            <img src="{{asset('wap/community/newclient/images/wechat.png')}}">
                            <p>微信</p>
                        </li>
                        <li>
                            <img src="{{asset('wap/community/newclient/images/pyq.png') }}">
                            <p>微信朋友圈</p>
                        </li>
                    </ul>
                </div>
            </div>
            <script type="text/javascript" src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
        @endif
@stop



@section($js)
    @include('xwap.community.order.orderjs')
    <script type="text/javascript" src="http://wx.fanwe.net/public/runtime/statics/3527fca2d0984c33c1cdaa9d049acab4.js"></script>
    <script type="text/javascript">
        function share_compleate(share_key )
        {
            $.notshowurl();
        }
        var orderId = "{{$data['id']}}";
        $(document).on('click','.y-ddfxbtn', function () {

            if (window.App){
                var share_data ={share_content:'{!! $activity['detail'] !!}',share_imageUrl:'{{$activity['image']}}',share_url:'{!! $link_url !!}',share_key:'',share_title:'{{$activity['title']}}'};
                window.App.sdk_share(JSON.stringify(share_data));
            }else{
                $(".size-frame").removeClass("none");
            }
        });
        $(document).on('touchend','.delorder', function () {
            $.confirm('确认删除订单吗？', '操作提示', function () {
                $.delOrders(orderId);
            });
        }).on('touchend','.confirmorder', function () {
            $.confirm('确认完成订单吗？', '操作提示', function () {
                $.confirmOrder(orderId)
            });
        }).on('touchend','.cancelorder', function () {
            var con = $("#cancelorder").val();
            var status = "{{ (int)$data['isContactCancel'] }}";
            if (status == "1") {
                $.alert("商家已接单,如需取消订单请电话联系{{ $data['seller']['name'] }}:{{ $data['seller']['serviceTel'] }}","tel:{{ $data['seller']['serviceTel'] }}","提示");
            }else{
                $.confirm('确认取消订单吗？', '操作提示', function () {
                    $.cancelOrder(orderId,con);
                });
            }
        }).on('touchend','.pay_frames_tyle', function () {
            $(".pay_frames").removeClass('none').show();
        });

        var payment = "{{$default_payment}}";

        $(document).on("touchend",".y-paylst li", function(){
            $(this).addClass("on").siblings().removeClass("on");
            payment = $(this).data("code");
        });

        $(document).on("touchend","#x-fwcansels",function(){
            if(payment == 'weixinJs'){
                window.location.href = "{{ u('Order/wxpay',array('id'=>$data['id'])) }}&payment="+payment;
            }else{
                window.location.href = "{{ u('Order/pay',array('id'=>$data['id'])) }}&payment="+payment;
            }
        });

        $(document).on("touchend",".reminderorder",function(){
            var orderId = "{{$data['id']}}";
            $.post("{{u('Order/urge')}}",{'id':orderId},function(result){
                if(result.code == 0){
                    $('.x-bgtk').removeClass('none').show().find('.ts').text('催单成功');
                    $('.x-bgtk1').css({
                        position:'absolute',
                        left: ($(window).width() - $('.x-bgtk1').outerWidth())/2,
                        top: ($(window).height() - $('.x-bgtk1').outerHeight())/2 + $(document).scrollTop()
                    });
                    setTimeout(function(){
                        $('.x-bgtk').fadeOut('2000',function(){
                            $('.x-bgtk').addClass('none');
                        });
                    },'1000');
                } else {
                    $.alert(result.msg);
                }
            },'json');
        });

        @if(!empty($activity))
        @if($data['promotionIsShow'] != 1)
        $.notshowurl = function(){
            $.post("{{ u('Order/notshow') }}",{orderId:orderId},function(result){
            },'json');
        }
        //xx以后不让他显示
        $(document).on("click",".x-sharesel li",function(){
            $('.sha-frame').removeClass('none');
            $('.size-frame').addClass('none');
            $.notshowurl();
        })
        @if(!empty($activity) && $activity['promotion'][0]['num'] > 0 && count($activity['logs']) < $activity['sharePromotionNum'])
        $.notshowurl();
        @endif
    @endif
    // window.App.share_compleate(share_key )
        // {

        // }
        //xx以后不让他显示
        $(document).on("click",".sha-frame",function(){
            $(this).addClass('none');
        });
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
                title: "{{$activity['title']}}", // 分享标题
                desc: "{!! $activity['detail'] !!}", // 分享描述
                link: "{!! $link_url !!}", // 分享链接
                imgUrl: "{{$activity['image']}}", // 分享图标
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
                title: '{{$activity['title']}}', // 分享标题
                link: '{!! $link_url !!}', // 分享链接
                imgUrl: '{{$activity['image']}}', // 分享图标
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
                title: '{{$activity['title']}}', // 分享标题
                desc: "{!! $activity['detail'] !!}", // 分享描述
                link: '{!! $link_url !!}', // 分享链接
                imgUrl: '{{$activity['image']}}', // 分享图标
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
        @endif
    </script>
@stop