@extends('xwap.community._layouts.base')

@section('css') 
@stop

@section('show_top') 
    <header class="bar bar-nav">
        <a class="button button-link button-nav pull-left back" href="@if(!empty($nav_back_url)) {{ $nav_back_url }} @else javascript:$.back(); @endif" data-transition='slide-out'>
            <span class="icon iconfont">&#xe600;</span>
        </a>
        <h1 class="title f16">{{$data['name']}}</h1>
        <a class="button button-link button-nav pull-right open-popup collect_it @if($data['iscollect']) on @endif" data-id="{{$data['id']}}" data-popup=".popup-about">
            @if($data['iscollect'])
                <i class="icon iconfont c-red x-searchico">&#xe652;</i><!-- 已收藏图片  -->
            @else
                <i class="icon iconfont c-gray x-searchico">&#xe651;</i><!-- 未收藏图标 -->
            @endif
        </a>
    </header>
@stop  

@section('content')

    <div class="bar bar-footer">
        <span class="x-cart pr mr10">
            <i class="icon iconfont c-gray">&#xe618;</i>
            <span class="badge pa c-bg c-white total_amount">{{ $cart['totalAmount'] }}</span>
        </span>
        <span class="f14 c-gray ml20">总价:￥</span>
        <span class="c-red f18 total_price">{{ $cart['totalPrice'] }}</span>
        <a class="x-menuok c-bg c-white f16 fr choose_complete"  data-id="{{$data['id']}}">选好了</a>
    </div>

    <div class="content" id=''>
        <div class="x-bigpic pr">
            <img src="{{$data['images'][0]}}" class="w100 vab" />
            <!-- <i class="icon iconfont c-white x-shareico">&#xe615;</i> --><!-- 分享按钮 -->
        </div>
        <!-- 选择数量 -->
        <div class="list-block x-goods nobor">
            <ul>
                <li class="item-content">
                    <div class="item-inner">
                        <div class="item-title f14">{{$data['name']}}</div>
                        <div class="item-after fr">
                            <i class="icon iconfont c-gray subtract">&#xe621;</i>
                            <input type="text" value="1" readonly="readonly" class="val tc pl0 count" />
                            <i class="icon iconfont c-red add">&#xe61e;</i>
                        </div>
                    </div>
                </li>
                <li class="item-content">
                    <div class="item-inner">
                        <div class="item-title">
                            <span class="c-red f18 mr10">￥{{$data['price']}}</span>
                            <span class="c-gray f14">{{$data['seller']['name']}}</span>
                        </div>
                    </div>
                </li>
            </ul>
        </div>
        <!-- 选择规格 -->
        <div class="list-block f14 nobor">
            <ul>
                <li class="item-content">
                    <div class="item-inner">
                        <div class="item-title">服务时间
                            <input type="text" id='datetime-picker' class="x-servicetime service_time ml20 pl0" />
                        </div>
                        <!-- <i class="icon iconfont c-gray f13">&#xe602;</i> -->
                    </div>
                </li>
                <li class="item-content" onclick="$.href('{{ u('Goods/appbrief',['goodsId'=>$data['id']]) }}')">
                    <div class="item-inner">
                        <div class="item-title">商品详情</div>
                        <i class="icon iconfont c-gray f13">&#xe602;</i>
                    </div>
                </li>
            </ul>
        </div>
    </div>
@stop

@section($js) 
<!-- <script src="{{ asset('static/infinite-scroll/jquery.infinitescroll.js') }}"></script>  -->
<script src="{{ asset('js/dot.js') }}"></script>
<script src="http://m.sui.taobao.org/assets/js/demos.js"></script>

<script type="text/javascript">
    $("#datetime-picker").datetimePicker({
        value: ["{{Time::toDate(UTC_TIME+1800,'Y')}}", "{{Time::toDate(UTC_TIME+1800,'m')}}", "{{Time::toDate(UTC_TIME+1800,'d')}}", "{{ intval(Time::toDate(UTC_TIME+1800,'H')) }}", "{{Time::toDate(UTC_TIME+1800,'i')}}"]
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
        $(this).siblings(".val").val(val);
    });
    // 关闭规格弹框
    $(document).on("touchend",".x-closebg .x-closeico",function(){
        $(this).parents(".size-frame").addClass('none');
    });
    // 规格选择
    $(document).on("touchend",".x-psize span",function(){
        $(this).addClass("c-bg").siblings().removeClass("c-bg");
    });


    $(document).on("touchend",".norms_choose",function(){
        $(".size-frame").removeClass('none');
    });
    $(document).on("touchend",".x-closeico",function(){
        $(".size-frame").addClass('none'); 
    });
    $(document).on("touchend",".norms_item",function(){
        var data = $(this).data('info'); 
        $(".norms_price").text(data.price);
        $(".norms_stock").text(data.stock);
        $(".current_price").text("￥ "+data.price);
        $(".norms_item").removeClass("c-bg");
        $(this).addClass("c-bg");
    })
    $(document).on("touchend",".jia",function(){
        var num = $(this).parent().find(".count").text();
        num++;
        $(".count").text(num);
    });
    $(document).on("touchend",".jian",function(){
        var num = $(this).parent().find(".count").text();
        num--;
        if(num <= 0){
            $.alert("数量必须大于0！");
            return;
        }
        $(".count").text(num);
    });
    $(".norms_item.c-bg").trigger("click");

    //规格加入购物车
    $(document).on('touchend', '.cart_join', function(){
        var data = new Object();
        var norms =  $(".norms_item.c-bg").data('info');
        data.goodsId = norms.goodsId;
        data.normsId = norms.id;
        data.num = $('.norms_amount').text(); 
        $.post("{{u('Goods/saveCart')}}", data, function(res){
            if(res.code < 0){
                $.router.load("{{u('User/login')}}", true);
                return;
            } else if(res.code == 0) { 
                $('.x-bgtk').removeClass('none').show().find('.ts').text('操作成功');
                $('.x-bgtk1').css({
                    position:'absolute',
                    left: ($(window).width() - $('.x-bgtk1').outerWidth())/2,
                    top: ($(window).height() - $('.x-bgtk1').outerHeight())/2 + $(document).scrollTop()
                });
                setTimeout(function(){
                    $('.x-bgtk').fadeOut('2000',function(){
                        $('.x-bgtk').addClass('none');
                    });
                },'1000');
            } else {
                $.alert(res.msg);
            }
            $(".total_amount").text(res.data.totalAmount);
            $(".total_price").text(res.data.totalPrice); 
            $(".current_norms").text(norms.name);
            $(".size-frame").addClass('none'); 
            
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
    $(document).on("touchend",".choose_complete",function(){
        var data = new Object();
        data.goodsId = $(this).data('id');
        data.serviceTime = $(".service_time").val();
        data.num  = $(".count").val();
        $.post("{{u('Goods/saveCart')}}", data, function(res){
            if(res.code < 0){
                $.router.load("{{u('User/login')}}", true);
            } else if(res.code == 0){ 
                $('.x-bgtk').removeClass('none').show().find('.ts').text('添加成功');
                $('.x-bgtk1').css({
                    position:'absolute',
                    left: ($(window).width() - $('.x-bgtk1').outerWidth())/2,
                    top: ($(window).height() - $('.x-bgtk1').outerHeight())/2 + $(document).scrollTop()
                });
                setTimeout(function(){
                    $('.x-bgtk').fadeOut('2000',function(){
                        $('.x-bgtk').addClass('none');
                    });
                },'1000');
                $.router.load("{{u('GoodsCart/index')}}", true);
            } else {
                $.alert(res.msg);
            }
        });
    });
</script>
@stop 
