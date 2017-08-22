@extends('wap.community._layouts.base_order')

@section('content')
    <!-- /header -->
    @section('show_top')
        <div data-role="header" data-position="fixed" class="x-header">
        <h1>选择地址</h1>
        <a href="@if(!empty($nav_back_url)) {!! $nav_back_url !!} @else javascript:history.back(-1); @endif" data-iconpos="notext" class="x-back ui-nodisc-icon" data-shadow="false"></a>
        <a class="x-sjr ui-btn-right addr_save"><i class="x-okico"></i></a>
    </div>
    @stop
    <!-- /content -->
    <div role="main" class="ui-content">
        <div class="x-bgfff">
            <div class="x-map">
                <div id="qqMapContainer" style="min-width:100%;max-width:640px; height:100%;min-height:80%; position:absolute;left:0px;top:0px;"></div>
                <div class="x-search clearfix mapurl">
                    <div class="x-serbtn">
                        <i class="x-serico"></i>
                    </div>
                    <input placeholder="请输入小区、写字楼、学校等"/>
                </div>
            </div>
            <p class="f12 c-green x-lbg30">附近配送地址</p>
            <ul class="x-addlst">
            </ul>
        </div>
    </div>
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

    jQuery(function ($)
    {   
        $(window).load(function(){   
            if(cartIds != '') {
                url += "?cartIds=" + cartIds;
                mapurl += "?cartIds=" + cartIds;
            }
            if(plateId > 0) {
                url += "?plateId=" + plateId + "&postId=" + postId;
                mapurl += "?plateId=" + plateId + "&postId=" + postId;
            }
            if(id > 0) {
                mapurl += "?id=" + id;
                url += "?id=" + id;
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
                        //console.log(nearPoi);
                        if (nowNearPoi == null || nowNearPoi.dist > nearPoi.dist) {
                            nowNearPoi = nearPoi;
                        }

                        html += '<li data-mappoint="'+nearPoi.latLng+'" data-name="'+nearPoi.name+'" class="nearpos"><i class="x-addico fl"></i><p ';
                        if(nearPoiKey == 0){
                            html += 'class="c-red">[当前]';
                        } else {
                            html += '>';
                        }
                        html += nearPoi.name+'</p><p class="c-green">'+nearPoi.address+'</p></li>';

                    }
                    $(".x-addlst").append(html);
                }
            });

            
            $(".addr_save").touchend(function() {
                var address = $(".c-red").parent().data('name')
                var mapPoint = $(".c-red").parent().data('mappoint');
                var data = {
                        "address":address,
                        "mapPoint":mapPoint
                    }; 
                $.post("{{ u('UserCenter/saveMap') }}",data,function(res){
                    window.location.href= url;
                },"json");
            })

            $(".x-addlst").on("click", "li", function () {
                var address = $(this).data('name')
                var mapPoint = $(this).data('mappoint');
                var data = {
                        "address":address,
                        "mapPoint":mapPoint
                    }; 
                $.post("{{ u('UserCenter/saveMap') }}",data,function(res){
                    window.location.href= url;
                },"json");
            });

            $(".mapurl").touchend(function() {
                window.location.href = mapurl;
            })

        })

    });
</script>
@stop