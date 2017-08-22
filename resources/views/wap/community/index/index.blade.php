@extends('wap.community._layouts.base')
@section('show_top')
    <div data-role="header" data-position="fixed" class="x-header">
        <h1><i class="x-addico"></i><a href="{{ u('UserCenter/address',['change'=>1])}}" class="title-span">@if($orderData['address']){{$orderData['address']}}@else<span id="locationName">定位中，请稍候</span>@endif</a><i class="x-rightico"></i></h1>
        <a class="x-sjr ui-btn-right" href="{{ u('Seller/search') }}"><i class="x-serico"></i></a>
    </div>
@stop
@section('css')
<style type="text/css">
    .x-iadr .x-lbico{margin-top: 15px;}
    .x-index1 p{color: #313233;}
    .x-pjlstct .star-rank,.x-pjlstct .star-score{height:25px;overflow: hidden;}
</style>
@stop
@section('content')
    <div role="main" class="ui-content">
        <!-- 幻灯片 -->
        <div class="x-sliderct">          
            <div id="focus" class="focus">
              <div class="hd">
                <ul></ul>
              </div>
              <div class="bd">
                <ul>
                    @foreach($data['banner'] as $key => $value)
                        <li>
                            @if($value['type'] == 1)
                            <a href="{{u('Seller/index',['id'=>$value['arg']])}}">
                                <img _src="{{ formatImage($value['image'],640,268) }}" src="{{ formatImage($value['image'],640,268) }}" />
                            </a>
                            @elseif ($value['type'] == 2 || $value['type'] == 3 || $value['type'] == 6)
                            <a href="{{u('Goods/detail',['goodsId'=>$value['arg']])}}">
                                <img _src="{{ formatImage($value['image'],640,268) }}" src="{{ formatImage($value['image'],640,268) }}" />
                            </a>
                            @elseif ($value['type'] == 4)
                            <a href="{{u('Seller/detail',['id'=>$value['arg']])}}">
                                <img _src="{{ formatImage($value['image'],640,268) }}" src="{{ formatImage($value['image'],640,268) }}" />
                            </a>
                            @elseif ($value['type'] == 7)
                                <a href="{{u('Article/detail',['id'=>$value['arg']])}}">
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
                @if($orderData['address'])
                var content = "{{$orderData['address']}}";
                if(content.length <= 20){
                    $(".title-span").html(content);
                } else {
                    $(".title-span").html("<marquee>"+content+"</marquee>");
                }
                @endif
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
        <ul class="x-index1 clearfix" style="display:none">
            @if(count($sellerCates) > 0)
            @foreach($sellerCates as $key=>$item)
			@if($key < 7)
            <li><a href="{{u('Seller/index',['id'=>$item['id']])}}"><img src="{{ $item['logo']}}"><p>{{ $item['name']}}</p></a></li>
			@endif
            @endforeach
            <li><a href="{{u('Seller/cates')}}"><img src="{{ asset('wap/community/client/images/s9.png') }}"><p>全部分类</p></a></li>
            @endif        
		</ul>
       <ul class="x-index2 clearfix">
            @for($i=0; $i < 2; $i++)
			<?php $value = $data['notice'][$i]; ?>
            @if($value['type'] == 1)
            <li><a href="{{u('Seller/index',['id'=>$value['arg']])}}" class="br"><img src="{{ formatImage($value['icon'],293,200) }}" ></a></li>
			@elseif ($value['type'] == 2 || $value['type'] == 3 || $value['type'] == 6)
            <li><a href="{{u('Goods/detail',['goodsId'=>$value['arg']])}}" class="br"><img src="{{ formatImage($value['icon'],293,200) }}" ></a></li> 
            @elseif($value['type'] == 4)
			<li><a href="{{u('Seller/detail',['id'=>$value['arg']])}}" class="br"><img src="{{ formatImage($value['icon'],293,200) }}" ></a></li>
            @elseif($value['type'] == 7)
                <li><a href="{{u('Article/detail',['id'=>$value['arg']])}}" class="br"><img src="{{ formatImage($value['icon'],293,200) }}" ></a></li>
            @else
            <li><a href="{{$value['arg']}}" class="br"><img src="{{ formatImage($value['icon'],293,200) }}"></a></li>
            @endif
            @endfor
        </ul>
        <div class="x-index3">
            @for($i=2; $i< count($data['notice']); $i++)
			<?php $value = $data['notice'][$i]; ?>
            @if($value['type'] == 1)
            <li><a href="{{u('Seller/index',['id'=>$value['arg']])}}" class="br"><img src="{{ formatImage($value['icon'],586,200) }}" ></a></li>
			@elseif ($value['type'] == 2 || $value['type'] == 3 || $value['type'] == 6)
            <li><a href="{{u('Goods/detail',['goodsId'=>$value['arg']])}}" class="br"><img src="{{ formatImage($value['icon'],586,200) }}" ></a></li>  
            @elseif($value['type'] == 4)
			<li><a href="{{u('Seller/detail',['id'=>$value['arg']])}}" class="br"><img src="{{ formatImage($value['icon'],586,200) }}" ></a></li>
            @elseif($value['type'] == 7)
                <li><a href="{{u('Article/detail',['id'=>$value['arg']])}}" class="br"><img src="{{ formatImage($value['icon'],586,200) }}" ></a></li>
			@else 
            <li><a href="{{$value['arg']}}" class="br"><img src="{{ formatImage($value['icon'],586,200) }}" ></a></li>
            @endif
            @endfor
        </div>
        <div class="x-lh45 x-lh40 c-green">
            <img src="{{ asset('wap/community/client/images/ico/ico1.png')}}" class="x-lhico" width="20" />
            附近推荐商户
        </div>
        <ul class="x-index4">
            @if(!empty($orderData))
            @foreach($data['sellers'] as $item)
            <li class="clearfix" @if($item['isDelivery'] == 0)style="background:#f3f3f3;"@endif>
                <php>
                    if($item['countGoods'] >= 0 && $item['countService'] == 0){
                        $url = u('Goods/index',['id'=>$item['id'],'type'=>1,'urltype'=>1]);
                    }elseif($item['countGoods'] == 0 && $item['countService'] > 0){
                        $url = u('Goods/index',['id'=>$item['id'],'type'=>2,'urltype'=>1]);
                    }else{
                        $url = u('Seller/detail',['id'=>$item['id'],'urltype'=>1]);
                    }
                </php>
                <a href="{{$url}}">
                    <div class="x-naimg">
                        <img src="{{formatImage($item['logo'],100,100)}}"/>
                    </div>
                    <div class="x-index4r">
                        <p class="c-black">{{$item['name']}}</p>
                        <p class="x-pjlstct  c-green f12 mt5" style="margin-left:0px;">
                            <span class="star-rank">
                                <!-- 五颗星总长70px，此时星级的长度用百分比控制 -->
                                <span class="star-score" style="width:{{$item['score'] * 20}}%;"></span>
                            </span>
                            @if($item['orderCount'] > 0)
                                <span style="line-height:25px;padding-left:5px;">已售 {{$item['orderCount']}}</span>
                             @else
                                <span style="line-height:25px;padding-left:5px;"></span>
                            @endif
                            <span class="fr">
                                <i class="x-addico"></i>
                                <span class="compute-distance" data-map-point-x="{{ $item['mapPoint']['x'] }}" data-map-point-y="{{ $item['mapPoint']['y'] }}"></span>
                            </span>
                        </p>
                        <p class="c-green f12 mt5">{!! $item['freight'] !!}</p>

                    </div>
                </a>
            </li>
            @endforeach
            @endif
        </ul>
    </div>
    @include('wap.community._layouts.bottom')
@stop

@section('js')
<script charset="utf-8" src="http://map.qq.com/api/js?v=2.exp&key=2N2BZ-KKZA4-ZG4UB-XAOJU-HX2ZE-HYB4O&libraries=geometry,convertor"></script>
<script type="text/javascript">
    //精确定位
    jQuery(function ($)
    {
        var qqcGeocoder = null;
        var clientLatLng = null;
        @if(!empty($orderData['mapPointStr']))
        var clientLatLngs = "{{ $orderData['mapPointStr'] }}".split(',');
        clientLatLng = new qq.maps.LatLng(clientLatLngs[0], clientLatLngs[1]);
        @endif

        $.gpsposition = function ()
        {
            var translatePoint = function (position)
            {
                var currentLat = position.coords.latitude;
                var currentLon = position.coords.longitude;
                clientLatLng = new qq.maps.LatLng(currentLat, currentLon);

                qq.maps.convertor.translate(new qq.maps.LatLng(currentLat, currentLon), 1, function (res)
                {
                    latlng = res[0];
                    qqcGeocoder.getAddress(latlng);
                    $.computeDistanceBegin();
                });
            }

            qqcGeocoder = new qq.maps.Geocoder({
                complete: function (result)
                {
                @if(empty($orderData))
                    var nowNearPoi = null;
                    var nearPoi;
                    
                    for(var nearPoiKey in result.detail.nearPois){
                        nearPoi = result.detail.nearPois[nearPoiKey];
                        if (nowNearPoi == null || nowNearPoi.dist > nearPoi.dist) {
                            nowNearPoi = nearPoi;
                        }
                    }

                    var address = nowNearPoi.address + nowNearPoi.name;
                    var reg = new RegExp("^" + result.detail.addressComponents.country, "gi");
                    address = address.replace(reg, '');
                    reg = new RegExp("^" + result.detail.addressComponents.province, "gi");
                    address = address.replace(reg, '');
                    reg = new RegExp("^" + result.detail.addressComponents.city, "gi");
                    address = address.replace(reg, '');
                    reg = new RegExp("^" + result.detail.addressComponents.district, "gi");
                    address = address.replace(reg, '');
                    
                    window.location.href = "{{u('Index/index')}}?address="+address+"&mapPointStr="+result.detail.location.lat+","+result.detail.location.lng;
                @endif
                    //$("#locationName").text(result.detail.address);
                }
            });

            var citylocation = new qq.maps.CityService({
                complete: function (result) {
                    clientLatLng = result.detail.latLng;
                    qqcGeocoder.getAddress(result.detail.latLng);
                    $.computeDistanceBegin();
                }
            });
            
            @if(empty($orderData['mapPointStr']))
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(translatePoint, function (error){
                    citylocation.searchLocalCity();
                },{enableHighAccuracy: true});
            } else {
                citylocation.searchLocalCity();
            }
            @endif
        }
        $(".data-content ul li").click(function ()
        {
            var id = parseInt($(this).data('id'));
            if (id > 0)
            {
                window.location.href = "{{u('Seller/detail')}}" + "?staffId=" + id;
            }
        });

        $.computeDistanceBegin = function ()
        {
            if (clientLatLng == null) {
                $.gpsposition();
                return;
            }

            $(".compute-distance").each(function ()
            {
                var mapPoint = new qq.maps.LatLng($(this).data('map-point-x'), $(this).data('map-point-y'));
                $.computeDistanceBetween(this, mapPoint);
                $(this).removeClass('compute-distance');
            })
        }

        $.computeDistanceBetween = function (obj, mapPoint)
        {
            var distance = qq.maps.geometry.spherical.computeDistanceBetween(clientLatLng, mapPoint);
            if (distance < 1000)
            {
                $(obj).html(Math.round(distance) + 'M');
            } else
            {
                $(obj).html(Math.round(distance / 1000 * 100) / 100 + 'Km');
            }
        }

        $.SwiperInit = function (box, item, url)
        {
            $(box).infinitescroll({
                itemSelector: item,
                debug: false,
                dataType: 'html',
                nextUrl: url
            }, function (data)
            {
                $.computeDistanceBegin();
            });
        }
        $.computeDistanceBegin();

    });
</script>
@stop
