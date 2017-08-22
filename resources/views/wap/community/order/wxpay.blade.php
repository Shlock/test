@extends('wap.community._layouts.base')
@section('css')
    <style>
        .x-anniu .ui-btn {background-color: #FF2D4B; text-shadow:none; margin-bottom:10px;}
		.y-cont ul li:first-child{ border:0;}
    </style>
@stop
@section('js')
@stop
@section('show_top')
 <div data-role="header" data-position="fixed" class="x-header">
    <h1>订单详情</h1>
    <a href="@if(!empty($nav_back_url)) {{ $nav_back_url }} @else javascript:history.back(-1); @endif" data-iconpos="notext" class="x-back ui-nodisc-icon" data-shadow="false"></a>
</div>
@stop
@section('content')
    <div class="y-cont">
        <ul>
        @foreach($data['cartSellers'] as $val)
            <li>
                <div class="y-img"><img src="{{ $val['goodsImages'] }}"></div>
                <div class="y-ddxqtext">
                    <p class="f16">{{ $val['goodsName'] }}<span class="f12">X{{ $val['num'] }}</span></p>
                    <h3 class="c-red f14">￥@if(!empty($data['norms'])) {{ $data['norms']['price'] }} @else {{ $val['price'] }} @endif</h3>
                </div>
            </li>
            @if($data['orderType'] == "2")
                <div class="y-ddxx y-bortop" style="background:none;padding-bottom:0">
                    <img src="{{ asset('wap/community/client/images/ico/ddxq_down.png') }}" class="y-ddxqdown">
                    <div class="y-item1" style="border:0">
                        <p><span class="y-left">服务时间：</span><span class="y-right">{{ $data['appTime'] }}</span></p>
                        <p><span class="y-left">服务人员：</span><span class="y-right">{{ $data['staff']['name'] }} {{ $data['staff']['mobile'] }}</span></p>
                    </div>
                </div>
            @endif
        @endforeach
        </ul>
        <div class="y-ddxx y-bortop">
            <div class="y-item1">
                <p><span>@if($data['orderType'] == 1)商品价格@else服务费用@endif：</span><span class="y-zfjg">￥{{ $data['goodsFee'] }}</span></p>
            </div>
            @if($data['orderType'] == 1)
                <div class="y-item2">
                    <p><span>运费：</span><span class="y-zfjg">￥{{ $data['freight'] }}</span></p>
                </div>
            @endif
            <div class="y-item2">
                <p><span>合计：</span><span class="c-red y-zfjg">￥{{ $data['totalFee'] }}</span></p>
            </div>
        </div>
    </div>
	<div class="x-zfbox x-anniu">
        <p class="x-zfb" style="padding-right:5%;padding-left:5%;margin-top: 10px">
            <button style="width:49%" class="x-btn fl" id="x-fwcansels" onclick="javascript:callpay()">立即支付</button>
            <button style="width:49%;padding-left:2%;" class="x-btn fr" id="x-fwcansels" onclick="location.href='{{u('Order/detail',['id'=>$data['id']])}}'">取消</button>
        </p>
    </div>
@if($payment == 'weixinJs')
    <script type="text/javascript" src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
    <script type="text/javascript">
    //微信分享配置文件
    wx.config({
        debug: false, // 调试模式
        appId: "{{$pay['appId']}}", // 公众号的唯一标识
        timestamp: "{{$pay['jsapi']['timestamp']}}", // 生成签名的时间戳
        nonceStr: "{{$pay['jsapi']['noncestr']}}", // 生成签名的随机串
        signature: "{{$pay['jsapi']['signature']}}",// 签名
        jsApiList: ['checkJsApi','chooseWXPay'] // 需要使用的JS接口列表
    });


    function callpay()
    {
        wx.chooseWXPay({
            timestamp: "{{$pay['timeStamp']}}", // 支付签名时间戳，注意微信jssdk中的所有使用timestamp字段均为小写。但最新版的支付后台生成签名使用的timeStamp字段名需大写其中的S字符
            nonceStr: "{{$pay['nonceStr']}}", // 支付签名随机串，不长于 32 位
            package: "{{$pay['package']}}", // 统一支付接口返回的prepay_id参数值，提交格式如：prepay_id=***）
            signType: "{{$pay['signType']}}", // 签名方式，默认为'SHA1'，使用新版支付需传入'MD5'
            paySign: "{{$pay['paySign']}}", // 支付签名
            success: function (res) {
                alert('支付成功');
                location.href = "{{ u('Order/detail', ['id' => $data['id']]) }}";
            },
            cancel: function (res) {
                console.log(res);
                //alert('取消支付');
                //location.href = "{{ u('Order/detail', ['id' => $data['id']]) }}";
            },
            fail: function (res) {
                console.log(res);
                //alert('支付失败');
                //location.href = "{{ u('Order/detail', ['id' => $data['id']]) }}";
            }
        });
    }
</script>
@endif
@include('wap.community._layouts.bottom')
@stop