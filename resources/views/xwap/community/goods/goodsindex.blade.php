@extends('xwap.community._layouts.base')

@section('css') 
@stop

@section('show_top') 
    <header class="bar bar-nav">
        <a class="button button-link button-nav pull-left pageloading" href="{{ $nav_back_url}}" data-transition='slide-out'>
            <span class="icon iconfont">&#xe600;</span>
        </a>
        <h1 class="title f16">{{$seller['name']}}</h1>
        <a class="button button-link button-nav pull-right open-popup pageloading" data-popup=".popup-about" href="{{ u('Seller/search') }}">
            <i class="icon iconfont c-gray x-searchico">&#xe65e;</i>
        </a>
    </header>
@stop  

@section('content') 
<?php
$cartgoods = [];

foreach($cart["data"]["goods"] as $good)
{
    $cartgoods[$good["goodsId"]][$good["normsId"]] = $good["num"];
} 
?>  
    <!-- NEW -->
    @include('xwap.community.goods.cartfooter')
    <div class="content" id=''>
        @include('xwap.community.goods.sellergoodshead')
        <!-- 菜单列表 -->
        <div class="x-sjfltab x-goodstab clearfix">
            <div class="buttons-tab fl pr" id="scroll_menu">
                <?php $leftsort = 0; ?>
                @foreach($cate as $ckey => $item)
                    @if(count($item['goods']) > 0)
                        <a href="#tab_{{$ckey}}" class="tab-link button @if($leftsort == 0) active @endif">{{$item['name']}}</a>
                        <?php $leftsort++; ?>
                    @endif
                @endforeach
            </div>
            <div class="tabs c-bgfff fl">
                <?php $leftsort = 0; ?>
                @foreach($cate as $ckey => $item)
                    @if(count($item['goods']) > 0)
                        <div id="tab_{{$ckey}}" class="tab @if($leftsort == 0) active @endif">
                            <div class="list-block media-list x-sortlst f14 ha nobor">
                                <ul>
                                    <li class="item-content active">
                                        <div class="item-inner x-goodstt">
                                            <div class="item-title f15 c-gray">{{$item['name']}}({{count($item['goods'])}})</div>
                                        </div>
                                    </li>
                                    @foreach($item['goods'] as $k=>$v)
                                        <li class="item-content">
                                            <div class="item-inner">
                                                <div class="item-title" onclick="$.href('{{u('Goods/detail',['goodsId'=>$v['id']])}}')">
                                                    <div class="goodspic fl mr5">
                                                        <img src="@if($v['image']) {{ formatImage($v['image'],150,150) }} @else {{ asset('wap/community/client/images/wykdimg.png') }} @endif">
                                                    </div>
                                                    <span class="goodstit">{{$v['name']}}</span>
                                                </div>
                                                @if(count($v['norms']) <= 0)
                                                    <div class="item-text ha mt10">
                                                        <span class="c-red">￥{{number_format($v['price'], 2)}}</span>
                                                        <div class="x-num fr">
                                                            <i class="icon iconfont c-gray subtract fl <?php if(empty($cartgoods[$v['id']][0])) echo "none"; ?>">&#xe621;</i>
                                                            <input type="text" value="<?php if(empty($cartgoods[$v['id']][0])) echo "0"; else echo$cartgoods[$v['id']][0]; ?>" data-goodsid="{{$v['id']}}" data-normsid="0" data-price="{{$v['price']}}" class="val tc pl0 fl <?php if(empty($cartgoods[$v['id']][0])) echo "none"; ?>" readonly="readonly" />
                                                            <i class="icon iconfont c-red add fl">&#xe61e;</i>
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        </li>
                                        @if(count($v['norms']) > 0)
                                            <!-- 有子菜单 -->
                                            <?php $t = 0; ?>
                                            @foreach($v['norms'] as $nk => $n)
                                                <li class="item-content @if($nk >= 3) showgoods none @endif">
                                                    <div class="item-inner goodsnopic">
                                                        <div class="item-title fl mr10">{{$n['name']}}</div>
                                                        <div class="item-after c-red fl">￥{{number_format($n['price'], 2)}}</div>
                                                        <div class="x-num fr mt5 cart_items">
                                                            <i class="icon iconfont c-gray subtract fl <?php if(empty($cartgoods[$v['id']][$n['id']])) echo "none"; ?>">&#xe621;</i>
                                                            <input type="text" value="<?php if(empty($cartgoods[$v['id']][$n['id']])) echo "0"; else echo$cartgoods[$v['id']][$n['id']]; ?>" data-goodsid="{{$v['id']}}" data-normsid="{{ $n['id'] }}" data-price="{{$n['price']}}" class="val tc pl0 fl <?php if(empty($cartgoods[$v['id']][$n['id']])) echo "none"; ?>" readonly="readonly"/>
                                                            <i class="icon iconfont c-red add fl">&#xe61e;</i>
                                                        </div>
                                                    </div>
                                                </li>
                                                @if($t == 3)
                                                <li class="item-content">
                                                    <div class="item-inner x-goodsb">
                                                        <div class="item-title tc">
                                                            展开剩下{{ count($v['norms']) - 3 }}条
                                                            <i class="icon iconfont c-gray f14 ml5 up">&#xe601;</i>
                                                            <i class="icon iconfont c-gray f14 ml5 down none">&#xe603;</i>
                                                        </div>
                                                    </div>
                                                </li>
                                                @endif
                                                <?php $t++; ?>
                                            @endforeach
                                        @endif
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                        <?php $leftsort++; ?>
                    @endif
                @endforeach
            </div>
        </div>
    </div>
