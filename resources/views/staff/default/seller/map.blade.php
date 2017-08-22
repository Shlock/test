@extends('staff.default._layouts.base')
@section('title'){{$title}}@stop
@section('show_top')
    <header class="bar bar-nav">
        <a class="button button-link button-nav pull-left" href="#" onclick="JumpURL('{{ u('Seller/info') }}','#seller_info_view',2)" data-transition='slide-out'>
            <span class="icon iconfont">&#xe64c;</span>
        </a>
        <h1 class="title">{{$title}}</h1>
    </header>
@stop
@section('contentcss')hasbottom @stop
@section('content')
    <div class="blank050"></div>
    <div class="surchbar">
        <div class="inputbox">
            <input type="search" placeholder='输入搜索地址...' name="address" id="map-address-1" value="{{$data['address']}}"/>
        </div>
        <a href="#" class="searchbtn" id="map-search-1">搜索</a>
    </div>

    <div style="width: 100%; height: 400px; position: relative; border: 1px solid rgb(204, 204, 204); ">
        <div class="mapimg" id="map-container-1" style="width: 100%; height: 400px; position: relative; overflow: hidden; -webkit-transform: translateZ(0px); background-color: rgb(229, 227, 223);"></div>
        {{--<div id="resetMapPos"><a class="ui-link">重置<br>范围</a></div>--}}
    </div>
    <div class="maptip">请搜索或者手动选择店铺的位置，并划定服务范围。</div>
    <input type="hidden" name="mapPoint" id="map-point-1" value="@if($data['mapPointStr']){{ $data['mapPointStr'] }} @else 29.56301,106.551557 @endif">
    <input type="hidden" name="mapPos" id="map-pos-1" value="@if($data['mapPosStr']){{ $data['mapPosStr'] }}@endif">
    <div class="account_hd_bottom content-padded">
        <a href="#" class="button button-fill button-success" id="map-save">保存</a>
    </div>
@stop

@section("page_js")
    <script type="text/javascript">
        var qqGeocoder1,qqMap1,qqMarker1,qqPolygon1 = null,qqLatLngs1 = null;
        var defaultMapPoint1 = "{{$data['mapPointStr']}}";
        var mapCenter1;
        if(defaultMapPoint1 == ""){
            mapCenter1 = new qq.maps.LatLng(39.916527,116.397128);
        } else {
            mapCenter1 = new qq.maps.LatLng({{$data['mapPointStr']}});
        }
        qqMap1 = new qq.maps.Map(document.getElementById("map-container-1"),{
            center: mapCenter1,
            zoom: 13
        });
        qqMarker1 = new qq.maps.Marker({
            map:qqMap1,
            draggable:true,
            position: mapCenter1
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
            $("#{{$id_action.$ajaxurl_page}} #map-point-1").val(event.latLng.getLat() + "," + event.latLng.getLng());
            $.createPolygon1(event.latLng);
        });
        qq.maps.event.addListener(qqMap1, "click", function(event) {
            qqMarker1.setPosition(event.latLng);
            $.createPolygon1(event.latLng);
            $("#{{$id_action.$ajaxurl_page}} #map-point-1").val(event.latLng.getLat() + "," + event.latLng.getLng());
        });

        if(defaultMapPoint1 == ""){
            var cityLocation1 = new qq.maps.CityService({
                complete : function(result){
                    qqMap1.setCenter(result.detail.latLng);
                    qqMarker1.setPosition(result.detail.latLng);
                    $.createPolygon1(result.detail.latLng);
                    $("#{{$id_action.$ajaxurl_page}} #map-point-1").val(result.detail.latLng.getLat() + "," + result.detail.latLng.getLng());
                }
            });
            cityLocation1.searchLocalCity();
        } else {
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
        }

        qqGeocoder1 = new qq.maps.Geocoder({
            complete : function(result){
                qqMap1.setCenter(result.detail.location);
                qqMarker1.setPosition(result.detail.location);
                $.createPolygon1(result.detail.location);
                $("#{{$id_action.$ajaxurl_page}} #map-point-1").val(result.detail.location.getLat() + "," + result.detail.location.getLng());
            }
        });

        $(document).on('click', '#{{$id_action.$ajaxurl_page}} #map-search-1',function() {
            if($.trim($("#{{$id_action.$ajaxurl_page}} #map-address-1").val()) != ""){
                qqGeocoder1.getLocation($("#{{$id_action.$ajaxurl_page}} #map-address-1").val());
            }
        });
        $(document).on('click', '#{{$id_action.$ajaxurl_page}} #map-save',function() {
            var addr = $("#{{$id_action.$ajaxurl_page}} #map-address-1").val();
            var mapPoint = $("#{{$id_action.$ajaxurl_page}} #map-point-1").val();
            var maplatLngs = new Array();
            qqPolygon1.getPath().forEach(function(element, index){
                maplatLngs.push(element.getLat() + "," + element.getLng());
            });
            $("#{{$id_action.$ajaxurl_page}} #map-pos-1").val(maplatLngs.join("|"));
            var mapPos = $("#{{$id_action.$ajaxurl_page}} #map-pos-1").val();
            $.post("{{u('Seller/sellermap')}}", {'address':addr,'mapPos':mapPos,'mapPoint':mapPoint}, function(result){
                var url = "{{u('Seller/info')}}";
                JumpURL(url,'#seller_info_view',2)
            });
        })
    </script>
@stop