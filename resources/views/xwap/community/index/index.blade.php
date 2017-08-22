@extends('xwap.community._layouts.base')

@section('show_top')
    <header class="bar bar-nav y-sybarnav">
        <h1 class="title tl pl10">
            <i class="icon iconfont c-red f17 mr5 left" onclick="$.relocation()">&#xe650;</i> <!-- 重新定位 -->
            <span onclick="$.href('{{ u('Index/addressmap')}}')" class="f15 p-location">
                @if($orderData['address'])
                    {{$orderData['address']}}
                @else
                    <span id="locationName">定位中请稍候</span>
                @endif
            </span>
            <i class="icon iconfont c-gray f13 ml5 right">&#xe602;</i>
        </h1>
        <a class="button button-link button-nav pull-right open-popup" data-popup=".popup-about" href="{{ u('Seller/search') }}">
            <i class="icon iconfont c-gray x-searchico">&#xe65e;</i>
        </a>
    </header>
@stop

@section('css')
@stop

@section('content')
    @include('xwap.community._layouts.bottom')

    @if($cityIsService == 0)
    <div class="x-null pa w100 tc">
        <i class="icon iconfont">&#xe645;</i>
        <p class="f12 c-gray mt10">当前城市未开通服务</p>
        <a class="f14 c-white x-btn db pageloading" href="{{ u('Index/addressmap')}}">切换地址</a>
    </div>
    @else
        <div class="content" id=''>
            <div class="swiper-container my-swiper" data-space-between='10'>
                <div class="swiper-wrapper">
                    @foreach($data['banner'] as $key => $value)
                        @if($value['type'] == 1)
                            <div class="swiper-slide pageloading" onclick="$.href('{{u('Seller/index',['id'=>$value['arg']])}}')">
                                <img _src="{{ formatImage($value['image'],640,268) }}" src="{{ formatImage($value['image'],640,268) }}" />
                            </div>
                        @elseif ($value['type'] == 2 || $value['type'] == 3 || $value['type'] == 6)
                            <div class="swiper-slide pageloading" onclick="$.href('{{u('Goods/detail',['goodsId'=>$value['arg']])}}')">
                                <img _src="{{ formatImage($value['image'],640,268) }}" src="{{ formatImage($value['image'],640,268) }}" />
                            </div>
                        @elseif ($value['type'] == 4)
                            <div class="swiper-slide pageloading" onclick="$.href('{{u('Seller/detail',['id'=>$value['arg']])}}')">
                                <img _src="{{ formatImage($value['image'],640,268) }}" src="{{ formatImage($value['image'],640,268) }}" />
                            </div>
                        @elseif ($value['type'] == 7)
                            <div class="swiper-slide pageloading" onclick="$.href('{{u('Article/detail',['id'=>$value['arg']])}}')">
                                <img _src="{{ formatImage($value['image'],640,268) }}" src="{{ formatImage($value['image'],640,268) }}" />
                            </div>
                        @else
                            <div class="swiper-slide pageloading" onclick="$.href('{{ $value['arg'] }}')">
                                <img _src="{{ formatImage($value['image'],640,268) }}" src="{{ formatImage($value['image'],640,268) }}" />
                            </div>
                        @endif
                    @endforeach
                </div>
                <div class="swiper-pagination"></div>
            </div>
            <iframe src="{{ u('Index/nav') }}" width="100%" class="y-iframe @if(count($menu) < 5) y-iframeheight @endif"></iframe>

            <ul class="x-advertising clearfix">
                @for($i=0; $i < 2; $i++)
                    <?php $value = $data['notice'][$i]; ?>
                    @if($value['type'] == 1)
                        <li><a href="{{u('Seller/index',['id'=>$value['arg']])}}" class="br pageloading"><img src="{{ formatImage($value['icon'],293,200) }}" ></a></li>
                    @elseif ($value['type'] == 2 || $value['type'] == 3 || $value['type'] == 6)
                        <li><a href="{{u('Goods/detail',['goodsId'=>$value['arg']])}}" class="br pageloading"><img src="{{ formatImage($value['icon'],293,200) }}" ></a></li>
                    @elseif($value['type'] == 4)
                        <li><a href="{{u('Seller/detail',['id'=>$value['arg']])}}" class="br pageloading"><img src="{{ formatImage($value['icon'],293,200) }}" ></a></li>
                    @elseif($value['type'] == 7)
                        <li><a href="{{u('Article/detail',['id'=>$value['arg']])}}" class="br pageloading"><img src="{{ formatImage($value['icon'],293,200) }}" ></a></li>
                    @else
                        <li><a href="{{$value['arg']}}" class="br pageloading"><img src="{{ formatImage($value['icon'],293,200) }}"></a></li>
                    @endif
                @endfor
            </ul>
            <div class="c-bgfff p10">
                @for($i=2; $i< count($data['notice']); $i++)
                    <?php $value = $data['notice'][$i]; ?>
                    @if($value['type'] == 1)
                        <a href="{{u('Seller/index',['id'=>$value['arg']])}}" class="db pageloading"><img src="{{ formatImage($value['icon'],586,200) }}" class="w100"></a>
                    @elseif($value['type'] == 2 || $value['type'] == 3 || $value['type'] == 6)
                        <a href="{{u('Goods/detail',['goodsId'=>$value['arg']])}}" class="db pageloading"><img src="{{ formatImage($value['icon'],586,200) }}" class="w100"></a>
                    @elseif($value == 4)
                        <a href="{{u('Seller/detail',['id'=>$value['arg']])}}" class="db pageloading"><img src="{{ formatImage($value['icon'],586,200) }}" class="w100"></a>
                    @elseif($value['type'] == 7)
                         <a href="{{u('Article/detail',['id'=>$value['arg']])}}" class="db pageloading"><img src="{{ formatImage($value['icon'],586,200) }}" class="w100"></a>
                    @else
                        <a href="{{$value['arg']}}" class="db pageloading"><img src="{{ formatImage($value['icon'],586,200) }}" class="w100"></a>
                    @endif
                @endfor
            </div>
            <!-- 附近推荐商户 -->
            <div class="content-block-title f14 c-gray"><i class="icon iconfont mr5">&#xe632;</i>附近推荐商户</div>
            <div class="list-block media-list y-sylist">
                <ul>
                    @if(!empty($orderData))
                        @foreach($data['sellers'] as $item)
                            <li @if($item['isDelivery'] == 0)style="background:#f3f3f3;"@endif>
                                <php>
                                    if($item['countGoods'] >= 0 && $item['countService'] == 0){
                                    $url = u('Goods/index',['id'=>$item['id'],'type'=>1,'urltype'=>1]);
                                    }elseif($item['countGoods'] == 0 && $item['countService'] > 0){
                                    $url = u('Goods/index',['id'=>$item['id'],'type'=>2,'urltype'=>1]);
                                    }else{
                                    $url = u('Seller/detail',['id'=>$item['id'],'urltype'=>1]);
                                    }
                                </php>
                                <a href="{{$url}}" class="item-link item-content pageloading">
                                    <div class="item-media">
                                        <img src="{{formatImage($item['logo'],100,100)}}" onerror='this.src="{{ asset("wap/community/newclient/images/no.jpg") }}"' width="73">
                                    </div>
                                    <div class="item-inner">
                                        <div class="item-subtitle f16">{{$item['name']}}</div>
                                        <div class="item-title-row f12 c-gray mt5 mb5">
                                            <div class="item-title">
                                                <div class="y-starcont">
                                                    <div class="c-gray4 y-star">
                                                        <i class="icon iconfont vat mr2 f12">&#xe654;</i>
                                                        <i class="icon iconfont vat mr2 f12">&#xe654;</i>
                                                        <i class="icon iconfont vat mr2 f12">&#xe654;</i>
                                                        <i class="icon iconfont vat mr2 f12">&#xe654;</i>
                                                        <i class="icon iconfont vat mr2 f12">&#xe654;</i>
                                                    </div>
                                                    <div class="c-red f12 y-startwo" style="width:{{$item['score'] * 20}}%;">
                                                        <i class="icon iconfont vat mr2 f12">&#xe654;</i>
                                                        <i class="icon iconfont vat mr2 f12">&#xe654;</i>
                                                        <i class="icon iconfont vat mr2 f12">&#xe654;</i>
                                                        <i class="icon iconfont vat mr2 f12">&#xe654;</i>
                                                        <i class="icon iconfont vat mr2 f12">&#xe654;</i>
                                                    </div>
                                                </div>
                                                @if($item['orderCount'] > 0)
                                                    <span class="c-gray f12">已售{{$item['orderCount']}}</span>
                                                @else
                                                    <span class="c-gray f12"></span>
                                                @endif
                                            </div>
                                    <span class="item-after">
                                        <i class="icon iconfont c-gray2 f18">&#xe60d;</i>
                                        <span class="compute-distance" data-map-point-x="{{ $item['mapPoint']['x'] }}" data-map-point-y="{{ $item['mapPoint']['y'] }}"></span>
                                    </span>
                                        </div>
                                        <div class="item-subtitle c-gray">
                                            <span class="mr10">{!! $item['freight'] !!}</span>
                                        </div>
                                    </div>
                                </a>
                            </li>
                        @endforeach
                    @endif
                </ul>
            </div>
        </div>

    @endif
@stop

@section($js)
<script charset="utf-8" src="http://map.qq.com/api/js?v=2.exp&key=2N2BZ-KKZA4-ZG4UB-XAOJU-HX2ZE-HYB4O&libraries=geometry,convertor"></script>
<script src="http://m.sui.taobao.org/assets/js/demos.js"></script>
<script type="text/javascript">

    //精确定位
    $(function(){
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

                        $.router.load("{{u('Index/index')}}?address="+address+"&mapPointStr="+result.detail.location.lat+","+result.detail.location.lng+"&city="+result.detail.addressComponents.city, true);
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

        $(document).on("touchend",".data-content ul li",function(){
            var id = parseInt($(this).data('id'));
            if (id > 0)
            {
                $.router.load("{{u('Seller/detail')}}" + "?staffId=" + id, true);
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
                var mapPoint = new qq.maps.LatLng($(this).attr('data-map-point-x'), $(this).attr('data-map-point-y'));
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

        //重新定位
        $.relocation = function(){
            //异步Session清空
            $.post("{{ u('Index/relocation') }}",function(){
                $.router.load("{{ u('Index/index') }}", true);
            })
        }

    });
</script>
@stop