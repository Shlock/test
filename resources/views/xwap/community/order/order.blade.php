@extends('xwap.community._layouts.base')

@section('show_top')
<header class="bar bar-nav y-barnav">
    <a class="button button-link button-nav pull-left pageloading back" href="@if(!empty($nav_back_url)) {{ $nav_back_url }} @else javascript:$.back(); @endif" data-transition='slide-out'>
        <span class="icon iconfont">&#xe600;</span>
    </a>
    <h1 class="title f16">确认订单</h1>
</header>
@stop

@section('content')
<!-- new -->
<nav class="bar bar-tab">
    <div class="c-bgfff y-pfb">
        <p class="c-gray f14"><span>应付款：<span class="c-red f18">￥{{ $fee['payFee'] }}</span></span><a href="#" id="x-fwcansels" class="fr c-white c-bg y-qrddbtn f16">@if($fee['payFee'] > 0)去支付@else确认下单@endif</a></p>
    </div>
</nav>
<div class="content">
    @if(!empty($address))
        <div class="card y-card active">
            <div class="card-content">
                <div class="card-content-inner">
                    <php>
                        $name = mb_substr($address['name'], 0, 5, "utf-8") . (mb_strlen($address['name'], 'UTF8') > 5 ? "……" : "");
                    </php>
                    <p><span>{{ $name }}</span><span class="fr">{{ $address['mobile'] }}</span></p>
                    <p class="mt5">{{ $address['address'] }}</p>
                    <input type="hidden" name="addressId" id="addressId" value="{{ $address['id'] }}">
                </div>
            </div>
        </div>
    @else
        <div class="c-bgfff pt15 pb15 pl10 pr10 mb10" onclick="$.href('{!! u('UserCenter/address',['cartIds' => $cartIds]) !!}')">
            <div class="f12">
                <span>添加地址</span>
                <i class="icon iconfont fr c-gray">&#xe602;</i>
            </div>
        </div>
    @endif

    <?php  
        $typename = $data[0]['type'] == 1 ? '配送方式' : '服务时间设置';
        $orderType = $data[0]['type'];
    ?>
    @if($orderType == 1)
        <!-- 横排显示 -->
        <div class="list-block media-list mt10 y-qrdd">
            <ul>
                <li>
                  <a href="#" class="item-link item-content">
                    <div class="item-inner">
                      <div class="item-title-row f14">
                        <div class="item-title">
                            @foreach($data as $val)
                                <?php $seller = $val['seller']; 
                                  if(!empty($val['norms'])){
                                    $price += $val['norms']['price'] * $val['num'];
                                  }else{
                                    $price += $val['price'] * $val['num'];
                                  }
                                  $num += $val['num'];
                                  //$id += $val['id'].",";
                                ?>
                                <span><img src="{{formatImage($val['goods']['logo'],100,100)}}"></span>
                            @endforeach
                        </div>
                        <div class="item-after c-gray f12 mt10">共<span class="c-red">{{ $num }}</span>件</div>
                      </div>
                    </div>
                  </a>
                </li>
            </ul>
        </div>
    @else
        <!-- 竖排显示 -->
        <div class="list-block media-list y-sylist">
            <ul>
                @foreach($data as $val)
                <?php 
                    if($seller == ''){
                        $seller = $val['seller'];
                    }
                ?>
                <li>
                    <?php                    
                      $price += $val['price'] * $val['num'];
                    ?>
                    <a href="#" class="item-link item-content">
                        <div class="item-media"><img src="{{formatImage($val['goods']['logo'],100,100)}}" width="55"></div>
                        <div class="item-inner">
                            <div class="item-subtitle y-sytitle mt10">{{ $val['goods']['name'] }}<span class="f12">X{{ $val['num'] }}</span></div>
                            <div class="item-subtitle mb10 mt10 y-f14">
                                <span class="c-red">￥{{ $val['price'] }}</span>
                            </div>
                        </div>
                    </a>
                </li>
                @endforeach
            </ul>
        </div>
    @endif
    <div class="content-block-title f14 c-gray">
        <i class="icon iconfont">&#xe606;</i>
        {{ $typename }}
    </div>
    <div class="list-block media-list y-iteminnerp">
        <ul>
            @if($orderType == 1)
                <li>
                  <a href="#" class="item-link item-content">
                    <div class="item-inner">
                      <div class="item-title-row f14">
                        <div class="item-title">配送方式:<span  id="freType" class="y-right">商家配送</span></div>
                      </div>
                    </div>
                  </a>
                </li>
            @endif
            <li>
              <a href="#" class="item-link item-content">
                <div class="item-inner">
                  <div class="item-title-row f14">
                    <div class="item-title">@if($orderType == 1)配送@else服务@endif时间：<input type="text" name="beginTime" id="beginTime" class="y-date"></div>
                    <div class="item-after c-gray"><i class="icon iconfont">&#xe602;</i></div>
                  </div>
                </div>
              </a>
            </li>
        </ul>
    </div>
    <div class="content-block-title f14 c-gray">
        <i class="icon iconfont">&#xe642;</i>
        其他
    </div>
    <div class="list-block media-list y-qrddqt">
        <ul>
           <!--  <li>
                <a href="#" class="item-link item-content">
                    <div class="item-inner">
                        <div class="item-title-row f14">
                            <div class="item-title">是否添加贺卡</div>
                            <div class="item-after c-gray"><i class="icon iconfont f20">&#xe611;</i></div>
                        </div>
                        <div class="item-title-row f14">
                            <input type="text" placeholder="请填写贺卡内容" class="y-qrddinput">
                        </div>
                    </div>
                </a>
            </li>
            <li>
                <a href="#" class="item-link item-content">
                    <div class="item-inner">
                        <div class="item-title-row f14">
                            <div class="item-title">是否需要发票</div>
                            <div class="item-after c-red"><i class="icon iconfont f20">&#xe611;</i></div>
                        </div>
                        <div class="item-title-row f14">
                            <input type="text" placeholder="请填写贺卡内容" class="y-qrddinput">
                        </div>
                    </div>
                </a>
            </li> -->
            <li>
                <a href="#" class="item-link item-content">
                    <div class="item-inner">
                        <div class="item-title-row f14">
                            <div class="item-title">备注</div>
                        </div>
                        <div class="item-title-row f14">
                            <input type="text" name="buyRemark" id="buyRemark" value="" class="y-qrddinput" placeholder="请填写备注信息(非必填)">
                        </div>
                    </div>
                </a>
            </li>
        </ul>
    </div>
    <div class="content-block-title f14 c-gray">
        <i class="icon iconfont">&#xe62d;</i>
        结算
    </div>
    <div class="list-block media-list y-iteminnerp">
        @if($orderType == 1)
            <ul>
                <li>
                  <a href="#" class="item-link item-content">
                    <div class="item-inner">
                      <div class="item-title-row f14">
                        <div class="item-title">商品价格:</div>
                        <div class="item-after c-black">￥{{ $fee['goodsFee'] }}</div>
                      </div>
                    </div>
                  </a>
                </li>
                @if($fee['isShowPromotion'] == 1)
                    <li>
                      <a href="{{ u('Coupon/usepromotion',['cartIds' => $cartIds,'addressId'=>(int)Input::get('addressId'),'sellerId'=>$fee['sellerId'],'money'=>$fee['totalMoney']]) }}" class="item-link item-content pageloading">
                        <div class="item-inner">
                          <div class="item-title-row f14">
                            <div class="item-title">优&nbsp;惠&nbsp;券:</div>
                            <div>
                                @if($fee['discountFee'] > 0)
                                    <div class="item-after c-red fl">-{{ $fee['discountFee'] }}</div>
                                @elseif($fee['promotionCount'] > 0)
                                    <div class="item-after c-black fl">可选择优惠券</div>
                                @else
                                    <div class="item-after c-black fl">无可用优惠券</div>
                                @endif
                                <i class="icon iconfont">&#xe602;</i>
                            </div>
                          </div>
                        </div>
                      </a>
                    </li>
                @endif
                <li>
                  <a href="#" class="item-link item-content">
                    <div class="item-inner">
                      <div class="item-title-row f14">
                        <div class="item-title">运&nbsp;费:</div>
                        <div class="item-after c-black">￥{{ $fee['freight'] }}</div>
                      </div>
                    </div>
                  </a>
                </li>
                <li>
                  <a href="#" class="item-link item-content">
                    <div class="item-inner">
                      <div class="item-title-row f14">
                        <div class="item-title">合&nbsp;计:</div>
                        <div class="item-after c-red">￥{{ $fee['totalFee'] }}</div>
                      </div>
                    </div>
                  </a>
                </li>
            </ul>
        @else
            <ul>
                <li>
                  <a href="#" class="item-link item-content">
                    <div class="item-inner">
                      <div class="item-title-row f14">
                        <div class="item-title">服务费用:</div>
                        <div class="item-after c-black">￥{{ $fee['goodsFee'] }}</div>
                      </div>
                    </div>
                  </a>
                </li>
                @if($fee['isShowPromotion'] == 1)
                    <li>
                      <a href="{{ u('Coupon/usepromotion',['cartIds' => $cartIds,'addressId'=>(int)Input::get('addressId'),'sellerId'=>$fee['sellerId'],'money'=>$fee['totalMoney']]) }}" class="item-link item-content pageloading">
                        <div class="item-inner">
                          <div class="item-title-row f14">
                            <div class="item-title">优&nbsp;惠&nbsp;券:</div>
                            @if($fee['discountFee'] > 0)
                                <div class="item-after c-red">-{{ $fee['discountFee'] }}</div>
                            @elseif($fee['promotionCount'] > 0)
                                <div class="item-after c-black">可选择优惠券</div>
                            @else
                                <div class="item-after c-black">无可用优惠券</div>
                            @endif
                            <i class="icon iconfont">&#xe602;</i>
                          </div>
                        </div>
                      </a>
                    </li>
                @endif
                <li>
                  <a href="#" class="item-link item-content">
                    <div class="item-inner">
                      <div class="item-title-row f14">
                        <div class="item-title">合&nbsp;计:</div>
                        <div class="item-after c-red">￥{{ $fee['totalFee'] }}</div>
                      </div>
                    </div>
                  </a>
                </li>
            </ul>
        @endif
    </div>
    @if($fee['payFee'] > 0)
        <div class="content-block-title f14 c-gray"><i class="icon iconfont mr10">&#xe638;</i>支付方式</div>
        <ul class="y-paylst mb10">
            <li class="on" data-code="1">
                <div class="y-payf f16">在线支付</div>
                <i class="icon iconfont">&#xe612;</i>
            </li>
            @if($fee['isCashOnDelivery'] == 1)
            <li data-code="0">
                <div class="y-payf f16">货到付款</div>
                <i class="icon iconfont">&#xe612;</i>
            </li>
            @endif
        </ul>
        <div class="content-block-title f14 c-gray">
            <i class="icon iconfont vat">&#xe646;</i>
            请在下单后{{ $time }}分钟内完成支付。
        </div>
    @endif
