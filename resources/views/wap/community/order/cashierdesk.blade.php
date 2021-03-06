@extends('wap.community._layouts.base')


@section('show_top')
 <div data-role="header" data-position="fixed" class="x-header">
    <h1>收银台</h1>
    <a href="@if(!empty($nav_back_url)) {{ $nav_back_url }} @else javascript:history.back(-1); @endif" data-iconpos="notext" class="x-back ui-nodisc-icon" data-shadow="false"></a>
</div>
@stop
@section('content')
    <div role="main">
        <ul class="y-checkstand f14">
            <li>
                <span class="c-green">商家:</span>
                <span class="fr">{{ $data['sellerName'] }}</span>
            </li>
            <li>
                <span class="c-green">待支付金额:</span>
                <span class="fr c-red">￥{{ $data['payFee']-$data['payMoney'] }}</span>
            </li>
        </ul>
        <p class="y-beizhu c-green"><img src="{{ asset('wap/community/client/images/ico/zffs.png') }}" width="15" height="15">支付方式</p>
        <ul class="y-paylst">
            @if($payments)
                <?php   
                $payment_index = 0;
                $default_payment = '';
                ?> 
                @if($data['user']['balance'] > 0 && $data['payMoney'] <= 0)
                <?php 
                    if($data['user']['balance'] >= $data['payFee']){
                        $default_payment = 'balancePay';
                    }
                ?>

                <li class=" @if($data['user']['balance'] < $data['payFee']) y-zhye @endif on" data-code="balancePay">
                    <img src="{{asset('wap/community/client/images/ico/zf5.png')}}" />
                    <div class="y-payf f16">账户余额可用<span>{{$data['user']['balance']}}</span>元 @if($data['user']['balance'] < $data['payFee']) <p class="f12 c-green balance_tip mt5"> 还需在线支付<span class="c-red">{{ ($data['user']['balance'] >= $data['payFee']) ? 0 : ($data['payFee'] - $data['user']['balance']) }}元</span></p>@endif</div>
                    <form>
                        <select id="is_balance_pay"  data-role="slider">
                            <option value="0" ></option>
                            <option value="1" selected="true"></option>
                        </select>
                    </form>
                </li>
                @endif
                @foreach($payments as $key => $pay)
                <?php
                if (empty($default_payment)){
                    $default_payment = $pay['code'];
                }
                ?>
                <li class="clearfix @if(($pay['code'] == $default_payment) && $data['payFee'] - $data['user']['balance'] > 0) on @endif @if(count($payments) == ($payment_index + 1)) last @endif" data-code="{{ $pay['code'] }}">
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
                            $icon = asset('wap/community/client/images/ico/zf4.png');
                            break;
                    }
                    ?>
                    <img src="{{ $icon }}" />
                    <div class="x-payf">{{ $pay['name'] }}</div>
                    <i></i>
                </li>
                <?php $payment_index ++; ?>
                @endforeach
            @endif
        </ul>
        <div class="y-end">
            <a href="javascript:;" class="ui-btn  x-paybtn">确认支付</a>
        </div>
    </div>
    <script type="text/javascript">
        var payment = "{{ $default_payment }}";
        var balancePay = {{$data['user']['balance'] > 0 ? 1 : 0}}; 
        var isCanBalancePay = {{$data['user']['balance'] >= $data['payFee'] ? 1 : 0}};
        $(".y-paylst li").touchend(function(){
            if(balancePay == 0 || isCanBalancePay == 0){
                $(".y-paylst li").removeClass("on");
                $(this).addClass("on");
                payment = $(this).data("code"); 
            }
        });
        $("#is_balance_pay").change(function(){
            balancePay = $(this).val();
            if(balancePay == 1){
                if(isCanBalancePay == 1){
                    $(".y-paylst li").removeClass("on");
                    payment = 'balancePay';
                }
                $(".balance_tip").show();
                $(".balance_tip").parent().parent().addClass('y-zhye');
            } else {
                $(".balance_tip").hide();
                $(".balance_tip").parent().parent().removeClass('y-zhye'); 
            }
        })
        $(".x-paybtn").on("touchend",function(){ 
            if(payment == 'weixinJs'){
                url = "{{ u('Order/wxpay',array('id'=>$data['id'])) }}&payment="+payment+"&balancePay="+balancePay;
            }else if(payment == 'balancePay'){
                if(balancePay == 1){
                    url = "{{ u('Order/balancepay',array('id'=>$data['id'])) }}&payment="+payment;
                } else {
                    $.showError('请选择支付方式');
                    return;
                }
            }else {
                url = "{{ u('Order/pay',array('id'=>$data['id'])) }}&payment="+payment+"&balancePay="+balancePay;
            }  
            window.location.href = url;
        })
    </script>
@stop


