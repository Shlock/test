@extends('xwap.community._layouts.base')

@section('show_top')
<header class="bar bar-nav y-barnav">
    <a class="button button-link button-nav pull-left back" href="#" data-transition='slide-out'>
        <span class="icon iconfont">&#xe600;</span>
    </a>
    <div class="searchbar" style="position: absolute;margin: 0 10%;width: 72%;">
        <div class="search-input">
            <label class="icon iconfont c-gray2 f14" for="search">&#xe65e;</label>
            <input type="search" id="addr_search" placeholder="请输入小区、写字楼、学校等">
			<span class="del iconfont" style="position:absolute;top:1px;right:10px;display:none;">&#xe630;</span>
        </div>	
    </div>
	<a class="button button-link button-nav pull-right open-popup addr_save" data-popup=".popup-about" id="search_btn" >
        <span class="icon iconfont c-gray f15">搜索</span>
    </a>
</header>

@stop

@section('content')
    
    <!-- /content -->
    <!-- <div role="main" class="ui-content" id="addlst">
    </div>
    <div id="qqMapContainer" style="display:none;min-width:100%;max-width:640px; height:100%;min-height:80%; position:absolute;left:0px;top:0px;"></div> -->

    <div class="content y-xzaddrcont">
        <div id="qqMapContainer" style="display:none;min-width:100%;max-width:640px; height:100%;min-height:80%; position:absolute;left:0px;top:0px;"></div>
        <div class="list-block y-syt y-xzaddr" id="addlst">
            <!-- <ul>
                <li class="item-content">
                    <div class="item-inner">
                        <div class="item-title">
                            <i class="icon iconfont c-gray f20">&#xe60d;</i><span class="f14">[当前]群生国际E区</span>
                            <p class="f12 c-gray ml20">八一七中路</p>
                        </div>
                    </div>
                </li>
            </ul> -->
        </div>
    </div>
@stop

@section($js)
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
    var change = "{{ Input::get('change') }}";
    var mapCenter = null;
    
    $(function(){
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
            mapurl += "?id=" + id;
            url += "?id=" + id;
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
                if (result.detail.nearPois.length > 0) {
                    html += '<ul class="x-addlst">';
                    for(var nearPoiKey in result.detail.nearPois){
                        nearPoi = result.detail.nearPois[nearPoiKey];
                        if (nowNearPoi == null || nowNearPoi.dist > nearPoi.dist) {
                            nowNearPoi = nearPoi;
                        }
                        html += '<li data-mappoint="'+nearPoi.latLng+'" data-name="'+nearPoi.name+'" data-city="'+result.detail.addressComponents.city+'" class="item-content"><div class="item-inner"><div class="item-title"><i class="icon iconfont c-gray f20">&#xe60d;</i><span class="f14">'+nearPoi.name+'<p class="f12 c-gray ml20">'+nearPoi.address+'</p></div></div></li>';
                    }
                    html += '</ul>';
                   
                } else {
                    html += '<div class="x-null pa w100 tc"><i class="icon iconfont">&#xe645;</i><p class="f12 c-gray mt10">很抱歉！未找到符合条件的地址</p></div>';
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
            var addr = "{{ $cityinfo['name'] }}";
            addr += $.trim($("#addr_search").val());

			if(addr!=''){
				$('.search-input .del').show();
			}else{
				$('.search-input .del').hide();
			}

        });

        $('#search_btn').click(function() {
            var addr = "{{ $cityinfo['name'] }}";
            addr += $.trim($("#addr_search").val());
			if(addr==''){
				$.alert('请输入搜索关键字');return false;
			}
            qqGeocoder.getLocation(addr);
        });
		$('.search-input .del').click(function(){
			$("#addr_search").val('').focus();
			$(this).hide();
		});
        $("#addlst").on("click", ".x-addlst li", function () {
            var address = $(this).attr('data-name')
            var mapPoint = $(this).attr('data-mappoint');
            var city = $(this).attr('data-city');
            var data = {
                    "address":address,
                    "mapPoint":mapPoint,
                    "city":city
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
</script>
@stop