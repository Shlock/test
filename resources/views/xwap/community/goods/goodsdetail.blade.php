@extends('xwap.community._layouts.base')

@section('css') 
@stop

@section('show_top') 
    <header class="bar bar-nav">
        <a class="button button-link button-nav pull-left back" href="@if(!empty($nav_back_url)) {{ $nav_back_url }} @else javascript:$.back(); @endif" data-transition='slide-out'>
            <span class="icon iconfont">&#xe600;</span>
        </a>
        <h1 class="title f16">{{$data['name']}}</h1>
        <a class="button button-link button-nav pull-right open-popup collect_it @if($data['iscollect']) on @endif" data-id="{{$data['id']}}" data-popup=".popup-about" href="#">
            @if($data['iscollect'])
                <i class="icon iconfont c-red x-searchico">&#xe652;</i><!-- 已收藏图片  -->
            @else
                <i class="icon iconfont c-gray x-searchico">&#xe651;</i><!-- 未收藏图标 -->
            @endif
        </a>
    </header>
@stop  

@section('content')
    <!-- <div role="main" class="ui-content">
        <div class="x-topic" style="border-bottom:1px solid #ddd;">
            <img src="{{$data['images'][0]}}" />
        </div>
        <div class="x-brbg" style="margin-top:0;border-top:0;">
            <div class="f14 clearfix">
                {{$data['name']}}
                @if(empty($data['norms'])) 
                <div class="x-num fr" data-gid="{{$data['id']}}">
                    <i class="jia cart"></i>
                    <div class="numr"  style=" @if($cart['data']['num'] > 0) display: block; @else display:none; @endif position: relative;margin-right: 25px;" >
                        <span class="count c-green"> {{$cart['data']['num']}}</span>
                        <i class="jian cart"></i>
                    </div>
                </div> 
                @endif
            </div>
            <p class="mt10 mb15">
                <b class="c-red mr10 current_price">￥ {{ $data['price']}}</b>
                <span class="c-green f14">{{$data['seller']['name']}}</span>
                @if($data['salesCount'] > 0)
                <span class="c-green fr f12" style="line-height: 25px;margin-right:10px; ">已售 {{$data['salesCount']}}</span>
                @endif
            </p>
        </div>
        <ul class="x-pdelst">
            @if(!empty($data['norms']))
            <li class="x-tosize norms_choose clearfix" >
                <span class="fl">选择规格</span>
                <span class="x-pdelstr current_norms"> @foreach($data['norms'] as $key => $item) @if($item['id'] == $cart['data']['normsId']) {{ $item['name'] }} @endif @endforeach </span>
                <i class="x-rightico fr"></i>
            </li> 
            @endif
        </ul>
        <div class="x-lh45" onclick="window.location.href='{!!$data['url']!!}'">商品描述</div>
        <div class="x-pdel">
            <p>{!!$data['brief']!!}</p>
        </div>
    </div> -->
    <!-- 规格弹框 -->
    <!-- <div class="f-bgtk none size-frame">
        <div class="x-closebg">
            <div class="x-probox">
                <div class="x-prott">
                    <div class="x-propic">
                        <img src="{{$data['logo']}}" />
                    </div>
                    <div class="x-prottr">
                        <p class="c-red pt5">￥<span class="norms_price">{{(int)$cart['data']['price']}}</span></p>
                        <p class="f12 c-green">库存<span class="norms_stock">{{(int)$cart['data']['stock']}}</span>件</p>
                        <p class="f12">请选择 @foreach($data['norms'] as $key => $item) {{ $item['name'] }} @endforeach</p>
                        <i class="x-closeico"></i>
                    </div>
                </div>
                @if(!empty($data['norms']))
                <div class="x-prott">
                    <p class="f14">规格</p>
                    <div class="x-psize clearfix c-green">
                        @foreach($data['norms'] as $key => $item) 
                        <span class="@if($cart['data']['normsId'] == $item['id']) c-bg @endif norms_item" data-info="{{ json_encode($item) }}" >{{$item['name']}}</span>
                        @endforeach
                    </div>
                </div>
                @endif
                <div class="x-pnum">
                    <span class="f14">购买数量</span>
                    <div class="fr x-num">
                        <i class="jia"></i>
                        <span class="count norms_amount c-green">@if($cart['data']['num'] > 0) {{$cart['data']['num']}} @else 0 @endif</span>
                        <i class="jian"></i>
                    </div>
                </div>
                <div class="x-pbtn">
                    <button class="join ui-btn cart_join">加入购物车</button>
                    <button class="join ui-btn c-bg cart_buy_now">立即购买</button>
                </div>
            </div>
        </div>
    </div> -->
    
    <!-- new -->
    @include('xwap.community.goods.cartfooter') 
    <div class="content" id=''>
        <div class="x-bigpic pr">
            <img src="{{$data['images'][0]}}" class="w100 vab" />
            <!-- <i class="icon iconfont c-white x-shareico">&#xe615;</i> -->
        </div>
        <!-- 选择数量 -->
        <div class="list-block x-goods nobor">
            <ul>
                <li class="item-content">
                    <div class="item-inner">
                        <div class="item-title f14">{{$data['name']}}</div>
                        <!-- <div class="item-after fr">
                            <i class="icon iconfont c-gray subtract">&#xe621;</i>
                            <input type="text" value="{{$cart['data']['num']}}" class="val tc pl0" />
                            <i class="icon iconfont c-red add">&#xe61e;</i>
                        </div> -->
                    </div>
                </li>
                <li class="item-content">
                    <div class="item-inner">
                        <div class="item-title">
                            <span class="c-red f18 mr10">￥{{ $data['price']}}</span>
                            <span class="c-gray f14">{{$data['seller']['name']}}</span>
                            @if($data['salesCount'] > 0)
                            <!-- 后期未涉及 -->
                            <!-- <span class="c-green fr f12" style="line-height: 25px;margin-right:10px; ">已售 {{$data['salesCount']}}</span> -->
                            @endif
                        </div>
                    </div>
                </li>
            </ul>
        </div>
        @if(!empty($data['norms']))
        <!-- 选择规格 -->
        <div class="list-block f14 nobor">
            <ul>
                <li class="item-content active norms_choose">
                    <div class="item-inner">
                        <div class="item-title">选择规格
                            <span class="x-pdelstr current_norms ml20"> 
                                @foreach($data['norms'] as $key => $item) @if($item['id'] == $cart['data']['normsId']) {{ $item['name'] }} @endif @endforeach 
                            </span>
                        </div>
                        <i class="icon iconfont c-gray f13">&#xe602;</i>
                    </div>
                </li>
            </ul>
        </div>
        @endif
        <!-- 商品详情 -->
        <div class="content-block-title f14 c-black">商品详情</div>
        <div class="c-bgfff pb10">
            <p class="p10">{!!$data['brief']!!}</p>
        </div>
    </div>
    <!-- 规格弹框 -->
    <div class="f-bgtk size-frame none">
        <div class="x-closebg">
            <div class="x-probox c-bgfff">
                <div class="x-prott pr">
                    <div class="x-propic">
                        <img src="{{$data['logo']}}" />
                    </div>
                    <div class="x-prottr">
                        <p class="c-red pt5 f18">￥<span class="norms_price">{{(int)$cart['data']['price']}}</span></p>
                        <p class="f12 c-gray">库存<span class="norms_stock">{{(int)$cart['data']['stock']}}</span>件</p>
                        <p class="f12">请选择 @foreach($data['norms'] as $key => $item) {{ $item['name'] }} @endforeach</p>
                        <i class="icon iconfont x-closeico c-gray">&#xe604;</i>
                    </div>
                </div>
                @if(!empty($data['norms']))
                <div class="x-prott pr">
                    <p class="f14">规格</p>
                    <div class="x-psize clearfix c-gray f12">
                        @foreach($data['norms'] as $key => $item) 
                            <span class="@if($cart['data']['normsId'] == $item['id']) c-bg @endif norms_item" data-info="{{ json_encode($item) }}">{{$item['name']}}</span>
                        @endforeach
                    </div>
                </div>
                @endif
                <div class="x-pnum pl10 pr10">
                    <span class="f14">购买数量</span>
                    <div class="fr x-num">
                        <i class="icon iconfont c-gray subtract">&#xe621;</i>
                        <input type="text" value="@if($cart['data']['num'] > 0) {{$cart['data']['num']}} @else 0 @endif" class="val tc pl0 count norms_amount c-green" readonly/>
                        <i class="icon iconfont c-red add">&#xe61e;</i>
                    </div>
                </div>
                <div class="x-pbtn c-white">
                    <button class="join f16 cart_join">加入购物车</button>
                    <button class="join f16 c-bg cart_buy_now">立即购买</button>
                </div>
            </div>
        </div>
    </div>
