@extends('xwap.community._layouts.base')

@section('show_top')
    <header class="bar bar-nav">
        <a class="button button-link button-nav pull-left" href="@if(!empty($nav_back_url)) {!! $nav_back_url !!} @else<?php echo "javascript:$.back();"; ?>@endif" data-transition='slide-out'>
            <span class="icon iconfont">&#xe600;</span>
        </a>
        <a class="button button-link button-nav pull-right open-popup addr_save" data-popup=".popup-about">
            <span class="icon iconfont c-gray f24">&#xe610;</span>
        </a>
        <h1 class='title'>选择地址</h1>
    </header>
@stop

@section('content')
    <div class="content y-xzaddrcont">
        <div class="y-map">
            <div id="qqMapContainer" style="min-width:100%;height:400px;min-height:80%;"></div>
            <div class="searchbar mapurl">
                <div class="search-input">
                    <label class="icon iconfont c-gray2" for="search">&#xe65e;</label>
                    <input type="search" id="search" placeholder="输入小区、写字楼、学校等">
                </div>
            </div>
        </div>
        <div class="content-block-title f14 c-gray y-blocktitle">附近配送地址</div>
        <div class="list-block y-syt y-xzaddr">
            <ul class="x-addlst">
                <!-- <li class="item-content">
                    <div class="item-inner">
                        <div class="item-title">
                            <i class="icon iconfont c-gray f20">&#xe60d;</i><span class="c-red f14">[当前]群生国际E区</span>
                            <p class="f12 c-gray ml20">八一七中路</p>
                        </div>
                    </div>
                </li> -->
            </ul>
        </div>
    </div>
@stop

@section($js)
<script charset="utf-8" src="http://map.qq.com/api/js?v=2.exp&key=2N2BZ-KKZA4-ZG4UB-XAOJU-HX2ZE-HYB4O&libraries=geometry,convertor"></script>
<script type="text/javascript">
    var qqGeocoder,qqcGeocoder,qqMap,qqMarker,currentLat,currentLon = null,citylocation = null;;
    var mapCenter = null;
    var mapurl = "{{ u('UserCenter/addrsearch') }}";
    var url = "{{ u('UserCenter/addressdetail') }}";
    var cartIds = "{{ Input::get('cartIds') }}";
    var id = "{{ Input::get('id') }}";
    var plateId = "{{ Input::get('plateId') }}";
    var postId = "{{ Input::get('postId') }}";
    var change = "{{ Input::get('change') }}";

    $(function (){
        $(".y-xzaddrcont").css("min-height",$(window).height()-$(".bar-nav").height());

        if(cartIds != '') {
            url += "?cartIds=" + cartIds;
            mapurl += "?cartIds=" + cartIds;
        }
        if(plateId > 0) {
            url += "?plateId=" + plateId + "&postId=" + postId;
            mapurl += "?plateId=" + plateId + "&postId=" + postId;
        }
        if(id > 0) {
            if(change > 0) {
                mapurl += "?id="+id+"&change=" + change;
                url += "?id="+id+"&change=" + change;
            }else{
                mapurl += "?id=" + id;
                url += "?id=" + id;
            }
        }
        if(change > 0) {
            mapurl += "?change=" + change;
            url += "?change=" + change;
        }
        @if (!empty($data['mapPointStr']))                
            mapCenter = new qq.maps.LatLng({{$data['mapPointStr']}});
        @endif
        qqMap = new qq.maps.Map(document.getElementById("qqMapContainer"),{
            @if (!empty($data['mapPointStr']))
            center: mapCenter,
            @endif
            zoom: 13
        });
        qqMarker = new qq.maps.Marker({
            @if (!empty($data['mapPointStr']))
            position: mapCenter,
            @endif
            map:qqMap,
            draggable:true
        });

        @if (empty($data['mapPointStr']))
            //精确定位
            var translatePoint = function (position){
                var currentLat = position.coords.latitude;
                var currentLon = position.coords.longitude;
                qq.maps.convertor.translate(new qq.maps.LatLng(currentLat, currentLon), 1, function (res)
                {
                    latLng = res[0];
                    $.setLocation(latLng);
                    qqcGeocoder.getAddress(latLng);
                });
            }

            var citylocation = new qq.maps.CityService({
                complete: function (result) {
                    clientLatLng = result.detail.latLng;
                    $.setLocation(clientLatLng);
                    qqcGeocoder.getAddress(clientLatLng);
                }
            });
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(translatePoint, function (error){
                    citylocation.searchLocalCity();
                },{enableHighAccuracy: true});
            }
        @else
            qq.maps.convertor.translate(mapCenter, 1, function (res)
            {
                latLng = res[0];
                $.setLocation(latLng);
                qqcGeocoder.getAddress(latLng);
            });
        @endif

        qq.maps.event.addListener(qqMarker, 'dragend', function(event) {
            qqMarker.setPosition(event.latLng);
            qqcGeocoder.getAddress(event.latLng);
        });
        qq.maps.event.addListener(qqMap, 'click', function(event) {
            qqMarker.setPosition(event.latLng);
            $(".x-addlst").empty();
            qqcGeocoder.getAddress(event.latLng);
        });

        $.setLocation = function(latLng) {
            qqMap.setCenter(latLng);
            qqMarker.setPosition(latLng);
        }

        qqcGeocoder = new qq.maps.Geocoder({
            complete : function(result){
                var nowNearPoi = null;
                var nearPoi;
                html = '';

                for(var nearPoiKey in result.detail.nearPois){
                    nearPoi = result.detail.nearPois[nearPoiKey];
                    if (nowNearPoi == null || nowNearPoi.dist > nearPoi.dist) {
                        nowNearPoi = nearPoi;
                    }

                    html += '<li data-mappoint="'+nearPoi.latLng+'" data-city="'+result.detail.addressComponents.city+'" data-name="'+nearPoi.name+'" class="item-content"><div class="item-inner"><div class="item-title"><i class="icon iconfont c-gray f20">&#xe60d;</i>';
                    if(nearPoiKey == 0){
                        html += '<span class="f14 c-red">[当前]'+nearPoi.name+'</span>';
                    } else {
                        html += '<span class="f14">'+nearPoi.name+'</span>';
                    }
                    html += '<p class="f12 c-gray ml20">'+nearPoi.address+'</p></div></div></li>';

                }
                $(".x-addlst").append(html);
            }
        });

        
        $(document).on("touchend",".addr_save",function(){
            var address = $(".c-red").parent().attr('data-name')
            var mapPoint = $(".c-red").parent().attr('data-mappoint');
            var city = $(".c-red").parent().attr('data-city');
            var data = {
                    "address":address,
                    "mapPoint":mapPoint,
                    "city":city
                };
            
            $.post("{{ u('UserCenter/saveMap') }}",data,function(res){
                window.location.href= url;
            },"json");
        })

        $(".x-addlst").on("click", "li", function () {
            var address = $(this).attr('data-name')
            var mapPoint = $(this).attr('data-mappoint');
            var city = $(this).attr('data-city');
            var data = {
                    "address":address,
                    "mapPoint":mapPoint,
                    "city":city
                };
            $.post("{{ u('UserCenter/saveMap') }}",data,function(res){
                if(res.code == 1){
                    $.toast("抱歉，当前城市未开通服务，请选择其他城市吧");
                }else{
                    window.location.href= url;
                }
            },"json");
        });

        $(document).on("touchend",".mapurl",function(){
            window.location.href= mapurl;
        })

    });
</script>
@stop