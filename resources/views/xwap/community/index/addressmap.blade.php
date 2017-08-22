@extends('xwap.community._layouts.base')

@section('show_top')
    <header class="bar bar-nav">
        <a class="button button-link button-nav pull-left" href="@if(!empty($nav_back_url)) {!! $nav_back_url !!} @else javascript:$.back(); @endif" data-transition='slide-out'>
            <span class="icon iconfont">&#xe600;</span>
        </a>
        <a href="{{ u('Index/cityservice',['type'=>2]) }}"><h1 class='title'>{{$cityinfo['name']}}<i class="icon iconfont f14 ml5 c-gray">&#xe601;</i></h1></a>
    </header>
@stop


@section('content')
    <style>
        .y-xzaddrcont{background: #efeff4}
        .x-comment.list-block .item-content{align-items: center; -webkit-align-items: center;}
        .x-comment li.active .y-addron{text-indent: 0; border:0;}
    </style>

    <div class="content y-xzaddrcont">
        <div class="y-map">
            <div id="qqMapContainer" style="min-width:100%;height:4em;min-height:80%;"></div>
            <div class="searchbar mapurl">
                <div class="search-input">
                    <label class="icon iconfont c-gray2" for="search">&#xe65e;</label>
                    <input type="search" id="search" placeholder="输入小区、写字楼、学校等">
                </div>
            </div>
        </div>
        <div class="pt5 pb5 pl10 pr10 f12 c-bgfff c-black mb10" id="nowaddress">
            <i class="icon iconfont">&#xe60d;</i><span id="myaddress">自动定位当前地址</span><i class="icon iconfont f12 fr">&#xe602;</i>
        </div>

        <div class="list-block media-list x-comment nobor">
            <ul>
        @if(!empty($list))
            @foreach($list as $v)
                <li class="x-setDuf @if($defaultAddress['addressId'] == $v['id']) active @endif" data-id="{{ $v['id'] }}">
                    <div class="item-content">
                        <div class="item-media">
                            <i class="icon iconfont mr5 f20 vam c-red y-addron x-setDuf">&#xe612;</i>
                        </div>
                        <div class="item-inner">
                            <div class="item-title-row c-black f14">
                                <div class="item-title">{{ $v['name'] }}</div>
                                <div class="item-after">{{ $v['mobile'] }}</div>
                            </div>
                            <div class="item-subtitle f12 c-gray">{{ $v['address'] }}</div>
                        </div>
                    </div>
                </li>
            @endforeach
        @endif
            </ul>
        </div>
        @if(!empty($userId))
            <a href="{{ u('UserCenter/address',['change'=>2])}}"><div class="tc pt5 pb5 c-bgfff mt10"><i class="icon iconfont c-red f16 mr5">&#xe61d;</i>管理收货地址</div></a>
        @endif

    </div>
@stop

@section($js)
<script type="text/javascript">
    var mapurl = "{{ u('Index/addrsearch') }}";
    $(document).on("touchend",".mapurl",function(){
        window.location.href = mapurl;
    })
</script>
<script charset="utf-8" src="http://map.qq.com/api/js?v=2.exp&key=2N2BZ-KKZA4-ZG4UB-XAOJU-HX2ZE-HYB4O&libraries=geometry,convertor"></script>
<script type="text/javascript">
    //精确定位
    $(function(){
        $(".x-comment li").click(function(){
            var id = $(this).attr("data-id");
            $.setDefaultAdd(id);
        })

        $.setDefaultAdd = function(id){
            $.post("{{ u('UserCenter/setdefault2') }}",{id:id},function(res){
                if(res.code == 0){
                    $.router.load("{!! u('Index/index') !!}", true);
                }
            },"json");
        }

        $("#nowaddress").click(function(){
            $("#myaddress").html('定位中请稍候');

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

                        //重置定位地址
                        var mapPointStr = result.detail.location.lat+","+result.detail.location.lng;
                        var city = result.detail.addressComponents.city;

                        var data = {
                            "address":address,
                            "mapPointStr":mapPointStr,
                            "city":city
                        };
                        $.post("{{ u('Index/relocation2') }}",data,function(status){
                            if(status.code == 1){
                                $.toast("抱歉，当前城市未开通服务，请选择其他城市吧");
                                $("#myaddress").html("抱歉，当前城市未开通服务，请选择其他城市吧");
                            }else{
                                $("#myaddress").html(address);
                                $.router.load("{!! u('Index/index') !!}", true);
                            }
                        },'json')
                    }
                });

                var citylocation = new qq.maps.CityService({
                    complete: function (result) {
                        clientLatLng = result.detail.latLng;
                        qqcGeocoder.getAddress(result.detail.latLng);
                        $.computeDistanceBegin();
                    }
                });

                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(translatePoint, function (error){
                        citylocation.searchLocalCity();
                    },{enableHighAccuracy: true});
                } else {
                    citylocation.searchLocalCity();
                }
            }


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

        })

    });
</script>

@stop