@stop 

@section($js)
    <script src="{{ asset('wap/community/client/js/cel.js') }}"></script>
    <script type="text/javascript">
        <?php
        $cartgoods = [];
        
        foreach($cart["data"]["goods"] as $good)
        {
            $cartgoods[$good["goodsId"]][$good["normsId"] ? $good["normsId"] : "null"] = ["num"=>$good["num"], "price"=>$good["price"]];
        }
        
        echo "var cartgoods = ";
        echo json_encode((array)$cartgoods);
        echo ";"
        ?>

        // 处理返回值
        function HandleResult(res)
        {
            if (res.code < 0)
            {
                $.alert("请登录");
                setTimeout(function () { $.router.load("{{u('User/login')}}", true); }, 2000);
            }
            else if (res.code > 0)
            {
                $.alert(res.msg);
            }
        }
        // 减少数量
        $(document).on("touchend", ".subtract", function ()
        {
            var thisVal = $(this);

            var sender = thisVal.siblings(".val");

            var value = parseInt(sender.val()) - 1;

            if (value <= 0)
            {
                value = 0;

                $(this).siblings(".add").siblings().addClass("none");
            } 

            $.post("{{u('Goods/saveCart')}}", { goodsId: sender.data("goodsid"), normsId: sender.data("normsid"), num: value, serviceTime: 0 }, function(res){
                if(res.code == 0){
                    sender.val(value);
                    CalculationTotal(sender.data("goodsid"), sender.data("normsid"), value, parseFloat(sender.data("price")));
                }
                HandleResult(res);
            } );

            
        });
        // 添加数量
        $(document).on("touchend", ".add", function ()
        {
            var thisVal = $(this);

            var sender = thisVal.siblings(".val")

            var value = parseInt(sender.val()) + 1; 

            $.post("{{u('Goods/saveCart')}}", { goodsId: sender.data("goodsid"), normsId: sender.data("normsid"), num: value, serviceTime: 0 }, function(res){
                if(res.code == 0){
                    sender.val(value);
                    CalculationTotal(sender.data("goodsid"), sender.data("normsid"), value, parseFloat(sender.data("price")));
                    thisVal.siblings().removeClass("none"); 
                }
                HandleResult(res);
            } );

        });
        // 计算合计
        function CalculationTotal(goodsid, normsId, num, price)
        {
            if (typeof(cartgoods[goodsid]) == "undefined")
            {
                cartgoods[goodsid] = new Object();
            }

            if (normsId == "0") normsId = "null";

            cartgoods[goodsid][normsId] = { num: num, price: price };

            var totalAmount = 0;

            var totalPrice = 0.0;

            for(var goods in cartgoods)
            {
                for (var item in cartgoods[goods])
                {
                    totalAmount += cartgoods[goods][item].num;
       
                    totalPrice += cartgoods[goods][item].num * cartgoods[goods][item].price;
                }
            }

            $("#cartTotalAmount").html(totalAmount);

            $("#cartTotalPrice").html(totalPrice.toFixed(2));
        }
        $(document).on("touchend",".x-goodsb",function(){
            if ($(this).hasClass("active"))
            {
                $(this).removeClass("active");
                $(this).parent().siblings(".showgoods").addClass("none");
                $(this).find(".up").removeClass("none");
                $(this).find(".down").addClass("none");
            }
            else
            {
                $(this).addClass("active");
                $(this).parent().siblings(".showgoods").removeClass("none");
                $(this).find(".up").addClass("none");
                $(this).find(".down").removeClass("none");
            }
            $(this).parents("li").addClass("none");
        });

    </script>
@stop 