@stop

@section($js) 
    <!-- <script src="{{ asset('static/infinite-scroll/jquery.infinitescroll.js') }}"></script> -->
    <script src="{{ asset('js/dot.js') }}"></script>
    <script src="http://m.sui.taobao.org/assets/js/config.js"></script>
    <script src="http://m.sui.taobao.org/assets/js/demos.js"></script>

    <script type="text/javascript">
    var serviceFee = "{{ $seller['serviceFee'] }}";
    $(document).on("touchend",".choose_complet",function(){
        if(!$(this).hasClass("no-click")){
            $.router.load("{{u('GoodsCart/index')}}", true);
        }else{
            return false;
        }
    });

    // 数量加载
    $(document).on("touchend",".subtract",function(){
        var val = $(this).siblings(".val").val();
        val = parseInt(val) - 1;
        if(val <= 0){
            val = 1;
            $.alert('数量必须大于0！');
        }
        $(this).siblings(".val").val(val);
    });
    $(document).on("touchend",".add",function(){
        var val = $(this).siblings(".val").val();
        val = parseInt(val) + 1;
		var norms =  JSON.parse($(".norms_item.c-bg").data('info'));
        if (val >  parseInt(norms.stock)) {
            $.alert('商品库存不足');
            return;
        }
        $(this).siblings(".val").val(val);
    });

    function toLogin(){
        $.router.load("{{u('User/login')}}", true);
    }
    $(document).on("touchend",".norms_choose",function(){
        $(".size-frame").removeClass('none');
    });
    $(document).on("touchend",".x-closeico",function(){
        $(".size-frame").addClass('none'); 
    })
    $(document).on("touchend",".norms_item",function(){
        var data = JSON.parse($(this).data('info'));
        $(".norms_price").text(data.price);
        $(".norms_stock").text(data.stock);
        $(".current_price").text("￥ "+data.price);
        $(".norms_item").removeClass("c-bg");
        $(this).addClass("c-bg");
    })
    $(document).on("touchend",".jia",function(){
        var num = $(this).parent().find(".count").text();
        num++;
        if($(this).hasClass('cart')){
            var data = new Object(); 
            data.goodsId = $(this).parent().data('gid');
            data.normsId = 0;
            data.num = num;
            $.post("{{u('Goods/saveCart')}}", data, function(res){
                // console.log(res.data);
                if(res.code < 0){
                    $.alert("请登录");
                    setTimeout('toLogin()', 2000);
                    return;
                }
                if(res.code > 0){
                    $.alert(res.msg);
                    return false;
                }
                $(".count").text(num);
                $(".numr").show();
                //$.showSuccess('添加成功');
                $(".total_amount").text(res.data.totalAmount);
                $(".total_price").text(res.data.totalPrice);
                var result = serviceFee;
                for(var i = 0; i < res.data.list.length; i++){
                    if(res.data.list[i].id == "{{$data['seller']['id']}}"){
                        result = Math.round((res.data.list[i].serviceFee - res.data.list[i].price) * 100) /100;
                        break;
                    }
                }
                if (result > 0) {
                    $(".choose_complet").text("还差" + result + "元起送").addClass("no-click").css("background-color","#ccc");
                } else {
                    $(".choose_complet").text("选好了").css('background-color', '#ff2d4b').removeClass("no-click");
                }
            });
        }else{
            $(".count").text(num);
        }
    });
    $(document).on("touchend",".jian",function(){
        var num = $(this).parent().find(".count").text();
        num--; 
        if(num <= 0){
            $(".numr").hide(); 
        }
        $(".count").text(num);
        if($(this).hasClass('cart')){
            var data = new Object(); 
            data.goodsId = $(this).parent().parent().data('gid');
            data.normsId = 0;
            data.num = num;
            $.post("{{u('Goods/saveCart')}}", data, function(res){
                // console.log(res.data);
                if(res.code < 0){
                    $.alert("请登录");
                    setTimeout('toLogin()', 2000);
                    return;
                } 
                //$.showSuccess('减少成功');
                $(".total_amount").text(res.data.totalAmount);
                $(".total_price").text(res.data.totalPrice);
                var result = serviceFee;
                for(var i = 0; i < res.data.list.length; i++){
                    if(res.data.list[i].id == "{{$data['seller']['id']}}"){
                        result = Math.round((res.data.list[i].serviceFee - res.data.list[i].price) * 100) /100;
                        break;
                    }
                }
                if (result > 0) {
                    $(".choose_complet").text("还差" + result + "元起送").addClass("no-click").css("background-color","#ccc");
                } else {
                    $(".choose_complet").text("选好了").css('background-color', '#ff2d4b').removeClass("no-click");
                }
            });
        }
    });
    $(".norms_item.c-bg").trigger("click");

    //规格加入购物车
    $(document).on('touchend', '.cart_join,.cart_buy_now', function(){
		var isBuy = $(this).hasClass("cart_buy_now");
        var data = new Object();
        var norms =  JSON.parse($(".norms_item.c-bg").data('info'));
        data.goodsId = norms.goodsId;
        data.normsId = norms.id;
        data.num = $('.norms_amount').val();
        if (data.num >  norms.stock) {
            $.alert('商品库存不足');
            return;
        }
        $.post("{{u('Goods/saveCart')}}", data, function(res){
            if(res.code < 0){
                $.router.load("{{u('User/login')}}", true);
                return;
            } 
			if(isBuy){
                $.router.load("{!! u('GoodsCart/index')!!}", true);
            }
            $(".total_amount").text(res.data.totalAmount);
            $(".total_price").text(res.data.totalPrice); 
            $(".current_norms").text(norms.name);
            $(".size-frame").addClass('none');
            var result = serviceFee;
            for(var i = 0; i < res.data.list.length; i++){
                if(res.data.list[i].id == "{{$data['seller']['id']}}"){
                    result = Math.round((res.data.list[i].serviceFee - res.data.list[i].price) * 100) /100;
                    break;
                }
            }
            if (result > 0) {
                $(".choose_complet").text("还差" + result + "元起送").addClass("no-click").css("background-color","#ccc");
            } else {
                $(".choose_complet").text("选好了").css('background-color', '#ff2d4b').removeClass("no-click");
            }
        });
    })
    //收藏
    $(document).on("touchend",".collect_it",function(){
        var obj = new Object();
        var collect = $(this);
        obj.id = $(this).data('id');
        obj.type = 1;

        if(collect.hasClass("on")){
            $.post("{{u('UserCenter/delcollect')}}",obj,function(result){
                if(result.code == 0){
                    collect.removeClass("on");
                    $.alert(result.msg, function(){
                        collect.html('<i class="icon iconfont c-gray x-searchico">&#xe651;</i>');
                    });
                } else if(result.code == 99996){
                    $.router.load("{{u('User/login')}}", true);
                } else {
                    $.alert(result.msg);
                }
            },'json');
        }else{
            $.post("{{u('UserCenter/addcollect')}}",obj,function(result){
                if(result.code == 0){
                    collect.addClass("on");
                   $.alert(result.msg, function(){
                        collect.html('<i class="icon iconfont c-red x-searchico">&#xe652;</i>');
                   });
                } else if(result.code == 99996){
                    $.router.load("{{u('User/login')}}", true);
                } else {
                    $.alert(result.msg);
                }
            },'json');
        }
    });
    </script>
@stop 

