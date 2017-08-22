<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" />
    <meta name="format-detection" />
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <link rel=stylesheet href="{{ asset('staff/css/base.css') }}">
    <link rel=stylesheet href="{{ asset('staff/css/jquery.mobile.custom.structure.min.css') }}">
    <link rel="stylesheet" href="{{ asset('staff/css/jquery.mobile.icons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('staff/css/theme-a.min.css') }}">
    <link rel="stylesheet" href="{{ asset('staff/css/service.css') }}">
    <link rel="stylesheet" href="{{ asset('staff/css/morris.css') }}">
    <script src="{{ asset('js/jquery.1.8.2.js') }}"></script>
    <script src="{{ asset('staff/js/jquery.mobile.custom.min.js') }}"></script>
    <style>
        #resetMapPos{
            position: absolute;top: 10px;left: 0px; z-index: 9999;width: 40px;height: 30px;background-color: #fff;line-height: 13px;-webkit-tap-highlight-color: rgba(0, 0, 0, 0);
            box-shadow: rgba(0, 0, 0, 0.498039) 0px 0px 6px;
            border-radius: 2px;text-align: center;padding: 5px 0;
        }
        #resetMapPos a{ color: #ff2d4b;  font-size: 12px;}
    </style>
</head>
<body>
<!-- /page -->
<div>
    <!-- /content -->
  
    <div role="main" class="ui-content" id="mapPos-form-item" >
        <div class="x-lh50 clearfix">
            <div class="x-seatxt fl">
                <input type="text" name="address" id="map-address-1" value="" />
            </div>
            <div class="x-seabtn">
                <input type="button" value="搜索" id="map-search-1"/>
            </div>
        </div>
        <div style="width: 540px; height: 400px; position: relative; border: 1px solid rgb(204, 204, 204); ">
            <div class="x-map" id="map-container-1" style="width: 540px; height: 400px; position: relative; overflow: hidden; -webkit-transform: translateZ(0px); background-color: rgb(229, 227, 223);"></div>
            <div id="resetMapPos"><a class="ui-link">重置<br>范围</a>
            </div>
        </div>
        <input type="hidden" name="mapPoint" id="map-point-1" value="@if($data['mapPoint']){{ $data['mapPoint'] }} @endif">
        <input type="hidden" name="mapPos" id="map-pos-1" value="@if($data['mapPosStr']){{ $data['mapPosStr'] }}@endif">
        <p class="f12 mt15 mb15">请搜索或者手动选择店铺的位置，并划定服务范围。</p>
        <input type="hidden" name="userId" value="{{$args['userId']}}">
        <button class="btn" id="map-save">保存</button>
    </div>
