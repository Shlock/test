@extends('xwap.community._layouts.base')

@section('show_top')
    <header class="bar bar-nav">
        <a class="button button-link button-nav pull-left back" href="@if(!empty($nav_back_url)) {{ $nav_back_url }} @else javascript:$.back(); @endif" data-transition='slide-out'>
            <span class="icon iconfont">&#xe600;</span>
        </a>
        <h1 class="title f16">充值</h1>
    </header>
@stop

@section('content')
    <!-- new -->
    <div class="content" id=''>
            <div class="c-bgfff y-paymoney mb10">
                <span class="fl f14 mt10">充值金额</span>
                <div class="y-sum">
                    <input type="number" id="charge" min="1" max="999999">
                    <p class="f12 c-gray mt5">充值后可使用余额进行交易支付</p>
                </div>
            </div>
            <div class="content-block-title f14 c-gray"><i class="icon iconfont mr10">&#xe638;</i>支付方式</div>
            <ul class="y-paylst">
                <?php
                    $payment_index = 0;
                    $default_payment = '';
                ?>
                @if($payments)
                    @foreach($payments as $key => $pay)
                        <li class="@if($payment_index == 0) on @endif @if(count($payments) == ($payment_index + 1)) last @endif" data-code="{{ $pay['code'] }}">
                            <?php
                                if (empty($default_payment)){
                                    $default_payment = $pay['code'];
                                }
                                switch ($pay['code']) {
                                    case 'weixin':
                                    case 'weixinJs':
                                        $icon = asset('wap/community/client/images/ico/zf2.png');
                                        break;
                                    case 'alipay':
                                    case 'alipayWap':
                                        $icon = asset('wap/community/client/images/ico/zf3.png');
                                        break;
                                    case 'unionpay':
									case 'unionapp':
                                        $icon = asset('wap/community/client/images/ico/zf4.png');
                                        break;
                                }
                            ?>
                            <img src="{{ $icon }}" />
                            <div class="y-payf f16">{{ $pay['name'] }}</div>
                            <i class="icon iconfont">&#xe612;</i>
                        </li>
                        <?php $payment_index ++; ?>
                    @endforeach
                @endif
            </ul>
            <p class="y-bgnone"><a class="y-paybtn f16 x-paybtn">确认充值</a></p>
        </div>
@stop

@section($js)
<script type="text/javascript">
    var payment = "{{ $default_payment }}";

    // 回调函数
    function PayComplete(result)
    {
        if (result == "Success")
        {
            $.router.load("{{ u('UserCenter/balance') }}", true);
        }
    }

    $(".x-paybtn").on("click",function(){
        var money = parseFloat( $("#charge").val() );
        if(money <= 0 || money=='' || isNaN(money)){
            $.alert('充值金额必须是大于0的数字');
            return;
        }

        if (window.App && payment != "balancePay")
        {
            var result = $.ajax({ url: "{{u('UserCenter/createpaylog')}}?payment=" + payment + "&money=" + money, async: false, dataType: "text" });

            window.App.pay_sdk(result.responseText);
        }
        else
        {
            if (payment == 'weixinJs')
            {
                $.alert("暂没实现");
                // $.router.load("{{ u('UserCenter/wxpay') }}?payment=" + payment+"&money="+money, true);
            } else
            {
                $.router.load("{{ u('UserCenter/pay') }}?payment=" + payment + "&money=" + money, true);
            }
        }
    });

    $(document).on("click", ".y-paylst li", function(){
        payment = $(this).data("code");
        $(this).addClass("on").siblings().removeClass("on");
    });
</script>
@stop