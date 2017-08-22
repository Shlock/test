@extends('wap.community._layouts.base')
@section('show_top')
    <div data-role="header" data-position="fixed" class="x-header">
        <h1>选择小区</h1>
        <a href="@if(!empty($nav_back_url)) {{ $nav_back_url }} @else javascript:history.back(-1); @endif" data-iconpos="notext" class="x-back ui-nodisc-icon" data-shadow="false"></a>
    </div>
@stop
@section('content')
    <div role="main" data-role="content">
        <div class="x-searchxq">
            <input type="text" placeholder="输入小区名称" name="keywords" value="{{$args['keywords']}}" id="keywords">
            <span class="btn" id="search">搜索</span>
        </div>
        <div class="x-lh45 f12 c-green x-searchfj">@if( empty($args['keywords']) )附近小区@else 搜索结果 @endif</div>
        @if(empty($list))
            <span >正在定位中。。。</span>
        @else
        <ul class="x-bgfff2">
            @foreach($list as $item)
            <li class="x-joinxq clearfix" onclick="window.location.href='{!! u('District/detail', ['districtId'=>$item['id']])!!}'">
                <span class="x-xqsl">{{$item['name']}}</span>
                <span class="f14 fr c-green x-xqsr">{{$item['province']['name']}}{{$item['city']['name']}}</span>
            </li>
            @endforeach
        </ul>
        @endif
    </div>
@stop
@section('js')
<script charset="utf-8" src="http://map.qq.com/api/js?v=2.exp&key=2N2BZ-KKZA4-ZG4UB-XAOJU-HX2ZE-HYB4O&libraries=geometry,convertor"></script>
<script type="text/javascript">
    //精确定位
    jQuery(function ($)
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
                    @if(empty($args['keywords']) && empty($args['location']))
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

                    window.location.href = "{{u('District/add')}}?location="+result.detail.location.lat+","+result.detail.location.lng;
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

        $(document).on("touchend","#search",function(){
            var keywords = $("#keywords").val();
            window.location.href="{!! u('District/add') !!}?keywords=" + keywords;
        })
        

    });
</script>
@stop