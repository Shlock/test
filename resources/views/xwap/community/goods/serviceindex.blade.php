@extends('xwap.community._layouts.base')

@section('cs') 
@stop

@section('show_top') 
    <header class="bar bar-nav">
        <a class="button button-link button-nav pull-left pageloading" href="{{ $nav_back_url}}" data-transition='slide-out'>
            <span class="icon iconfont">&#xe600;</span>
        </a>
        <h1 class="title f16">{{$seller['name']}}</h1>
        <a class="button button-link button-nav pull-right open-popup pageloading" href="{{ u('Seller/search')}}" data-popup=".popup-about"><i class="icon iconfont c-gray x-searchico">&#xe65e;</i></a>
    </header>
@stop  

@section('content')  
    <!-- new -->
    @include('xwap.community._layouts.bottom')
    <div class="content" id=''>
        <!-- <div class="x-bigpic">
            <img src="images/x1.png" class="w100 vab" />
        </div> -->
        @if(count($adv) > 1)
            <div class="swiper-container" data-space-between='10'>
                <div class="swiper-wrapper">
                    
                    @foreach($adv as $key => $value)
                        <div class="swiper-slide">
                            @if($value['type'] == 1)
                                <a href="{{u('Seller/index',['id'=>$value['arg']])}}">
                                    <img _src="{{ formatImage($value['image'],640,268) }}" src="{{ formatImage($value['image'],640,268) }}" class="w100 vab"/>
                                </a>
                            @elseif ($value['type'] == 2 || $value['type'] == 3)
                                <a href="{{u('Goods/detail',['goodsId'=>$value['arg']])}}">
                                    <img _src="{{ formatImage($value['image'],640,268) }}" src="{{ formatImage($value['image'],640,268) }}" class="w100 vab"/>
                                </a>
                            @elseif ($value['type'] == 4)
                                <a href="{{u('Seller/detail',['id'=>$value['arg']])}}">
                                    <img _src="{{ formatImage($value['image'],640,268) }}" src="{{ formatImage($value['image'],640,268) }}" class="w100 vab"/>
                                </a>
                            @else
                                <a href="{{ $value['arg'] }}">
                                    <img _src="{{ formatImage($value['image'],640,268) }}" src="{{ formatImage($value['image'],640,268) }}" class="w100 vab"/>
                                </a>
                            @endif
                        </div>
                    @endforeach
                </div>
                <div class="swiper-pagination"></div>
            </div>
        @else
            <div class="x-bigpic pr">
                @foreach($adv as $key => $value)
                    @if($value['type'] == 1)
                        <a href="{{u('Seller/index',['id'=>$value['arg']])}}">
                            <img _src="{{ formatImage($value['image'],640,268) }}" src="{{ formatImage($value['image'],640,268) }}" class="w100 vab"/>
                        </a>
                    @elseif ($value['type'] == 2 || $value['type'] == 3)
                        <a href="{{u('Goods/detail',['goodsId'=>$value['arg']])}}">
                            <img _src="{{ formatImage($value['image'],640,268) }}" src="{{ formatImage($value['image'],640,268) }}" class="w100 vab"/>
                        </a>
                    @elseif ($value['type'] == 4)
                        <a href="{{u('Seller/detail',['id'=>$value['arg']])}}">
                            <img _src="{{ formatImage($value['image'],640,268) }}" src="{{ formatImage($value['image'],640,268) }}" class="w100 vab"/>
                        </a>
                    @else
                        <a href="{{ $value['arg'] }}">
                            <img _src="{{ formatImage($value['image'],640,268) }}" src="{{ formatImage($value['image'],640,268) }}" class="w100 vab"/>
                        </a>
                    @endif
                @endforeach
            </div>
        @endif

        <div class="list-block media-list x-service nobor">
            <ul>
                <li>
                    <a href="{{u('Seller/detail',['id'=>$seller['id']])}}" class="item-link item-content pageloading">
                        <div class="item-media"><img src="{{ formatImage($seller['logo'], 100, 100)}}" width="80"></div>
                        <div class="item-inner">
                            <div class="item-title-row">
                                <div class="item-title f16 mt5">{{$seller['name']}}</div>
                            </div>
                            <div class="item-subtitle"><i class="icon iconfont c-gray fr f13 vat">&#xe602;</i></div>
                            <div class="item-text f12 c-gray ha">营业时间：{{$seller['businessHours']}}</div>
                        </div>
                    </a>
                </li>
            </ul>
        </div>
        @if(!empty($cate))
            <ul class="x-fwlst pl5 pr5 clearfix">
                @foreach($cate as $key => $goods)
                    <li>
                        <a href="{{u('Goods/detail',['goodsId'=>$goods['id']])}}" class="c-bgfff pageloading">
                            <div class="x-fwpic pr mb5">
                                <img src="{{ formatImage($goods['logo'], 300, 300)}}" />
                            </div>
                            <p class="f12 c-black">
                                <span class="fl na">{{$goods['name']}}</span>
                                <span class="time">{{$goods['duration']}}分钟</span>
                            </p>
                            <p class="c-red f12 mb5">￥{{$goods['price']}}</p>
                        </a>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>
@stop 

@section($js) 
    <!-- <script src="{{ asset('static/infinite-scroll/jquery.infinitescroll.js') }}"></script> -->
    <script src="http://m.sui.taobao.org/assets/js/demos.js"></script>
@stop 