@extends('wap.community._layouts.base')

@section('css') 
<style type="text/css">
    .y-gwcmain li .y-gwcbtm span.y-zhuangtai_bad{
        float: right;
        padding: 0;
        padding-right: 10px;
        background: none;
    }
    .y-gwcmain li .y-gwcbtm span.y-zhuangtai_bad a{
        line-height: 35px;
        padding: 0 1em;
        display: inline-block;
        background: #BEBEBE;
        color: #fff!important;
        border-radius: 3px;
    }
</style>
@stop
@section('js') 
    <script src="{{ asset('static/infinite-scroll/jquery.infinitescroll.js') }}"></script> 
    <script src="{{ asset('js/dot.js') }}"></script>
@stop 

@section('show_top') 
    <div data-role="header" data-position="fixed" data-tap-toggle="false" class="x-header">
        <h1>购物车</h1>
        <a href="javascript:history.back(-1);" data-iconpos="notext" class="x-back ui-nodisc-icon" data-shadow="false"></a>
        <a href="javascript:;" class="x-sjr ui-btn-right clear_cart"><img src="{{ asset('wap/community/client/images/ico/delete.png') }}" width="23"></a>
    </div>
@stop

@section('content')  
    <!-- /content -->
    <div>
        <ul class="y-gwcmain">
            @if(empty($cart)) 
            <div class="y-null">
                <img src="{{ asset('wap/community/client/images/null.png')}}">
            </div>
            @else
            @foreach($cart as $ckey => $citem)
            <li>
                <p class="f14 check_all on">
                    <span><a href="{{u('Goods/index', ['id'=>$citem['id'],'urltype'=>2])}}"><img src="{{ asset('wap/community/client/images/ico/ico1.png')}}" width="18">{{$citem['name']}}</a></span>
                </p>
                @foreach($citem['goods'] as $gkey => $gitem)
                <div class="y-ddcontent on" data-sid="{{$citem['id']}}" data-id="{{$gitem['id']}}">
                    <div class="y-xzimg"></div>
                    <div class="y-img"><a href='{{u("Goods/detail",["goodsId"=>$gitem["goodsId"]])}}'><img src="{{ formatImage($gitem['logo'], 100, 100) }}"></a></div>
                    <div class="y-ddxqtext">
                        <p class="f14"><strong>{{$gitem['name']}}</strong><img src="{{ asset('wap/community/client/images/ico/delete.png') }}" class="single_delete" data-id="{{$gitem['id']}}"></p>
                        <h3 class="c-red f14" data-gid="{{$gitem['goodsId']}}" data-nid="{{$gitem['normsId']}}">￥<span class="y-price">{{$gitem['price']}}</span>
                            <span class="plus num"></span>
                            <span class="count num f18 c-black">{{$gitem['num']}}</span>
                            <span class="minus num"></span>
                        </h3>
                    </div> 
                </div>
                @endforeach 
                <div class="f14 y-gwcbtm">
                    <span>合计：<span class="c-red f18">￥<b class="total_price">{{(float)$citem['price']}}</b></span></span>
                    <!--<span class="c-green f12">(不含运费)</span-->
                    @if( (float)$citem['price'] < $citem['serviceFee'])
                        <span class="y-zhuangtai_bad price_one_{{$citem['id']}}"><a href="javascript:;">差{{ $citem['serviceFee'] - (float)$citem['price']}}元起送</a></span>
                    @else
                        <span class="y-zhuangtai price_one_{{$citem['id']}}"><a  href="javascript:;">去结算</a></span>
                    @endif
                    <span class="y-zhuangtai_bad price_two_{{$citem['id']}} none"><a href="javascript:;">差<span class="price_spread"></span>元起送</a></span>
                    <span class="y-zhuangtai price_three_{{$citem['id']}} none"><a  href="javascript:;">去结算</a></span>
                </div>
            </li>
            @endforeach  
            @endif
        </ul>
    </div>
    @include('wap.community._layouts.bottom')
    <script type="text/javascript"> 
    function toLogin(){
        window.location.href = "{{u('User/login')}}";
    }

    function deleteItem(id){ 
        $.post("{{u('Goods/cartDelete')}}", {id:id}, function(res){
            if(res.status == true){
                $.showSuccess(res.msg); 
                //obj.parent().parent().parent().remove();
                window.location.reload();
            } else {
                $.showError(res.msg);
            }
        }); 
    }
    $(function(){
        //选择
        $(".y-gwcmain li .y-ddcontent .y-xzimg").touchend(function(){ 
            // console.log($(this).parents());
            if($(this).parents().hasClass("on")){
                $(this).parents().removeClass("on");
                $(this).parents().siblings(".y-gwcmain li p").removeClass("on");
                var subflag = true; 
                var num = 0;
               // console.log($(this).parent().parent().find(".y-gwcbtm span span .total_price"));
                var total_price = $(this).parent().parent().find(".y-gwcbtm .total_price").text();
                var price = $(this).parent().find(".y-ddxqtext .y-price").text();
                var count = $(this).parent().find(".y-ddxqtext .count").text();
                var sum_prices = (price * count).toFixed(2) ;
                $(this).parent().parent().find(".y-gwcbtm .total_price").text((parseFloat(total_price)-parseFloat(sum_prices)).toFixed(2));

                $(this).parent().parent().find(".y-xzimg").each(function(){ 
                    if($(this).parent().hasClass("on")){
                        subflag = false;
                        topay = true;
                        num++;
                    }  
                });     
                if(num <= 0){ 
                    $(this).parent().parent().find(".y-zhuangtai a").css('background', '#B6AFB0');
                    $(this).parent().parent().find(".y-zhuangtai").unbind('click'); 
                }
            }else{ 
                $(this).parent().addClass("on");
                var flag = true;
                $(this).parent().parent().find('.y-xzimg').each(function(){ 
                    if(!$(this).parent().hasClass("on")){
                        flag = false;
                    }
                }); 
                var total_price = $(this).parent().parent().find(".y-gwcbtm .total_price").text();
                var price = $(this).parent().find(".y-ddxqtext .y-price").text();
                var count = $(this).parent().find(".y-ddxqtext .count").text();
                var sum_prices = (price * count).toFixed(2);
                $(this).parent().parent().find(".y-gwcbtm .total_price").text((parseFloat(total_price)+parseFloat(sum_prices)).toFixed(2));
                // alert(3 + "-" + flag);
                if(flag){
                    $(this).parent().parent().find(".check_all").addClass("on");
                    $(this).parents().addClass("on");
                }
                $(this).parent().parent().find(".y-zhuangtai a").css('background', '#ff2d4b'); 
                $(this).parent().parent().find(".y-zhuangtai").click(function(){ 
                    var ids = '';
                    var obj = $(this).parent().parent().find('.y-ddcontent.on');
                    var length = obj.length;
                    obj.each(function(res){ 
                        if(res != length - 1) {
                            ids += $(this).data('id') + ",";
                        } else {
                            ids += $(this).data('id');
                        }
                    });   
                    window.location.href = "{{u('Order/order')}}?cartIds="+ids;
                }); 
            }
        });

        $(".y-gwcmain li p").touchend(function(){  
            var num=0;
            $(this).siblings(".y-ddcontent").each(function(){
                if(!$(this).hasClass("on")){
                    num++;
                }
            });
            if($(this).hasClass("on")){
                if(num!=0){
                    $(this).siblings(".y-ddcontent").addClass("on");
                    $(this).addClass("on");

                }else{
                    $(this).siblings(".y-ddcontent").removeClass("on");
                    $(this).removeClass("on");
                }
                $(this).parent().find(".y-gwcbtm .total_price").text('0.00');
            }else{
                $(this).siblings(".y-ddcontent").addClass("on");
                $(this).addClass("on");
                var obj = $(this).siblings(".y-ddcontent");
                var length = obj.length;
                var price =0,total_price = 0, num = 1;
                obj.each(function(res){ 
                    num = parseInt($(obj[res]).find(".count").text());
                    price = parseFloat($(obj[res]).find(".y-price").text());
                    total_price += parseFloat((num*price).toFixed(2));
                    //console.log(total_price);
                }); 
                //alert(total_price);
                $(this).parent().find(".y-gwcbtm .total_price").text(total_price.toFixed(2));
            } 
            if($(this).parent().find(".y-ddcontent.on").length > 0){
                $(this).parent().find(".y-zhuangtai a").css('background', '#ff2d4b'); 
                $(this).parent().find(".y-zhuangtai").click(function(){ 
                    var ids = '';
                    var obj = $(this).parent().find('.y-ddcontent.on');
                    var length = obj.length;
                    obj.each(function(res){ 
                        if(res != length - 1) {
                            ids += $(this).data('id') + ",";
                        } else {
                            ids += $(this).data('id');
                        }
                    });   
                    window.location.href = "{{u('Order/order')}}?cartIds="+ids;
                }); 
            } else {
                $(this).parent().find(".y-zhuangtai a").css('background', '#B6AFB0');
                $(this).parent().find(".y-zhuangtai").unbind('click'); 
            }
        });
        // 增减数量
        $(".y-ddxqtext .minus").touchend(function(){ 
            var obj = $(this);
            var data = new Object();
            data.goodsId = obj.parent().data('gid');
            data.normsId = obj.parent().data('nid');
            var count = $(this).siblings(".count");
            var j=$(count).text(); 
            var i= parseInt(j);
            i=i-1;
            if(i<=1){
                i=1;
            } 
            data.num = i; 
            $.post("{{u('Goods/saveCart')}}", data, function(res){ 
                if(res.code < 0){
                    $.showError("请登录");
                    setTimeout('toLogin()', 2000);
                    return;
                } else if(res.code > 0) {
                    $.showError(res.msg);
                } else { 
                    $(count).text(i); 
                }
                //$.showSuccess('添加成功');
                var sid = obj.parents('.y-ddcontent').data('sid');

                for(var j = 0; j < res.data.list.length; j++){  
                    if(res.data.list[j]['id'] == sid){  
                        obj.parent().parent().parent().parent().find(".total_price").text(res.data.list[j].price);

                        //按钮设置
                        $(".price_one_"+sid+",.price_two_"+sid+",.price_three_"+sid).addClass("none");
                        //还差XX元起送
                        if(res.data.list[j].serviceFee - res.data.list[j].price > 0){
                            $(".price_two_"+sid+" .price_spread").text((res.data.list[j].serviceFee - res.data.list[j].price).toFixed(2));
                            $(".price_two_"+sid).removeClass("none");
                        }else{
                            $(".price_three_"+sid).removeClass("none");
                        }

                        break;
                    }
                }    
                $("#cart_amount").text(res.data.totalAmount);

            });
        });
        $(".y-ddxqtext .plus").touchend(function(){ 
            var obj = $(this);
            var data = new Object();
            data.goodsId = obj.parent().data('gid');
            data.normsId = obj.parent().data('nid');
            var count = $(this).siblings(".count");
            var j=$(count).text(); 
            var i= parseInt(j);
            i=i+1; 
            data.num = i;
            $.post("{{u('Goods/saveCart')}}", data, function(res){ 
                if(res.code < 0){
                    $.showError("请登录");
                    setTimeout('toLogin()', 2000);
                    return;
                } else if(res.code > 0) {
                    $.showError(res.msg);
                } else {
                    $(count).text(i); 
                }   
                //$.showSuccess('添加成功');
                var sid = obj.parents('.y-ddcontent').data('sid');
                for(var j = 0; j < res.data.list.length; j++){  
                    if(res.data.list[j]['id'] == sid){  
                        obj.parent().parent().parent().parent().find(".total_price").text(res.data.list[j].price);

                        //按钮设置
                        $(".price_one_"+sid+",.price_two_"+sid+",.price_three_"+sid).addClass("none");
                        //还差XX元起送
                        if(res.data.list[j].serviceFee - res.data.list[j].price > 0){
                            $(".price_two_"+sid+" .price_spread").text((res.data.list[j].serviceFee - res.data.list[j].price).toFixed(2));
                            $(".price_two_"+sid).removeClass("none");
                        }else{
                            $(".price_three_"+sid).removeClass("none");
                        }

                        break;
                    }
                }    
                $("#cart_amount").text(res.data.totalAmount);

            });
            
        });
        //清空购物车
        $(".clear_cart").click(function(){
            var obj = $(this);
            var id = 0; 
            $.showOperation('确认清空购物车？', "javascript:deleteItem(" + id + ");"); 
        });
        //删除单个
        $(".single_delete").click(function(){
            var obj = $(this);
            var id = obj.data('id'); 
            $.showOperation('确认删除？', "javascript:deleteItem(" + id + ");");      
        });
        $(".y-zhuangtai").click(function(){  
            var ids = '';
            var obj = $(this).parent().parent().find('.y-ddcontent.on');
            var length = obj.length;
            obj.each(function(res){ 
                if(res != length - 1) {
                    ids += $(this).data('id') + ",";
                } else {
                    ids += $(this).data('id');
                }
            });
            window.location.href = "{{u('Order/order')}}?cartIds="+ids;
        }); 
    })
    </script>
@stop 
