@extends('wap.community._layouts.base_order')
@section('css')
<style>
    .chaoz a{color: #fff}

    .x-tkcoupons{margin: 0 45px; border-radius: 5px;}
    .x-tkcoupons .couponstt img{width: 100%; vertical-align: bottom;}
    .x-tkcoupons .couponstt .couclose{width: 25px; height: 25px; background: url("{{ asset('wap/images/ico/couclose.png') }}") no-repeat; background-size: 100% 100%; top: 7px; right: 7px;}
    .x-tkcoupons .couponsm{background: #fff; border-radius: 0 0 5px 5px; line-height: 16px;}
    .x-tkcoupons .couponsm .f40{font-size: 40px; line-height: 40px; vertical-align: -7px; padding: 0 2px;}
    .x-tkcoupons .couponsbtn{line-height: 45px; border-top: 1px solid #e3e1e1;}
    .x-sharebtn{width: 80px; height: 81px; background: url("{{ asset('wap/images/share.png') }}"); background-size: 100% 100%; right: 10px; bottom: 100px;}
    @media screen and (max-height:500px){
        .x-sharebtn{bottom: 60px;}
    }
    .x-shareb{width: 100%; height: 100%;}
    .x-sharetk{background: #fff; left: 0; bottom: 0; width: 100%;}
    .x-sharetk .sharett{line-height: 45px; padding: 0 10px; border-bottom: 1px solid #e3e1e1;}
    .x-sharetk .x-sharesel li{width: 50%; float: left;}
    .x-sharetk .x-sharesel li .br{padding: 25px 0;}
    .x-sharetk .x-sharesel li:first-of-type .br{border-right: 1px solid #e1e1e1;}
    .x-sharetk .x-sharesel li img{width: 60px; margin-bottom: 2px;}
    .x-sharepic{width: 45%; position: absolute; top: 5px; right: 10px;}

    .y-ddxqbtn{margin-top: -1em;padding: 1em 0px;background: #fff;overflow: hidden;position:fixed; left: 0px; bottom: 0px; width: 100%;}

</style>
@stop
@section('js')
@stop
@section('show_top')
     <div data-role="header" data-position="fixed" class="x-header">
        <h1>订单详情</h1>
        <a href="@if(!empty($nav_back_url)) {{ $nav_back_url }} @else javascript:history.back(-1); @endif" data-iconpos="notext" class="x-back ui-nodisc-icon" data-shadow="false"></a>
        {{--<a href="tel:{{ $data['seller']['mobile'] }}" class="x-sjr ui-btn-right"><img src="{{  asset('wap/community/client/images/ico/head-call.png') }}" width="23" /></a>--}}
    </div>
@stop
@section('content')
    <div role="main" class="ui-content">
        <div class="y-ddxqlct">
            <img src="{{$data['statusFlowImage']}}">
            {{--<ul class="clearfix">
                <img src=""/>
            </ul>--}}
            <div class="f12 y-ddzt">订单状态 {{$data['orderStatusStr']}}</div>
        </div>
        <ul class="y-main f14">
            @if($data['refundCount'] > 0)
            <li onclick="window.location.href='{{ u('Order/refundview',['orderId'=>$data['id']]) }}'">
                <span>查看退款详情</span>
                <i class="x-rightico fr"></i>
            </li>
            @endif
            <li>
                <span>@if($data['orderType'] == 2) 服务人员@else 配送人员@endif：</span>
                <span>{{$data['staffName']}}</span>
                @if($data['isCanContact'])
                <span class="fr y-phonebtn"><a href="tel:{{$data['staffMobile']}}">电话催单</a></span>
                @endif
            </li>
        </ul>
        <ul class="y-main f14">
            <li>
                <p onclick="window.location.href='{{ u('Goods/index',['type'=>$data['orderType'],'id'=>$data['sellerId']]) }}'">
                    <img src="{{ asset('wap/community/client/images/ico/ico1.png') }}" width="17">
                    <span>{{$data['sellerName']}}</span>
                    <i class="x-rightico fr"></i>
                </p>
                <div>
                    <a href="tel:{{ $site_config['wap_service_tel'] }}" class="fl"><img src="{{ asset('wap/community/client/images/ico/lxkf-img.png') }}" height="18">联系客服</a>
                    <a href="tel:{{ $data['sellerTel'] }}" class="fr"><img src="{{ asset('wap/community/client/images/ico/head-call.png') }}" height="18">联系商家</a>
                </div>
            </li>
        </ul>
        <div class="y-title c-green f14"><img src="{{ asset('wap/community/client/images/ico/ddxq-img.png') }}" width="17"><span>订单详情</span></div>
        <ul class="y-main f14">
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
                <span class="fr">{{ $data['freight'] }}</span>
            </li>
            @endif

            <li>
                <span>合计:</span>
                <span class="fr c-red">￥{{$data['payFee']}}</span>
            </li>
        </ul>
        <ul class="y-orderlst f14">
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
    <div class="y-ddxqbtn">
        @if($data['isCanDelete'])
            <!--<a href="javascript:;" class="ui-btn fl delorder">删除</a>-->
            <a href="{{ u('Goods/index', ['id'=>$data['sellerId'],'type'=>$data['orderType']]) }}" class="ui-btn" style="width:90%">去逛逛</a>
        @endif
        @if($data['isCanRate'])
            <a href="{{ u('Order/comment',['orderId' => $data['id']]) }}" class="ui-btn fl">评价</a>
            <a href="{{ u('Goods/index', ['id'=>$data['sellerId'],'type'=>$data['orderType']]) }}" class="ui-btn fr">去逛逛</a>
        @endif
        @if($data['isCanCancel'] && !$data['isCanPay'])
            <a href="javascript:;" class="ui-btn fl cancelorder" style="width:90%">取消订单</a>
        @endif
        @if($data['isCanPay'])
            <a href="javascript:;" class="ui-btn fl cancelorder">取消订单</a>
            <a href="{{ u('Order/cashierdesk',['orderId'=>$data['id']]) }}" class="ui-btn fr">去支付</a>
        @endif
        @if($data['isCanConfirm'])
            <a href="javascript:;" class="ui-btn fr confirmorder">确认完成</a>
        @endif
    </div>

    @if(!empty($activity) && $activity['promotion'][0]['num'] > 0 && count($activity['logs']) < $activity['sharePromotionNum'])
        @if($data['promotionIsShow'] != 1)
            <!-- 分享优惠券弹框 -->
            <div class="m-tkbg">
                <div class="x-tkbg">
                    <div class="x-tkbgi">
                        <div class="x-tkcoupons">
                            <div class="couponstt pr">
                                <img src="{{ asset('wap/images/couponspic.png') }}" />
                                <i class="couclose pa"></i>
                            </div>
                            <div class="couponsm tc pt15">
                                <strong class="f18">恭喜获得<span class="c-red f40">{{ $activity['sharePromotionNum'] }}</span>张优惠券</strong>
                                <p class="f12 mt5">分享优惠券给好友</p>
                                <p class="f12">可用于抵扣在线支付金额！</p>
                                <div class="c-red couponsbtn mt15 f14">发优惠券</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- 分享优惠券弹框 -->
        @endif

        <div class="x-sharebtn pf"></div>
        <div class="m-tkbg share-frame none">
            <div class="x-shareb"></div>
            <div class="x-sharetk pa">
                <div class="sharett f14">分享至</div>
                <ul class="x-sharesel f12 c-green tc">
                    <li>
                        <div class="br">
                            <img src="{{ asset('wap/images/wechat.png') }}" />
                            <p>微信</p>
                        </div>
                    </li>
                    <li>
                        <div class="br">
                            <img src="{{ asset('wap/images/pyq.png') }}" />
                            <p>微信朋友圈</p>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
        <!-- 分享到微信好友或朋友圈 -->
        <div class="m-tkbg sha-frame none">
            <div class="x-shareb"></div>
            <img src="{{ asset('wap/images/share2.png') }}" class="x-sharepic">
        </div>
        <script type="text/javascript" src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
    @endif

        <!-- content end -->
    </div>
@include('wap.community.order.orderjs') 
<script type="text/javascript">       
    var orderId = "{{$data['id']}}";

    @if(!empty($activity))
        @if($data['promotionIsShow'] != 1)
            $.notshowurl = function(){
                $.post("{{ u('Order/notshow') }}",{orderId:orderId},function(result){

                },'json');
            }
            //xx以后不让他显示
            $(".x-tkcoupons .couclose").touchend(function(){
                $(this).parents(".m-tkbg").addClass("none").fadeOut();
                $.notshowurl();
            });
            $(".x-tkcoupons .couponsbtn").click(function(){
                $(this).parents(".m-tkbg").addClass("none").fadeOut();
                $(".share-frame").fadeIn().removeClass("none");
                $.notshowurl();
            })
            @if(!empty($activity) && $activity['promotion'][0]['num'] > 0 && count($activity['logs']) < $activity['sharePromotionNum'])
            $.notshowurl();
            @endif
        @endif


    $(".x-sharebtn").touchend(function(){
        $(".share-frame").fadeIn().removeClass("none");
    });
    $(".x-shareb").touchend(function(){
        $(this).parent().fadeOut().addClass("none");
    });
    $(".x-sharetk .x-sharesel li").touchend(function(){
        $(".sha-frame").fadeIn().removeClass("none");
        $(".share-frame").fadeOut().addClass("none");
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
    

    $(document).on("touchend",".delorder",function(){
        $.showOperation('确认删除订单吗？',"javascript:$.delOrders(" + orderId + ");",'删除订单');
    }).on("touchend",".confirmorder",function(){
        $.showOperation('确认完成订单吗？',"javascript:$.confirmOrder(" + orderId + ");",'确认完成');
    }).on("touchend",".cancelorder",function(){
        var con = $("#cancelorder").val();
        var status = "{{ (int)$data['isContactCancel'] }}";
        if (status == "1") {
            $.showOrderCancelNotice("商家已接单,如需取消订单请电话联系{{ $data['seller']['name'] }}:{{ $data['seller']['serviceTel'] }}","javascript:$.callSeller('{{$data['seller']['serviceTel']}}');","提示");
        }else{
            $.showOperation('确认取消订单吗？',"javascript:$.cancelOrder(" + orderId +","+"'"+con+"'"+");",'确认取消？');            
        }
    }).on("touchend",'.pay_frames_tyle',function(){
        $(".pay_frames").removeClass('none').show();
    });
    var payment = "{{$default_payment}}"; 
        
    $(".y-paylst li").touchend(function(){
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
                $.showError(result.msg);
            }
        },'json');
    });
</script>
@stop