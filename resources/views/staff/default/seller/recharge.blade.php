@extends('staff.default._layouts.base')
@section('title')
    {{$title}}
@stop

@section('show_top')
    <header class="bar bar-nav">
        <a class="button button-link button-nav pull-left" href="#" onclick="JumpURL('{{ $nav_back_url }}','#seller_account_view',2)" data-transition='slide-out'>
            <span class="icon iconfont">&#xe64c;</span>
        </a>
        <h1 class="title">{{$title}}</h1>
    </header>
@stop

@section('content')
    <div class="content" id=''>
        <div class="x-paymoney mb10">
            <span>充值金额</span>
            <div class="x-sum">
                <input type="number" id="charge" min="1" max="999999">
                <p>充值后可使用余额进行交易支付</p>
            </div>
        </div>
        <div class="content-block-title x-lh40"><i class="icon iconfont mr10">&#xe63c;</i>支付方式</div>
        <ul class="x-paylst">
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
                                $icon = asset('wap/community/client/images/ico/zf4.png');
                                break;
                        }
                        ?>
                        <img src="{{ $icon }}" />
                        <div class="x-payf f16">{{ $pay['name'] }}</div>
                        <i class="icon iconfont">&#xe638;</i>
                    </li>
                    <?php $payment_index ++; ?>
                @endforeach
            @endif
        </ul>
        <p class="x-paybtn"><a class="paybtn">确认充值</a></p>
    </div>
@stop

@section($js)
    <script>
        var payment = "{{ $default_payment }}";
		
	 // 回调函数
		function PayComplete(result)
		{
			if (result == "Success")
			{
				JumpURL("{{u('Seller/account',['type'=>1,'status'=>3])}}","#seller_account_view_3",2);
			}
			// else{
				// JumpURL("{{u('Seller/recharge')}}","#seller_recharge_view",2);			
			// }
		}
	
        $(document).on('click','#{{$id_action.$ajaxurl_page}} .paybtn', function () {
            var money = parseFloat( $("#charge").val() );
            if(money <= 0 || money=='' || isNaN(money)){
                $.toast('充值金额必须是大于0的数字');
                return;
            }
            if (window.App && payment != "balancePay")
            {
                var result = $.ajax({ url: "{{u('Seller/createpaylog')}}?payment=" + payment + "&money=" + money, async: false, dataType: "text" });

                window.App.pay_sdk(result.responseText);
            }
            else
            {
                if (payment == 'weixinJs')
                {
                    $.toast("暂没实现");
                    //window.location.href = "{{ u('Seller/wxpay',array('id'=>$orderId)) }}&payment=" + payment+"&money="+money;
                }
                else
                {
                    var url = "{{ u('Seller/pay',array('id'=>$orderId)) }}&payment=" + payment + "&money=" + money;
                    JumpURL(url);
                }
            }
        });

        $(document).on("click", "#{{$id_action.$ajaxurl_page}} .x-paylst li", function ()
        {
            payment = $(this).data("code");
            $(this).addClass("on").siblings().removeClass("on");
        });
    </script>
@stop
