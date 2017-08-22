@extends('wap.community._layouts.base_order')
@section('content')
    <!-- /header -->
    @section('show_top')
    <div data-role="header" data-position="fixed" class="x-header">
        <a href="@if(!empty($nav_back_url)) {!! $nav_back_url !!} @else javascript:history.back(-1); @endif" data-iconpos="notext" class="x-back ui-nodisc-icon" data-shadow="false"></a>
        <div class="x-search clearfix">
            <div class="x-serbtn">
                <i class="x-serico"></i>
            </div>
            <input type="text" placeholder="请输入小区、写字楼、学校等" id="addr_search"/>
        </div>
    </div>
    @stop
    <!-- /content -->
    <div role="main" class="ui-content" id="addlst">
    </div>
    <div id="qqMapContainer" style="display:none;min-width:100%;max-width:640px; height:100%;min-height:80%; position:absolute;left:0px;top:0px;"></div>
<script charset="utf-8" src="http://map.qq.com/api/js?v=2.exp&key=2N2BZ-KKZA4-ZG4UB-XAOJU-HX2ZE-HYB4O&libraries=geometry,convertor"></script>
<script type="text/javascript">
    var qqGeocoder,qqcGeocoder,qqMap,qqMarker,currentLat,currentLon = null,citylocation = null;
    var mapurl = "{{ u('UserCenter/addressmap') }}";
    var url = "{{ u('UserCenter/addressdetail') }}";
    var nullimg = "{{  asset('wap/community/client/images/ico/error.png') }}";
    var cartIds = "{{ Input::get('cartIds') }}";
    var id = "{{ Input::get('id') }}";
    var plateId = "{{ Input::get('plateId') }}";
    var postId = "{{ Input::get('postId') }}";
    var mapCenter = null;
    
    jQuery(function($){   
        var padding = $(window).height()*0.2;

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
            qqMap = new qq.maps.Map(document.getElementById('qqMapContainer'),{
                zoom: 13
            });
            qqMarker = new qq.maps.Marker({
                map:qqMap,
                draggable:true
            });

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

            $.setLocation = function(latLng) {
                qqMap.setCenter(latLng);
                qqMarker.setPosition(latLng);
            }

            qqcGeocoder = new qq.maps.Geocoder({
                complete : function(result){
                    var nowNearPoi = null;
                    var nearPoi;
                    html = '';
                    //alert(result.detail.nearPois.length);
                    if (result.detail.nearPois.length > 0) {
                        html += '<div class="x-bgfff" style="padding-top:5px;"><ul class="x-addlst">';
                        for(var nearPoiKey in result.detail.nearPois){
                            nearPoi = result.detail.nearPois[nearPoiKey];
                            //console.log(nearPoi);
                            if (nowNearPoi == null || nowNearPoi.dist > nearPoi.dist) {
                                nowNearPoi = nearPoi;
                            }
                            html += '<li data-mappoint="'+nearPoi.latLng+'" data-name="'+nearPoi.name+'" class="nearpos"><i class="x-addico fl"></i><p>'+nearPoi.name+'</p><p class="c-green">'+nearPoi.address+'</p></li>';
                        }
                        html += '</ul></div>';
                       
                    } else {
                        html += '<div class="x-null" style="padding-top:'+padding+'px;"><img src="'+nullimg+'"><p class="f12 c-green mt10">未找到符合条件的地址</p><a class="ui-btn redbtn">使用地图选点</a></div>';
                    }
                    
                    $("#addlst").append(html);
                }
            });

            qqGeocoder = new qq.maps.Geocoder({
                complete : function(result){
                    //alert(result.detail.location);
                    $("#addlst").empty();
                    $.setLocation(result.detail.location);
                    qqcGeocoder.getAddress(result.detail.location);
                }
            });

            $('#addr_search').keyup(function() {
                var addr = $.trim($("#addr_search").val());
                qqGeocoder.getLocation(addr);
            });

            $("#addlst").on("click", ".x-addlst li", function () {
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

            $("#addlst").on("click", ".x-null a", function () {
                window.location.href= mapurl;
            });

           // $("#addlst .x-null").css("padding-top",$(window).height()*0.2);   
        })

    })
</script>
@stop