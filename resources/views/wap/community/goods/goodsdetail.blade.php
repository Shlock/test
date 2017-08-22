@extends('wap.community._layouts.base')

@section('css') 
<style type="text/css">

</style>
@stop
@section('js') 
    <script src="{{ asset('static/infinite-scroll/jquery.infinitescroll.js') }}"></script> 
    <script src="{{ asset('js/dot.js') }}"></script>
@stop 

@section('show_top') 
    <div data-role="header" data-tap-toggle="false" data-position="fixed" class="x-header">
        <h1>{{$data['name']}}</h1>
        <a href="@if(!empty($nav_back_url)) {{ $nav_back_url }} @else javascript:history.back(-1); @endif" data-iconpos="notext" class="x-back ui-nodisc-icon" data-shadow="false"></a>
        <span class="x-sjr ui-btn-right"><i class="x-sjsc collect_it @if($data['iscollect']) on @endif" data-id="{{$data['id']}}"></i></span>
    </div>
@stop  

@section('content')  
    <div role="main" class="ui-content">
        <div class="x-topic" style="border-bottom:1px solid #ddd;">
            <img src="{{$data['images'][0]}}" />
            <!-- <i class="x-shareico"></i> -->
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
    </div> 
    <!-- footer start -->
    @include('wap.community.goods.cartfooter')   
    <!-- footer end -->
    <!-- 规格弹框 -->
    <div class="f-bgtk none size-frame">
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
    </div>
    <!-- 收藏弹框 -->
    <div class="x-bgtk none">
        <div class="x-bgtk1" style="position: absolute; left: 0px; top: 311px;">
            <div class="x-tkbgi">
                <div class="ts"></div>
            </div> 
        </div>
    </div>
    <script type="text/javascript">
    var serviceFee = "{{ $seller['serviceFee'] }}";
    function toLogin(){
        window.location.href = "{{u('User/login')}}";
    }
    $(".norms_choose").click(function(){
        $(".size-frame").removeClass('none');
    });
    $(".x-closeico").click(function(){
        $(".size-frame").addClass('none'); 
    })
    $(".norms_item").click(function(){
        var data = $(this).data('info'); 
        $(".norms_price").text(data.price);
        $(".norms_stock").text(data.stock);
        $(".current_price").text("￥ "+data.price);
        $(".norms_item").removeClass("c-bg");
        $(this).addClass("c-bg");
    })
    $(".jia").click(function(){
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
                    $.showError("请登录");
                    setTimeout('toLogin()', 2000);
                    return;
                }
                if(res.code > 0){
                    $.showError(res.msg);
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
    $(".jian").click(function(){
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
                    $.showError("请登录");
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
    $(document).on('click', '.cart_join', function(){
        var data = new Object();
        var norms =  $(".norms_item.c-bg").data('info');
        data.goodsId = norms.goodsId;
        data.normsId = norms.id;
        data.num = $('.norms_amount').text(); 
        $.post("{{u('Goods/saveCart')}}", data, function(res){
            if(res.code < 0){
                window.location.href = "{{u('User/login')}}";
                return;
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
    $(".collect_it").click(function(){
        var obj = new Object();
        var collect = $(this);
        obj.id = $(this).data('id');
        obj.type = 1;
        if(collect.hasClass("on")){
            $.post("{{u('UserCenter/delcollect')}}",obj,function(result){
                if(result.code == 0){
                    collect.removeClass("on");
                    $('.x-bgtk').removeClass('none').show().find('.ts').text('取消收藏成功');
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
                    //$.showSuccess(result.msg);
                } else if(result.code == 99996){
                    window.location.href = "{{u('User/login')}}";
                } else {
                    $.showError(result.msg);
                }
            },'json');
        }else{
            $.post("{{u('UserCenter/addcollect')}}",obj,function(result){
                if(result.code == 0){
                    collect.addClass("on");
                    $('.x-bgtk').removeClass('none').show().find('.ts').text('收藏成功');
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
                   // $.showSuccess(result.msg);
                } else if(result.code == 99996){
                    window.location.href = "{{u('User/login')}}";
                } else {
                    $.showError(result.msg);
                }
            },'json');
        }
    });
    </script>
@stop 
