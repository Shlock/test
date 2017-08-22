@extends('xwap.community._layouts.base')

@section('show_top')
<header class="bar bar-nav">
    <a class="button button-link button-nav pull-left pageloading back" href="@if(!empty($nav_back_url)) {{ $nav_back_url }} @else javascript:$.back(); @endif" data-transition='slide-out'>
        <span class="icon iconfont">&#xe600;</span>
    </a>
    <h1 class="title f16">充值详情</h1>
</header>
@stop

@section('content')
    @include('xwap.community._layouts.bottom')
    <div class="content y-ddxqlist" id=''>
        <ul class="y-main f14 y-ddxqmain2">
            <li>
                <span>充值:</span>
                <span class="fr c-red">￥{{ $money }}</span>
            </li>
        </ul>
        <div class="y-ddxqbtn">
            <a href="{{u('UserCenter/recharge')}}" class="ui-btn fl" id="x-fwcansels">取消支付</a>
            <a href="javascript:$.callpay();" class="ui-btn fr" id="x-fwcansels">立即支付</a>
        </div>
    </div>
@stop

@section($js)
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

            $.callpay = function()
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
                        alert('取消支付');
                        location.href = "{{ u('Order/detail', ['id' => $data['id']]) }}";
                    },
                    fail: function (res) {
                        alert('支付失败');
                        location.href = "{{ u('Order/detail', ['id' => $data['id']]) }}";
                    }
                });
            }
        </script>
    @endif
@stop