</div>
<script type="text/javascript">
    $.orderTime();
</script>
@stop

@section($js)
<script type="text/javascript">
    var payment = "1";
    $(document).on("touchend",".y-paylst li",function(){
        $(this).addClass("on").siblings().removeClass("on"); 
        payment = $(this).data("code");
        if(payment == 0) {
            $("#x-fwcansels").html("确认下单");
        }else{
            $("#x-fwcansels").html("去支付");
        }
    });
    $(document).on("click","#x-fwcansels",function(){
        var addressId     = $("input[name=addressId]").val();
        var freType       = $("#freType").text();
        var appTime       = $("input[name=beginTime]").val();
        var orderType     = "{{$orderType}}";
        var invoiceRemark = $("input[name=invoiceRemark]").val();
        var buyRemark     = $("input[name=buyRemark]").val();
        var giftRemark    = $("input[name=giftRemark]").val();
        var id           = "{{ $cartIds }}";
        var promotionSnId        = "{{ $promotion['id'] }}";
        var obj ={
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
        $.showPreloader('正在创建订单...');
        $.post("{{ u('Order/toOrder') }}",obj,function(res){
            $.hidePreloader();
            if(res.code == 0) {
                $(".x-tksure").addClass("none");
                if (res.data.payStatus == "{{ ORDER_PAY_STATUS_YES }}" || payment == 0) {
                    $.alert(res.msg);
                    $.router.load("{{ u('Order/detail',array('id'=>ids)) }}".replace("ids", res.data.id), true);
                }else{
                    $.alert(res.msg + "进入第三方支付");
                  // window.location.href = "{{ u('Order/orderpay',array('orderId'=>ids)) }}".replace("ids", res.data.id);
                    $.router.load("{{ u('Order/cashierdesk',array('orderId'=>ids)) }}".replace("ids", res.data.id), true);
                }
            }else{
                $.alert(res.msg);
            }
        },"json")
       
    });
    $(document).on("touchend",".y-qrddqt li p span",function(){
        if($(this).parents("li").hasClass("on")){
            $(this).parents("li").removeClass("on");
        }else{
            $(this).parents("li").addClass("on");
        }
    });

    // $(document).on("click", ".y-paylst li", function(){
    //     if($(this).hasClass("on")){
    //         $(this).removeClass("on");
    //     }else{
    //         $(this).addClass("on").siblings().removeClass("on");
    //     }
    // });
    // 

    $.orderTime = function() {
        $("#beginTime").datetimePicker({
            value: ["{{Time::toDate(UTC_TIME+1800,'Y')}}", "{{Time::toDate(UTC_TIME+1800,'m')}}", "{{Time::toDate(UTC_TIME+1800,'d')}}", "{{ intval(Time::toDate(UTC_TIME+1800,'H')) }}", "{{Time::toDate(UTC_TIME+1800,'i')}}"]
        });
    }

    $.orderTime();
</script>
@stop