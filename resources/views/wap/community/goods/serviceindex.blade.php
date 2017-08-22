@extends('wap.community._layouts.base')

@section('cs') 
<style type="text/css">

</style>
@stop
@section('js') 
    <script src="{{ asset('static/infinite-scroll/jquery.infinitescroll.js') }}"></script> 
@stop 

@section('show_top') 
    <div data-role="header" data-tap-toggle="false" data-position="fixed" class="x-header">
        <h1>{{$seller['name']}}</h1>
        <a href="@if(!empty($nav_back_url)) {{ $nav_back_url }} @else javascript:history.back(-1); @endif" data-iconpos="notext" class="x-back ui-nodisc-icon" data-shadow="false"></a>
        <a class="x-sjr ui-btn-right" href="{{ u('Seller/search')}}"><i class="x-serico"></i></a>
    </div>
@stop  

@section('content')  
    <!-- /content -->
    <div role="main" class="ui-content">
        <div class="x-sliderct">          
            <div id="focus" class="focus">
              <div class="hd">
                <ul></ul>
              </div>
              <div class="bd">
                <ul>
                    @foreach($adv as $key => $value)
                        <li>
                            @if($value['type'] == 1)
                            <a href="{{u('Seller/index',['id'=>$value['arg']])}}">
                                <img _src="{{ formatImage($value['image'],640,268) }}" src="{{ formatImage($value['image'],640,268) }}" />
                            </a>
                            @elseif ($value['type'] == 2 || $value['type'] == 3)
                            <a href="{{u('Goods/detail',['goodsId'=>$value['arg']])}}">
                                <img _src="{{ formatImage($value['image'],640,268) }}" src="{{ formatImage($value['image'],640,268) }}" />
                            </a>
                            @elseif ($value['type'] == 4)
                            <a href="{{u('Seller/detail',['id'=>$value['arg']])}}">
                                <img _src="{{ formatImage($value['image'],640,268) }}" src="{{ formatImage($value['image'],640,268) }}" />
                            </a>
                            @else
                            <a href="{{ $value['arg'] }}">
                                <img _src="{{ formatImage($value['image'],640,268) }}" src="{{ formatImage($value['image'],640,268) }}" />
                            </a>
                            @endif
                        </li>
                    @endforeach
                </ul>
              </div>
            </div>
            <script type="text/javascript">
            $(function() {
                TouchSlide({ 
                    slideCell:"#focus",
                    titCell:".hd ul", //开启自动分页 autoPage:true ，此时设置 titCell 为导航元素包裹层
                    mainCell:".bd ul", 
                    effect:"left", 
                    autoPlay:true,//自动播放
                    autoPage:true, //自动分页
                    switchLoad:"_src" //切换加载，真实图片路径为"_src" 
                  });
            })
            </script>
        </div>
        <ul class="x-index4" style="margin-bottom:0;">
            <li class="clearfix">
                <a href="{{u('Seller/detail',['id'=>$seller['id']])}}">
                    <div class="x-naimg">
                        <img src="{{ formatImage($seller['logo'], 100, 100)}}" />
                    </div>
                    <div class="x-index4r">
                        <p class="c-black mt10">{{$seller['name']}}</p>
                        <i class="x-rightico"></i>
                        <p class="c-green f12 time">
                            <span>营业时间：{{$seller['businessHours']}}</span>
                        </p>
                    </div>
                </a>
            </li>
        </ul>
        @if(!empty($cate))
        <ul class="x-fwlst clearfix">
            @foreach($cate as $key => $goods)
            <li>
                <a href="{{u('Goods/detail',['goodsId'=>$goods['id']])}}">
                    <div class="x-fwpic">
                        <img src="{{ formatImage($goods['logo'], 300, 300)}}" />
                    </div>
                    <p>
                        <span class="fl na">{{$goods['name']}}</span>
                        <span class="time">{{$goods['duration']}}分钟</span>
                    </p>
                    <p class="c-red">￥{{$goods['price']}}</p>
                </a>
            </li>
            @endforeach 
        </ul>
        @else
        <!--div style="margin-top:30px; text-align:center;width:100%;">
            <img src="{{ asset('wap/community/client/images/null.png')}}" style="width:50%;">
        </div-->
        @endif
    </div>
    <!-- content end -->
    @include('wap.community._layouts.bottom')
@stop 