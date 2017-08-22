@extends('xwap.community._layouts.base')

@section('show_top')
    <header class="bar bar-nav">
        <a class="button button-link button-nav pull-left pageloading" href="{{u('Index/index')}}" data-transition='slide-out'>
            <span class="icon iconfont">&#xe600;</span>
        </a>
        <h1 class="title f16">{{$seller_data['name']}}</h1>
        <a class="button button-link button-nav pull-right open-popup collect_opration @if($seller_data['isCollect'] == 1) on @endif" data-popup=".popup-about">
            @if($seller_data['isCollect'] == 1)
                <i class="icon iconfont c-red x-searchico">&#xe652;</i><!-- 已收藏图片  -->
            @else
                <i class="icon iconfont c-gray x-searchico">&#xe651;</i><!-- 未收藏图标 -->
            @endif
        </a>
    </header>

@stop

@section('content')
<!-- new -->
<nav class="bar bar-tab">
    <p class="buttons-row x-bombtn">
        @if($seller_data['countService'] > 0 && $seller_data['countGoods'] < 1)
            <a href="#" class="button" @if($seller_data['isDelivery'] == 1)onclick="$.href('{{ u('Goods/index',['id'=>$seller_data['id'],'type'=>2])}}')"@else style="background:#ddd;" @endif>选择服务</a>
        @elseif($seller_data['countGoods'] > 0 && $seller_data['countService'] > 0)
            <a href="#" class="button" @if($seller_data['isDelivery'] == 1)onclick="$.href('{{ u('Goods/index',['id'=>$seller_data['id'],'type'=>2])}}')"@else style="background:#ddd;" @endif>选择服务</a>
            <a href="#" class="button pr" @if($seller_data['isDelivery'] == 1)onclick="$.href('{{ u('Goods/index',['id'=>$seller_data['id'],'type'=>1])}}')"@else style="background:#ddd;" 
            @endif>购买商品</a>
        @else
            <a href="#" class="button pr" @if($seller_data['isDelivery'] == 1)onclick="$.href('{{ u('Goods/index',['id'=>$seller_data['id'],'type'=>1])}}')"@else style="background:#ddd;" 
            @endif>购买商品</a>
        @endif
    </p>
</nav>
<div class="content" id=''>
    <div class="x-goodstop buttons-tab">
        <a class="button f15 @if(CONTROLLER_NAME == 'Goods'   && ACTION_NAME == 'index') active @endif " href="{{u('Goods/index',['id'=>$seller_data['id'], 'type'=>1])}}">商品</a>
        <a class="button f15 @if(CONTROLLER_NAME == 'Goods' && ACTION_NAME == 'comment') active @endif " href="{{u('Goods/comment',['id'=>$seller_data['id']])}}">评价</a>
        <a class="button f15 @if(CONTROLLER_NAME == 'Seller' && ACTION_NAME == 'detail') active @endif " href="{{u('Seller/detail',['id'=>$seller_data['id']])}}">商家</a>
    </div>

    <div class="pr">
        <img src="@if(!empty($seller_data['image'])){{formatImage($seller_data['image'],375,260)}}@else{{ asset('wap/community/client/images/sj.jpg') }}@endif" class="w100 vam" />
        <div class="x-sjdel">
            <div class="x-sjpic"><img src="@if(!empty($seller_data['logo'])) {{formatImage($seller_data['logo'],200,200)}} @else {{ asset('wap/community/client/images/x5.jpg')}} @endif" /></div>
            <div class="f16 c-white mt5">{{$seller_data['name']}}</div>
            <ul class="x-sjbot pa c-white w100">
                <li>
                    <p class="f16">￥{{$seller_data['serviceFee']}}</p>
                    <p class="f12">起送价</p>
                </li>
                <li class="pr">
                    <p class="f16">￥{{$seller_data['deliveryFee']}}</p>
                    <p class="f12">配送费</p>
                </li>
            </ul>
        </div>
    </div>

    <div class="list-block media-list x-store">
        <ul>
            <li>
                <div class="item-content">
                    <div class="item-inner">
                        <div class="item-title-row">
                            <div class="item-title f14">
                                <i class="icon iconfont c-gray fl">&#xe639;</i>
                                <span class="x-storer db">{{$seller_data['businessHours']}}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </li>
            <li>
                <div class="item-content">
                    <div class="item-inner">
                        <div class="item-title-row">
                            <div class="item-title f14">
                                <i class="icon iconfont c-gray fl f17">&#xe608;</i>
                                <span class="x-storer db"><a href="tel:{{$seller_data['tel']}}">{{$seller_data['tel']}}</a></span>
                            </div>
                            <a href="tel:{{$seller_data['tel']}}" class="icon iconfont c-white c-bg x-storephone f17">&#xe60a;</a>
                        </div>
                    </div>
                </div>
            </li>
            <li>
                <div class="item-content">
                    <div class="item-inner">
                        <div class="item-title-row">
                            <div class="item-title f14">
                                <i class="icon iconfont c-gray fl f20">&#xe60d;</i>
                                <span class="x-storer db">{{$seller_data['address']}}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </li>
            <li>
                <div class="item-content">
                    <div class="item-inner">
                        <div class="item-title-row">
                            <div class="item-title f14">
                                <i class="icon iconfont c-gray fl f18">&#xe647;</i>
                                <span class="x-storer db">
                                    @if(count($articles)>0)
                                        @foreach($articles as $key => $value)
                                            <span>{!!$value['content']!!}</span><br/>
                                        @endforeach 
                                    @else 
                                        <span>暂无最新公告信息</span>
                                    @endif
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </li>
        </ul>
    </div>
    <div class="content-block-title f14 c-gray">商家介绍</div>
    <div class="card m0">
        <div class="card-content">
            <div class="card-content-inner p10">{{ $seller_data['detail'] ? $seller_data['detail'] : '暂无介绍'}}</div>
        </div>
    </div>
</div>
@stop

@section($js)
    <script>
        $(document).on("touchend",".collect_opration",function(){
            var obj = new Object();
            var collect = $(this);
            obj.id = "{{$seller_data['id']}}";
            obj.type = 2;
            if(collect.hasClass("on")){
                $.post("{{u('UserCenter/delcollect')}}",obj,function(result){
                    if(result.code == 0){
                        collect.removeClass("on");
                        $.alert(result.msg,function(){
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
                       $.alert(result.msg,function(){
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

 