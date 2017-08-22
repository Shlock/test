@extends('xwap.community._layouts.base')

@section('show_top')
    <header class="bar bar-nav">
        <a class="button button-link button-nav pull-left pageloading" href="{{ Input::get('location') != "" ? u('District/add') : u('District/index') }}" data-transition='slide-out'>
            <span class="icon iconfont">&#xe600;</span>
        </a>
        <h1 class="title f16">选择小区</h1>
    </header>
@stop

@section('content')
    <div class="content" id=''>
        <div class="searchbar row x-searchplot mt10 ml0 pl10 mb10">
            <div class="search-input fl">
                <input type="text" placeholder="输入小区名称" name="keywords" value="{{$args['keywords']}}" id="keywords">
            </div>
            <a class="button button-fill button-primary tc f16 c-black fl" id="search">搜索</a>
        </div>
        <div class="list-block x-splotlst nobor f14">
            @if($args['keywords'] && empty($list))
                <span style="margin-left: 10px;">没有相关数据</span>
            @elseif(empty($args['keywords']) && empty($list))
                <span style="margin-left: 10px;" onclick="$.relocation()" class="ts"><i class="icon iconfont f17 mr5 left">&#xe650;</i><span>点击获取当前定位</span></span>
            @else
                <ul>
                    @foreach($list as $item)
                        <li class="item-content" onclick="$.href('{!! u('District/detail', ['districtId'=>$item['id']])!!}')">
                            <div class="item-inner">
                                <div class="item-title">{{$item['name']}}</div>
                                @if($item['province']['name'])
                                    <div class="item-after c-gray">{{$item['province']['name']}}{{$item['city']['name']}}</div>
                                @else
                                    <i class="icon iconfont c-gray f13">&#xe602;</i>
                                @endif
                            </div>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>
@stop

@section($js)
<script charset="utf-8" src="http://map.qq.com/api/js?v=2.exp&key=2N2BZ-KKZA4-ZG4UB-XAOJU-HX2ZE-HYB4O&libraries=geometry,convertor"></script>
<script type="text/javascript">
    //精确定位
    $(function ($)
    {
        var qqcGeocoder = null;
        var clientLatLng = null;
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
                    @if(!isset($args['keywords']) && empty($args['location']))
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

                    $.router.load("{{u('District/add')}}?location="+result.detail.location.lat+","+result.detail.location.lng, true);
                    @endif
                        //$("#locationName").text(result.detail.address);
                }
            });

            var citylocation = new qq.maps.CityService({
                complete: function (result)
                {
                    clientLatLng = result.detail.latLng;
                    qqcGeocoder.getAddress(result.detail.latLng);
                    $.computeDistanceBegin();
                }
            });
            
            if (navigator.geolocation)
            {
                navigator.geolocation.getCurrentPosition(translatePoint, function (error)
                {
                    citylocation.searchLocalCity();
                },
                {
                    enableHighAccuracy: true,
                    maximumAge: 30000,
                    timeout: 5000
                });
            }
            else
            {
                citylocation.searchLocalCity();
            }
        }

        $.computeDistanceBegin = function ()
        {
            if (clientLatLng == null)
            {
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

        //手动获取定位
        $.relocation = function() {
            $('.ts span').text('定位中...');
            $.computeDistanceBegin();
        }
        

        $(document).on("touchend","#search",function(){
            var keywords = $("#keywords").val();
            $.router.load("{!! u('District/add') !!}?keywords=" + keywords, true);
        })

    });
</script>
@stop