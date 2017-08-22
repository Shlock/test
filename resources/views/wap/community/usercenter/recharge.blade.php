@extends('wap.community._layouts.base')

@section('show_top')
    <div data-role="header" data-position="fixed" class="x-header">
        <h1>充值</h1>
        <a href="@if(!empty($nav_back_url)) {{ $nav_back_url }} @else javascript:history.back(-1); @endif" data-iconpos="notext" class="x-back ui-nodisc-icon" data-shadow="false"></a>
    </div>
@stop
@section('content')
    <div role="main">
        <div class="y-money f14">
            <span class="fl">充值金额:</span>
            <input type="text" id="charge">
        </div>
        <p class="y-beizhu c-green"><img src="{{ asset('wap/community/client/images/ico/zffs.png') }}" width="15" height="15">支付方式</p>
        <ul class="y-paylst">
            <?php
            $payment_index = 0;
            $default_payment = '';
            ?>
            @if($payments)
                @foreach($payments as $key => $pay)
                    <li class="@if($payment_index == 0)on @endif @if(count($payments) == ($payment_index + 1)) last @endif" data-code="{{ $pay['code'] }}">
                        <?php
                        if (empty($default_payment)){
                            $default_payment = $pay['code'];
                        }
                        switch ($pay['code']) {
                            case 'alipay':
                            case 'alipayWap':
                                $icon = asset('wap/community/client/images/ico/zf3.png');
                                break;
                            case 'weixin':
                            case 'weixinJs':
                                $icon = asset('wap/community/client/images/ico/zf2.png');
                                break;
                            case 'unionpay':
                                $icon = asset('wap/images/ico/yl.png');
                                break;
                        }
                        ?>
                        <img src="{{ $icon }}">
                        <div class="y-payf f16">{{ $pay['name'] }}</div>
                        <i></i>
                    </li>
                    <?php $payment_index ++; ?>
                @endforeach
            @endif
        </ul>
        <div class="y-end">
            <a href="javascript:;" class="ui-btn x-paybtn">确认充值</a>
        </div>
    </div>
    <script type="text/javascript">
        var payment = "{{ $default_payment }}";
        $(".y-paylst li").touchend(function(){
            $(".y-paylst li").removeClass("on");
            $(this).addClass("on");
            payment = $(this).data("code");
        });
        $(".x-paybtn").on("touchend",function(){
            var money = parseFloat($("#charge").val()); 
            if(money <= 0 || isNaN(money)){
                $.showError('充值金额必须大于0');
                return;
            }
            if (payment == 'weixinJs') {
                window.location.href = "{{ u('UserCenter/wxpay',array('id'=>$orderId)) }}&payment=" + payment+"&money="+money;
            } else {
                window.location.href = "{{ u('UserCenter/pay',array('id'=>$orderId)) }}&payment=" + payment+"&money="+money;
            }
        })
    </script>
@stop

