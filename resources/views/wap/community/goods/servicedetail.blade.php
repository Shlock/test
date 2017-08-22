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
        <div class="x-topic">
            <img src="{{$data['images'][0]}}" />
            <!--i class="x-shareico"></i-->
        </div>
        <div class="x-brbg" style="margin-top:0;border-top:0;">
            <div class="f14 clearfix">
                {{$data['name']}}
                <div class="x-num fr">
                    <i class="jia"></i>
                    <span class="count c-green">1</span>
                    <i class="jian"></i>
                </div>
            </div>
            <p class="mt10 mb15">
                <b class="c-red mr10">￥{{$data['price']}}</b>
                <span class="c-green f14">{{$data['seller']['name']}}</span>
            </p>
        </div>
        <ul class="x-pdelst"> 
            <li class="clearfix">
                <div class="fl">服务时间</div>
                <div class="x-pdelstr fl" style="width:70%;"><input type="datetime-local" class="d-date service_time" min="{{ Time::toDate(UTC_TIME,'Y-m-d\TH:i') }}" id="beginTime" value="{{ Time::toDate(UTC_TIME,'Y-m-d\TH:i') }}" style="min-height:0px;padding:0px;"></div>
                <!-- <i class="x-rightico fr"></i> -->
            </li>
            <li onclick="window.location.href='{!!$data['url']!!}'">
                <a href="javascript:;">
                <span class="fl">商品详情</span>
                <span class="x-pdelstr"></span>
                <i class="x-rightico fr"></i>
                </a>
            </li>
        </ul>
    </div> 
    <!-- footer start -->
    <div data-role="footer" data-position="fixed" data-tap-toggle="false" class="x-footer">
        <div class="x-tocart">
            <span class="dot c-bg"><label class="total_amount">{{ $cart['totalAmount'] }}</label></span>
        </div>
        <div class="fl x-total">
            <span class="f14 fl c-green">总价:￥</span>
            <span class="x-tprice c-red total_price">{{ $cart['totalPrice'] }}</span>
        </div>
        <div class="x-menuok c-bg choose_complete" data-id="{{$data['id']}}">选好了</div>
    </div>  
    <!-- footer end -->
<!-- 收藏弹框 -->
    <div class="x-bgtk none">
        <div class="x-bgtk1" style="position: absolute; left: 0px; top: 311px;">
            <div class="x-tkbgi">
                <div class="ts"></div>
            </div> 
        </div>
    </div>
    <script type="text/javascript">
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
        $(".count").text(num);
    });
    $(".jian").click(function(){
        var num = $(this).parent().find(".count").text();
        num--;
        if(num <= 0){
            $.showError("数量必须大于0！");
            return;
        }
        $(".count").text(num);
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
                $.showError(res.msg);
            }
            $(".total_amount").text(res.data.totalAmount);
            $(".total_price").text(res.data.totalPrice); 
            $(".current_norms").text(norms.name);
            $(".size-frame").addClass('none'); 
            
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
    $(".choose_complete").click(function(){
        var data = new Object();
        data.goodsId = $(this).data('id');
        data.serviceTime = $(".service_time").val();
        data.num  = $(".count").text();
        $.post("{{u('Goods/saveCart')}}", data, function(res){
            if(res.code < 0){
                window.location.href = "{{u('User/login')}}"; 
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
                window.location.href = "{{u('GoodsCart/index')}}"; 
            } else {
                $.showError(res.msg);
            }
        });
    });
    </script>
@stop 
