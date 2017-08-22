@extends('wap.community._layouts.base')

@section('css') 
<style type="text/css">

</style>
@stop
@section('js')

@stop 

@section('show_top') 
    <div data-role="header" data-tap-toggle="false" data-position="fixed" class="x-header">
        <h1>{{$seller['name']}}</h1>
        <a href="@if(!empty($nav_back_url)) {{ $nav_back_url }} @else javascript:history.back(-1); @endif" data-iconpos="notext" class="x-back ui-nodisc-icon" data-shadow="false"></a>
        <a class="x-sjr ui-btn-right" href="{{ u('Seller/search') }}"><i class="x-serico"></i></a>
    </div>
@stop  

@section('content')
    <!-- /content -->
    <div role="main" class="ui-content x-menu">
        @include('wap.community.goods.sellergoodshead')
        <div class="x-protype clearfix">
            <ul class="x-protypett x-menut3" id="scroll_menu">
                @foreach($cate as $ckey => $item)
                    @if(count($item['goods']) > 0)
                    <li @if($ckey == 0) class="on" @endif><a href="javascript:;">{{$item['name']}}</a></li>
                    @endif
                @endforeach
            </ul>
            <div class="x-prolst">
                @foreach($cate as $ckey => $item)
                    @if(count($item['goods']) > 0)
                        <div class="scroll_top">
                            <div class="x-prott">{{$item['name']}}({{count($item['goods'])}})</div>
                            @foreach($item['goods'] as $k=>$v)
                            <div class="x-pro1 clearfix mr10 mt5">
                                <div class="x-typepic">
                                    <a href="{{u('Goods/detail',['goodsId'=>$v['id']])}}">
                                        <img src="@if($v['image']) {{ formatImage($v['image'],150,150) }} @else {{ asset('wap/community/client/images/wykdimg.png') }} @endif" />
                                    </a>
                                </div>
                                <a href="{{u('Goods/detail',['goodsId'=>$v['id']])}}"><p>{{$v['name']}}</p></a>
                                @if($v['salesCount'] > 0)
                                <p style="font-size:10px;color:#979797;line-height:30px;">已售 {{$v['salesCount']}}</p>
                                @endif
                            </div>
                                @if(count($v['norms']) > 0)
                                    <div>
                                        @foreach($v['norms'] as $nk => $n)
                                        <php>
                                            $normsNum = 0;
                                            foreach ($cart['data']['goods'] as $carts) {
                                            if ($carts['normsId'] == $n['id']) {
                                            $normsNum = $carts['num'];
                                            break;
                                            }
                                            }
                                        </php>
                                        <div class="x-pro2 clearfix @if($nk > 3) none @endif">
                                            <a href="#"><div class="prl">{{$n['name']}}</div></a>
                                            <div class="x-num" style="width:100%;right:10px;position:inherit;margin-top:0px;">
                                                <span class="c-red fl">￥{{$n['price']}}</span>
                                                <div class="cart_items" data-gid="{{$v['id']}}" data-nid="{{ $n['id'] }}">
                                                    <i class="jia cart_add"></i>
                                                    <div class="numr" @if($normsNum > 0) style="display: block" @endif>
                                                        <span class="count">{{$normsNum}}</span>
                                                        <i class="jian cart_sub"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        @endforeach
                                        @if(count($v['norms']) > 3)
                                            <div class="x-proshow">展开剩下{{ count($v['norms']) - 3 }}条<i></i></div>
                                        @endif
                                    </div>
                                @else
                                    <php>
                                        $goodsNum = 0;
                                        foreach ($cart['data']['goods'] as $carts) {
                                        if ($carts['goodsId'] == $v['id'] && $carts['normsId'] == 0) {
                                        $goodsNum = $carts['num'];
                                        break;
                                        }
                                        }
                                    </php>
                                    <div class="x-pro2 clearfix">
                                        <div class="x-num" style="width:100%;right:10px;position:inherit;margin-top:0px;">
                                            <span class="c-red fl" style="margin-left:0px;">￥{{$v['price']}}</span>
                                            <div class="cart_items" data-gid="{{$v['id']}}" data-nid="0">
                                                <i class="jia cart_add"></i>
                                                <div class="numr" @if($goodsNum > 0) style="display: block" @endif>
                                                    <span class="count">{{$goodsNum}}</span>
                                                    <i class="jian cart_sub"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                @endif
                                @endforeach
                        </div>
                    @endif
            @endforeach

            </div>
        </div>
    </div>
    <!-- content end -->
    <!-- footer start -->
    @include('wap.community.goods.cartfooter')
    <!-- footer end -->
    <!-- 规格弹框 -->
    <div class="f-bgtk none size-frame">
        <div class="x-closebg">

        </div>
    </div>
    <script type="text/tpl" id="loadTemp">
        <img style="position:absolute;top:50%;left:50%;margin-left:-16px;margin-top:-16px;" src="{{ asset('wap/community/client/css/images/ajax-loader.gif')}}" />
    </script>

    <script src="{{ asset('wap/community/client/js/cel.js') }}"></script>
    <script type="text/javascript">
        $('.x-menut3').css({
            height:$(window).height()-138-50,
            overflowY:'auto'
        });
        var serviceFee = "{{ $seller['serviceFee'] }}";
        function toLogin(){
            window.location.href = "{{u('User/login')}}";
        }

        //无规格商品增加
        $(document).on('touchend', '.cart_add', function(){
            var obj = $(this);
            var data = new Object();
            data.goodsId = obj.parent().data('gid');
            data.normsId = obj.parent().data('nid');
            data.num = obj.parent().find('.count').text();
            data.num++;
            $.post("{{u('Goods/saveCart')}}", data, function(res) {
                if (res.code < 0) {
                    $.showError("请登录");
                    setTimeout('toLogin()', 2000);
                    return;
                }
                if (res.code == 0) {
                    obj.parent().find('.count').text(data.num);
                    obj.siblings('.numr').show();
                } else {
                    $.showError(res.msg);
                }
                $(".total_amount").text(res.data.totalAmount);
                $(".total_price").text(res.data.totalPrice);
                var result  = serviceFee;
                for (var i = 0; i < res.data.list.length; i++) {
                    if (res.data.list[i].id == {{$seller['id']}}) {
                        result = Math.round((res.data.list[i].serviceFee - res.data.list[i].price) * 100) / 100;
                        break;
                    }
                }
                if (result > 0) {
                    $(".choose_complet").text("还差" + result + "元起送").addClass("no-click").css("background-color","#ccc");
                } else {
                    $(".choose_complet").text("选好了").removeClass("no-click").css("background-color","#ff2d4b");
                }

            });
        });
        //无规格商品减少
        $(document).on('touchend', '.cart_sub', function(){
            var obj = $(this);
            var data = new Object();
            data.goodsId = obj.parents(".cart_items").data('gid');
            data.normsId = obj.parents(".cart_items").data('nid');;
            data.num = obj.parent().find('.count').text();
            data.num--;
            if(data.num < 0) {
                $.showError('数量必须大于0');
                return;
            }
            $.post("{{u('Goods/saveCart')}}", data, function(res){
                // console.log(res.data);
                if(res.code < 0){
                    $.showError("请登录");
                    setTimeout('toLogin()', 2000);
                    return;
                }
                if(res.code == 0){
                    obj.parent().find('.count').text(data.num);
                    if(data.num == 0) {
                        obj.parent().hide();
                    }
                } else {
                    $.showError(res.msg);
                }
                //$.showSuccess('减少成功');
                $(".total_amount").text(res.data.totalAmount);
                $(".total_price").text(res.data.totalPrice);
                var result  = serviceFee;
                for(var i = 0; i < res.data.list.length; i++){
                    if(res.data.list[i].id == {{$seller['id']}}){
                        result = Math.round((res.data.list[i].serviceFee - res.data.list[i].price) * 100) /100;
                        break;
                    }
                }
                if (result > 0) {
                    $(".choose_complet").text("还差" + result + "元起送").addClass("no-click").css("background-color","#ccc");
                } else {
                    $(".choose_complet").text("选好了").removeClass("no-click").css("background-color","#ff2d4b");
                }
            });
        });

        var typelst_h = $(".x-typelst").height();
        $(".x-fwtypett").css({"height":$(window).height()-210,"min-height":typelst_h});
        $(".x-proshow").touchend(function(){
            $(this).siblings(".x-pro2").removeClass("none");
            $(this).hide();
        });
    </script>
@stop 