<script charset="utf-8" src="http://map.qq.com/api/js?v=2.exp&key=2N2BZ-KKZA4-ZG4UB-XAOJU-HX2ZE-HYB4O&libraries=geometry,convertor"></script>
    <script type="text/javascript">
    var qqGeocoder1,qqMap1,qqMarker1,qqPolygon1,currentLat,currentLon = null,qqLatLngs1 = null;
    var defaultMapPoint1 = "{{$data['mapPoint']}}";
    var mapCenter1;

    jQuery(function($){
        $(window).load(function(){
            @if (!empty($data['mapPoint']))                
                mapCenter1 = new qq.maps.LatLng({{$data['mapPoint']}});
            @endif         
            qqMap1 = new qq.maps.Map(document.getElementById("map-container-1"),{
                @if (!empty($data['mapPoint']))
                center: mapCenter1,
                @endif
                zoom: 13
            });
            qqMarker1 = new qq.maps.Marker({
                map:qqMap1,
                @if (!empty($data['mapPoint']))
                position: mapCenter1,
                @endif
                draggable:true
            });

            //重置范围
            $("#resetMapPos").click(function(){
                $.createPolygon1(mapCenter1, true);
            });



            $.createPolygon1 = function(latLng, isReset){
                if(!qqPolygon1 || isReset){
                    var tmpLng = qq.maps.geometry.spherical.computeOffset(latLng, 500, 0);
                    //西北角
                    var wnLatLng = qq.maps.geometry.spherical.computeOffset(tmpLng, 500, -90);
                    //东北角
                    var enLatLng = qq.maps.geometry.spherical.computeOffset(tmpLng, 500, 90);
                    //东南角
                    var esLatLng = qq.maps.geometry.spherical.computeOffset(enLatLng, 1000, 180);
                    //西南角
                    var nwLatLng = qq.maps.geometry.spherical.computeOffset(wnLatLng, 1000, 180);
                    qqLatLngs1 = [wnLatLng,enLatLng,esLatLng,nwLatLng];

                    if(!qqPolygon1) {
                        qqPolygon1 = new qq.maps.Polygon({
                            map:qqMap1,
                            editable:true,
                            visible:true,
                            path:qqLatLngs1
                        });
                    } else {
                        qqPolygon1.setPath(qqLatLngs1);
                    }

                } else {
                    var heading = qq.maps.geometry.spherical.computeHeading(mapCenter1, latLng);
                    var distance = qq.maps.geometry.spherical.computeDistanceBetween(mapCenter1, latLng);
                    qqLatLngs1 = new Array();
                    qqPolygon1.getPath().forEach(function(element, index){
                        qqLatLngs1.push(qq.maps.geometry.spherical.computeOffset(element, distance, heading));
                    });
                    qqPolygon1.setPath(qqLatLngs1);
                }

                mapCenter1 = latLng;
            }

            qq.maps.event.addListener(qqMarker1, "dragend", function(event) {
                $("#map-point-1").val(event.latLng.getLat() + "," + event.latLng.getLng());
                $.createPolygon1(event.latLng);
            });
            qq.maps.event.addListener(qqMap1, "click", function(event) {
                qqMarker1.setPosition(event.latLng);
                $.createPolygon1(event.latLng);
                $("#map-point-1").val(event.latLng.getLat() + "," + event.latLng.getLng());
            });

            @if (empty($data['mapPoint']))
               var translatePoint = function (position){
                    var currentLat = position.coords.latitude;
                    var currentLon = position.coords.longitude;
                    qq.maps.convertor.translate(new qq.maps.LatLng(currentLat, currentLon), 1, function (res)
                    {
                        latlng = res[0];
                        $.setLocation(latlng);
                    });
                }

                var citylocation = new qq.maps.CityService({
                    complete: function (result) {
                        clientLatLng = result.detail.latLng;
                        $.setLocation(clientLatLng);
                    }
                });
                
                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(translatePoint, function (error){
                        citylocation.searchLocalCity();
                    },{enableHighAccuracy: true});
                } else {
                    citylocation.searchLocalCity();
                }

            @else
                var mapPos1 = "{{$data['mapPosStr']}}".split("|"); 
                var mpLatLng1;
                qqLatLngs1 = new Array();
                for(var mpIndex = 0; mpIndex < mapPos1.length; mpIndex++){
                    mpLatLng1 = mapPos1[mpIndex].split(",");
                    qqLatLngs1.push(new qq.maps.LatLng(mpLatLng1[0],mpLatLng1[1]));
                } 
                qqPolygon1 = new qq.maps.Polygon({
                    map:qqMap1,
                    editable:true,
                    visible:true,
                    path:qqLatLngs1
                });
            @endif
            
            qqGeocoder1 = new qq.maps.Geocoder({
                complete : function(result){
                    $.setLocation(result.detail.location);
                }
            });

            $("#map-search-1").click(function(){
                if($.trim($("#map-address-1").val()) != ""){
                    qqGeocoder1.getLocation($("#map-address-1").val());
                }
            });

            $.setLocation = function(latLng) {
                qqMap1.setCenter(latLng);
                qqMarker1.setPosition(latLng);
                $.createPolygon1(latLng);
                $("#map-point-1").val(latLng.getLat() + "," + latLng.getLng());
            }



            @if (empty($data['mapPoint']))
                $.createPolygon1(mapCenter1, true);
            @endif

        })

        $("#map-save").click(function() {
            // var addr = $("#map-address-1").val();
            var mapPoint = $("#map-point-1").val();
            var maplatLngs = new Array();
            qqPolygon1.getPath().forEach(function(element, index){
                maplatLngs.push(element.getLat() + "," + element.getLng());
            });
            $("#map-pos-1").val(maplatLngs.join("|"));
            var mapPos = $("#map-pos-1").val(); 
            $.post("{{u('Seller/mapSave')}}", {'mapPos':mapPos,'mapPoint':mapPoint}, function(result){
                window.location.href = "{!! u('Seller/reg') !!}";
            }); 
        })
    })

    </script>
</body>
</html>