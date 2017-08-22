@extends('xwap.community._layouts.base')

@section('show_top')
<header class="bar bar-nav">
    <a class="button button-link button-nav pull-left back" href="@if(!empty($nav_back_url)) {{ $nav_back_url }} @else javascript:$.back('确认取消当前订单支付？','取消操作'); @endif" data-transition='slide-out'>
        <span class="icon iconfont">&#xe600;</span>
    </a>
    <h1 class="title f16">收银台</h1>
</header>
@stop

@section('content')
    <!-- new -->
    <div class="content" id=''>
        <div class="list-block y-syt">
            <ul>
                <li class="item-content">
                    <div class="item-inner">
                        <div class="item-title c-gray">商家</div>
                        <div class="item-after c-black">{{ $data['sellerName'] }}</div>
                    </div>
                </li>
                <li class="item-content">
                    <div class="item-inner">
                        <div class="item-title c-gray">合计</div>
                        <div class="item-after c-red">￥{{ number_format($data['payFee']-$data['payMoney'], 2) }}</div>
                    </div>
                </li>
            </ul>
        </div>
        <div class="content-block-title f14 c-gray"><i class="icon iconfont mr10">&#xe638;</i>支付方式</div>
        <ul class="y-paylst">
            @if($payments)
                <?php
                $default_payment = '';
                ?> 
                @if($data['user']['balance'] > 0 && $data['payMoney'] <= 0)
                <?php 
                    if($data['user']['balance'] >= $data['payFee']){
                        $default_payment = 'balancePay';
                    }
                ?>
                <li class=" @if($data['user']['balance'] < $data['payFee']) y-zhye @endif on">
                    <img src="{{ asset('wap/community/newclient/images/ico/zf5.png') }}" />
                    <div class="y-payf y-sytpay @if($data['user']['balance'] < $data['payFee']) y-paynocenter @endif">
                        <p>账户余额可用<span>{{ $data['user']['balance'] }}</span>元</p>
                        @if($data['user']['balance'] < $data['payFee'])
                            <p class="f12 balance_tip c-gray">
                                还需在线支付<span class="c-red">{{ ($data['user']['balance'] >= $data['payFee']) ? 0 : (number_format($data['payFee'] - $data['user']['balance'], 2)) }}元</span>
                            </p>
                        @endif
                    </div>
                    <label class="label-switch x-sliderbtn fr mr10 mt5">
                        <input type="checkbox" id="is_balance_pay" checked="checked" >
                        <div class="checkbox"></div>
                    </label>
                </li>
                @endif
                @foreach($payments as $key => $pay)
                <?php
                if (empty($default_payment)){
                    $default_payment = $pay['code'];
                }
                ?>
                <li class="@if(($pay['code'] == $default_payment) && $data['payFee'] - $data['user']['balance'] > 0) on @endif" data-code="{{ $pay['code'] }}">
                    <?php
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
						case 'unionapp':
                            $icon = asset('wap/images/ico/yl.png');
                            break;
                    }
                    ?>
                    <img src="{{ $icon }}" />
                    <div class="y-payf">{{ $pay['name'] }}</div>
                    <i class="icon iconfont">&#xe612;</i>
                </li>
                @endforeach
            @endif
        </ul>
        <p class="y-bgnone"><a href="javascript:btnOK_onclick();" class="y-paybtn f16 x-paybtn">确认支付</a></p>
    </div>
@stop

@section($js)
<script type="text/javascript">

    var payment = "{{ $default_payment }}";
    var balancePay = {{$data['user']['balance'] > 0 ? 1 : 0}}; 
    var isCanBalancePay = {{$data['user']['balance'] >= $data['payFee'] ? 1 : 0}};
    $(document).on("touchend",".y-paylst li",function(){
        if(balancePay == 0 || isCanBalancePay == 0){
            $(".y-paylst li").removeClass("on");
            $(this).addClass("on");
            payment = $(this).data("code"); 
        }
    });
    $("#is_balance_pay").change(function(){
        balancePay = $("#is_balance_pay:checked").val() == "on" ? 1 : 0;
        if(balancePay == 1){
            if(isCanBalancePay == 1){
                $(".y-paylst li").removeClass("on");
                payment = 'balancePay';
            }
            $(".balance_tip").show();
            //$(".balance_tip").parent().parent().addClass('y-zhye');
        } else {
            $(".balance_tip").hide();
            //$(".balance_tip").parent().parent().removeClass('y-zhye'); 
        } 
    }); 

    // 支付
    function btnOK_onclick()
    {
        try
        {
            if (window.App && payment != "balancePay" && typeof(payment) != "undefined")
            {  
				var url = "{{u('Order/createpaylog')}}?payment=" + payment + "&id={{$data['id']}}";
				if(balancePay == 1){
					url = "{{u('Order/createpaylog')}}?payment=" + payment + "&id={{$data['id']}}&balancePay="+balancePay;
				}  
                var result = $.ajax({ url: url, async: false, dataType: "text"});

                window.App.pay_sdk(result.responseText);
            } else {
                 var url = '';
                if(payment == 'weixinJs'){
                    url = "{{ u('Order/wxpay',array('id'=>$data['id'])) }}&payment="+payment+"&balancePay="+balancePay;
                }else if(payment == 'balancePay'){
                    if(balancePay == 1){
                        url = "{{ u('Order/balancepay',array('id'=>$data['id'])) }}&payment="+payment;
                    } else {
                        $.alert('请选择支付方式');
                        return;
                    }
                }else {
					if(typeof(payment) == "undefined"){
                        $.alert('余额不足以支付，请选择支付方式');
                        return;
					}
                    url = "{{ u('Order/pay',array('id'=>$data['id'])) }}&payment="+payment+"&balancePay="+balancePay;
                } 
                $.router.load(url, true);
            }
        }
        catch (ex)
        {
        }
    }
    // 回调函数
    function PayComplete(result)
    {
        if(result == "Success")
        {
            $.router.load("{{ u('Order/detail',array('id'=>$data['id'])) }}", true);
        }
    }
    
</script>
@stop
