@extends('wap.community._layouts.base_order')
@section('css')
<style>
    #y-right div{border-color:#fff;margin:0px;overflow:hidden;height:25px;}
    .y-paylst a{color:#fff;}
    .scc li div.y-ddcontent {padding: 0px;}
    .scc li {border: 0px solid #e6e6e6;}
    .x-addadr{line-height:45px;padding:0 10px;}
    .y-ddxx y-bortop .ui-btn{background-color:initial;}
    .box{display: -webkit-box;display: -moz-box;display: -ms-box;display: box;display:flex;-moz-webkit-orient:horizontal; -webkit-box-orient:horizontal;}
    .flex1{-webkit-box-flex:1;-moz-box-flex:1;-ms-box-flex:1;flex:1;}
</style>
@stop
@section('js')

@stop
@section('show_top')
<div data-role="header" data-position="fixed" class="x-header">
    <h1>确认订单</h1>
    <a href="@if(!empty($nav_back_url)) {{ $nav_back_url }} @else javascript:history.back(-1); @endif" data-iconpos="notext" class="x-back ui-nodisc-icon" data-shadow="false"></a>
</div>
@stop
@section('content')
<div data-role="content" class="d-content">
    @if(!empty($address))
    <div class="y-khxx" onclick="window.location.href = '{!! u("UserCenter/address",["cartIds" => $cartIds,"proId"=>(int)Input::get('proId')]) !!}'">
         <div>
            <span>{{ $address['name'] }}</span><span class="tel">{{ $address['mobile'] }}</span>
            <p>{{ $address['address'] }} </p>
            <input type="hidden" name="addressId" id="addressId" value="{{ $address['id'] }}">
        </div>
    </div>
    @else
    <div class="x-addadr" onclick="window.location.href = '{!! u("UserCenter/address",["cartIds" => $cartIds]) !!}'">暂无地址，点击添加<i class="x-rightico fr" style="margin-top:16px;"></i></div>
    @endif
</div>
<div role="main" id="isCard" data-iscard="{{$fee[isCard]}}" data-isbind="{{$fee[isBind]}}">
    <?php
    $typename = $data[0]['type'] == 1 ? '配送方式' : '服务时间设置';
    $orderType = $data[0]['type'];
    ?>
    @if($orderType == 1)
    <ul class="y-wdddmain">
        <li>
            <div class="y-ddcontent" style="border:0">
                <div class="y-imgcont">
                    @foreach($data as $val)
                    <?php
                    $seller = $val['seller'];
                    if (!empty($val['norms'])) {
                        $price += $val['norms']['price'] * $val['num'];
                    } else {
                        $price += $val['price'] * $val['num'];
                    }
                    $num += $val['num'];
                    //$id += $val['id'].",";
                    ?>
                    <div><img src="{{formatImage($val['goods']['logo'],100,100)}}" height="43"></div>
                    @endforeach
                </div>
                <i class="c-green">共<span class="c-red">{{ $num }}</span>件</i>
            </div>
        </li>
    </ul>
    @else     
    <div class="y-cont">
        <ul>
            @foreach($data as $val)
            <?php
            if ($seller == '') {
                $seller = $val['seller'];
            }
            ?>
            <li>
                <?php
                $price += $val['price'] * $val['num'];
                ?>
                <div class="y-img"><img src="{{formatImage($val['goods']['logo'],100,100)}}" height="43"></div>
                <div class="y-ddxqtext">
                    <p class="f16">{{ $val['goods']['name'] }}<span class="f12">X{{ $val['num'] }}</span></p>
                    <h3 class="c-red f14">￥{{ $val['price'] }}</h3>
                </div>
            </li>
            @endforeach
        </ul>
    </div>   
    @endif
    <p class="y-beizhu c-green" id="block_delivery_title"><img src="{{ asset('wap/community/client/images/ico/psfs.png') }}" width="20" height="15">{{ $typename }}</p>
    <div class="y-cont" id="block_delivery">
        <div class="y-ddxx y-bortop">
            @if($orderType == 1)
            <div class="y-item1">
                <p style="line-height:20px;height:20px;"><span class="y-left">配送方式：</span></p>
            </div>
            <div class="box" id="delivery_type" data-type="seller" style="margin-right:10px;">
                <div class="flex1">
                    <button class="ui-btn ui-btn-a delivery">商家配送</button>
                </div>
                <div class="flex1">
                    <button class="ui-btn ui-btn-b delivery">我要自提</button>
                </div>
            </div>
            @endif
            <div class="y-item2" id="delivery_time" style="max-height: 53px;border:0;overflow:hidden;">
                <p>
                    <span class="y-left"> @if($orderType == 1)配送@else服务@endif时间：</span>
                    <span class="y-right" id="y-right">
                        <input type="datetime-local" name="beginTime" class="d-date" min="{{ Time::toDate(UTC_TIME+10*60,'Y-m-d\TH:i') }}" id="beginTime" value="{{ Time::toDate(UTC_TIME+10*60,'Y-m-d\TH:i') }}" style="min-height:0px;padding-top:10px;" data-options="{'mode': 'datebox'}">
                    </span>
                    <b class="x-rightico"></b>
                </p>
            </div>
        </div>
    </div>
    <p class="y-beizhu c-green" id="block_note_title"><img src="{{ asset('wap/community/client/images/ico/qt.png') }}" width="15" height="15">其他</p>
    <ul class="y-qrddqt" id="block_note">
        <!--  <li>
             <p class="f14">是否添加贺卡</p>
             <input type="text" name="giftRemark" id="giftRemark" value="" placeholder="填写贺卡内容(非必填)">
         </li>
         <li>
             <p class="f14">是否需要发票</p>
             <input type="text" name="invoiceRemark" id="invoiceRemark" value="" placeholder="请填写发票抬头(非必填)">
         </li>-->
        <li>
            <p class="f14">备注</p>
            <input type="text" name="buyRemark" id="buyRemark" value="" placeholder="请填写备注信息(非必填)">
        </li>
    </ul>
    <p class="y-beizhu c-green"><img src="{{ asset('wap/community/client/images/ico/js.png') }}" width="17" height="17">结算</p>
    <div class="y-cont">
        @if($orderType == 1)
        <div class="y-ddxx y-bortop">
            <div class="y-item2">
                <p><span>商品价格：</span><span class="y-zfjg">￥{{ $fee['goodsFee'] }}</span></p>
            </div>
            @if($fee['isShowPromotion'] == 1)
            <div class="y-item2" id="btnProm" data-url="{{ u("Coupon/usepromotion",["cartIds" => $cartIds,"addressId"=>(int)Input::get('addressId'),'sellerId'=>$fee['sellerId'],'money'=>$fee['totalMoney']]) }}">
                <p><span>优惠券：</span>
                    @if($fee['discountFee'] > 0)
                    <span class="y-zfjg c-red canbe">-{{ $fee['discountFee'] }}</<i class="x-rightico"></i>
                        @elseif($fee['todayUsed'] == 'true')
                        <span class="y-zfjg">当日优惠券限用一张，您已使用<i class="x-rightico"></i>
                            @elseif($fee['promotionCount'] > 0)
                            <span class="y-zfjg canbe">可选择优惠券<i class="x-rightico"></i>
                                @else
                                <span class="y-zfjg">无可用优惠券<i class="x-rightico"></i>
                                    @endif
                                </span>
                                </p>
                                </div>
                                @endif
                                <div class="y-item2" id="delivery_fee">
                                    <p id="yunfei" data-fee="{{ $fee['freight'] }}"><span>运费：</span><span class="y-zfjg">￥{{ $fee['freight'] }}</span></p>
                                </div>
                                <div class="y-item2">
                                    <p><span>合计：</span><span class="c-red y-zfjg" id="heji">￥{{  $fee['totalFee'] }}</span></p>
                                </div>
                                </div>
                                @else
                                <div class="y-ddxx y-bortop">
                                    <div class="y-item2">
                                        <p><span>服务费用：</span><span class="y-zfjg">￥{{ $fee['goodsFee'] }}</span></p>
                                    </div>
                                    @if($fee['isShowPromotion'] == 1)
                                    <div class="y-item2" onclick="window.location.href ='{{ u("Coupon/usepromotion",["cartIds" => $cartIds,"addressId"=>(int)Input::get('addressId'),'sellerId'=>$fee['sellerId'],'money'=>$fee['totalMoney']]) }}'">
                                        <p><span>优惠券：</span>
                                            @if($fee['discountFee'] > 0)
                                            <span class="y-zfjg c-red">-{{ $fee['discountFee'] }}<i class="x-rightico"></i>
                                                @elseif($fee['promotionCount'] > 0)
                                                <span class="y-zfjg">可选择优惠券<i class="x-rightico"></i>
                                                    @else
                                                    <span class="y-zfjg">无可用优惠券<i class="x-rightico"></i>
                                                        @endif
                                                    </span>
                                                    </p>
                                                    </div>
                                                    @endif
                                                    <div class="y-item2">
                                                        <p><span>合计：</span><span class="c-red y-zfjg" >￥{{ $fee['totalFee'] }}</span></p>
                                                    </div>
                                                    </div>
                                                    @endif
                                                    </div>
                                                    @if($fee['payFee'] > 0)
                                                    <p class="y-beizhu c-green"><img src="{{ asset('wap/community/client/images/ico/zffs.png') }}" width="15" height="15">支付方式</p>
                                                    <ul class="y-paylst">

                                                        <li class="on" data-code="1" style="padding:0.5em 0em;">
                                                            <div class="y-payf f16">在线支付</div>
                                                            <i></i>
                                                        </li>
                                                        @if($fee['isCashOnDelivery'] == 1)
                                                        <li class="last" data-code="0">
                                                            <div class="y-payf f16">货到付款</div>
                                                            <i></i>
                                                        </li>
                                                        @endif
                                                    </ul>
                                                    <p class="y-beizhu c-green"><img src="{{ asset('wap/community/client/images/ico/ts.png') }}" width="15" height="15">请在下单后{{ $time }}分钟内完成支付。</p>
                                                    @endif
                                                    @if($fee['isCard'] == 1 && $fee['isBind'] == 0)
                                                    <ul class="y-qrddqt">
                                                        <li>
                                                            <p class="f14">第一次在线购卡，需输入会员卡号</p>
                                                            <input type="text" name="buyCard" id="buyCard" value="" placeholder="请输入12位会员卡卡号">
                                                        </li>
                                                    </ul>
                                                    @endif
                                                    <div class="y-btn">
                                                        <p class="f14 c-green">应付款：<span class="c-red f18" id="yingfu" data-fee="{{ $fee['payFee'] }}">￥{{ $fee['payFee'] }}</span></p><a href="javascript:;" id="x-fwcansels" style="color:#fff">@if($fee['payFee'] > 0)去支付@else确认下单@endif</a>
                                                    </div>
                                                    </div>
                                                    <script type="text/javascript">
                                                                var payment = "1";
                                                                $(".y-paylst li").touchend(function(){
                                                        $(this).addClass("on").siblings().removeClass("on");
                                                                payment = $(this).data("code");
                                                                if (payment == 0) {
                                                        $("#x-fwcansels").html("确认下单");
                                                        } else{
                                                        $("#x-fwcansels").html("去支付");
                                                        }
                                                        });
                                                                $(document).on("touchend", "#x-fwcansels", function(){
                                                        var cardno = 0;
                                                                if ($('#isCard').data('iscard') * 1 === 1 && $('#isCard').data('isbind') * 1 === 0) {
                                                        if ($('#buyCard').val().length != 12) {
                                                        $('#buyCard').attr('placeholder', '请输入正确的会员卡号');
                                                                $('#buyCard').css({color:'red'});
                                                                return false;
                                                        }
                                                        cardno = $('#buyCard').val();
                                                        }
                                                        var addressId = $("input[name=addressId]").val();
                                                                var freType = $("#freType").text();
                                                                var appTime = $("input[name=beginTime]").val();
                                                                if ($('#delivery_type').data('type') == 'myself') {
                                                        appTime = 0;
                                                        }
                                                        var orderType = "{{$orderType}}";
                                                                var invoiceRemark = $("input[name=invoiceRemark]").val();
                                                                var buyRemark = $("input[name=buyRemark]").val();
                                                                var giftRemark = $("input[name=giftRemark]").val();
                                                                var id = "{{ $cartIds }}";
                                                                var promotionSnId = "{{ $promotion['id'] }}";
                                                                var obj = {
                                                                cardNo : cardno,
                                                                        addressId: addressId,
                                                                        freType:freType,
                                                                        appTime:appTime,
                                                                        orderType:orderType,
                                                                        invoiceTitle:invoiceRemark,
                                                                        buyRemark:buyRemark,
                                                                        giftContent:giftRemark,
                                                                        cartIds:id,
                                                                        payment:payment,
                                                                        promotionSnId:promotionSnId
                                                                };
                                                                $.post("{{ u('Order/toOrder') }}", obj, function(res){
                                                                if (res.code == 0) {
                                                                $(".x-tksure").addClass("none");
                                                                        if (res.data.payStatus == "{{ ORDER_PAY_STATUS_YES }}" || payment == 0) {
                                                                $.showSuccess(res.msg);
                                                                        window.location.href = "{{ u('Order/detail',array('id'=>ids)) }}".replace("ids", res.data.id);
                                                                } else{
                                                                $.showSuccess(res.msg + "进入第三方支付");
                                                                        // window.location.href = "{{ u('Order/orderpay',array('orderId'=>ids)) }}".replace("ids", res.data.id);
                                                                        window.location.href = "{{ u('Order/cashierdesk',array('orderId'=>ids)) }}".replace("ids", res.data.id);
                                                                }
                                                                } else{
                                                                $.showError(res.msg);
                                                                }
                                                                }, "json")
                                                                //console.log(obj);

                                                        });
                                                                $(".y-qrddqt li p span").touchend(function(){
                                                        if ($(this).parents("li").hasClass("on")){
                                                        $(this).parents("li").removeClass("on");
                                                        } else{
                                                        $(this).parents("li").addClass("on");
                                                        }
                                                        });
                                                                $('#btnProm .canbe').on('click', function(){
                                                        window.location.href = $('#btnProm').data('url');
                                                        });
                                                                $('.flex1').on('click', function(){
                                                        console.log($(this).index());
                                                                if ($(this).index() === 0){
                                                        $('#yingfu,#heji').html('￥' + $('#yingfu').data('fee'));
                                                                cacheSet('delivery', 'seller');
                                                                $('#delivery_fee,#delivery_time').show();
                                                                $('#delivery_type').data('type', 'seller');
                                                                $(this).find('button').removeClass('ui-btn-b').addClass('ui-btn-a');
                                                                $('.flex1').eq(1).find('button').removeClass('ui-btn-a').addClass('ui-btn-b');
                                                        } else {
                                                        $('#yingfu,#heji').html('￥' + ($('#yingfu').data('fee') - $('#yunfei').data('fee')));
                                                                cacheSet('delivery', 'myself');
                                                                $('#delivery_fee,#delivery_time').hide();
                                                                $('#delivery_type').data('type', 'myself');
                                                                $(this).find('button').removeClass('ui-btn-b').addClass('ui-btn-a');
                                                                $('.flex1').eq(0).find('button').removeClass('ui-btn-a').addClass('ui-btn-b');
                                                        }
                                                        });
                                                                if (cacheGet('delivery') == 'myself') {
                                                        $('#yingfu,#heji').html('￥' + ($('#yingfu').data('fee') - $('#yunfei').data('fee')));
                                                                $('.flex1').eq(1).find('button').trigger('click');
                                                        } else {
                                                        $('#yingfu,#heji').html('￥' + $('#yingfu').data('fee'));
                                                                $('.flex1').eq(0).find('button').trigger('click');
                                                        }
                                                        if ($('#isCard').data('iscard') * 1 === 1) {
                                                        $('#yingfu,#heji').html('￥' + ($('#yingfu').data('fee') - $('#yunfei').data('fee')));
                                                                $('.flex1').eq(1).find('button').trigger('click');
                                                                $('#btnProm,#block_delivery,#block_delivery_title,#block_note,#block_note_title').hide();
                                                        }
                                                        function cacheSet(key, val) {
                                                        if (window.localStorage){
                                                        return localStorage.setItem(key, val);
                                                        }
                                                        console.log('no');
                                                                return false;
                                                        }
                                                        function cacheGet(key) {
                                                        if (window.localStorage){
                                                        return localStorage.getItem(key);
                                                        }
                                                        console.log('no');
                                                                return false;
                                                        }
                                                    </script>
                                                    @stop