@extends('wap.community._layouts.base_order')
@section('css')
<style>
    .showcity{  border: 1px solid #000;
      text-align: center;
      height: 100%;
      position: fixed;
      top: 0px;
      width: 100%;
      background-color: rgba(204, 204, 204, 0.76);
    }
    .ui-select {width: 20%;float: left;margin: 225px 0 0 10px ;}
    .showcityok{width: 10%;top: 225px;position: absolute;right: 30px;margin-left: 10px}
    .x-editadd {position: relative; z-index: 400;}
    .nones{display: none;}
    .tjdz1 .search{width: 80%; float: left;}
    .tjdz1 .search .ui-input-text{margin: 0; border-radius: 5px 0 0 5px;}
    .tjdz1 .search input{line-height: 45px; padding: 0 10px;}
    .tjdz1 .btn{width: 20%; float: left;}
    .tjdz1 .x-btnsure{padding: 0; width: 100%; line-height: 45px; border-radius: 0 5px 5px 0; text-shadow: none; background: #ff2d4b;}
</style>
@stop
@section('js')
    <script charset="utf-8" src="http://map.qq.com/api/js?v=2.exp&key=2N2BZ-KKZA4-ZG4UB-XAOJU-HX2ZE-HYB4O&libraries=geometry"></script>
@stop
@section('content')
    <!-- /header -->
    @section('show_top')
        <div data-role="header" data-position="fixed" class="x-header">
            <h1>我的{{ $title }}</h1>
            <a href="@if(!empty($nav_back_url)) {!! $nav_back_url !!} @else javascript:history.back(-1); @endif" data-iconpos="notext" class="x-back ui-nodisc-icon" data-shadow="false"></a>
            <a href="javascript:;" class="x-sjr ui-btn-right addr_save" data-addr="true"><i class="x-okico"></i></a>
        </div>
    @stop
    <!-- /content -->
    <div role="main" class="ui-content">
        <div class="x-tjdz showx-tjdz" style="z-index:2;position: absolute;display: none;width: 95%;">
            <div class="tjdz1">      
                <div class="search">
                    <input type="text" id="address1" placeholder="点击输入详细地址" />
                </div> 
                <div class="btn">
                    <button class="x-btnsure addr_save1">确定</button>
                </div>
            </div>
        </div>
        <div class="x-lh45br x-editadd">
            <p>
                <span class="fl">收货人：</span>
                <input type="text" name="name"  id="name" placeholder="请输入收货人姓名" value="{{ $data['name'] }}" />
            </p>
            <p>
                <span class="fl"><span class="x-w3">电</span>话：</span>
                <input type="text" name="mobile"  id="mobile" placeholder="请输入收货人电话" value="{{ $data['mobile'] }}"/>
            </p>           
             <p>
                <span class="fl"><span class="x-w3">地</span>址：</span>
                <input type="text" name="detailAddress" id="address" placeholder="请输入地址区域"  value="{{ $data['address'] }}"/>
            </p>
            <p class="last">
                <input type="text" name="doorplate" id="doorplate" placeholder="输入楼号门牌号等详细信息"  value="{{ $data['doorplate'] }}"/>
            </p>
        </div>        
        <div id="qqMapContainer" style="display: none;min-width:100%;max-width:640px; height:100%;min-height:100%; z-index:1;position:absolute;left:0px;top:0px;"></div>
        <input type="hidden" id="map_point" />
        <input type="hidden" id="id" value="{{ $data['id'] }}" />
    </div>
    <!-- content end -->
<!--     <div class="showcity">
        <p class="showcityok ui-btn">确定</p>
        @yizan_begin
            <yz:region pname="provinceId" cname="cityId" aname="areaId" ></yz:region>
        @yizan_end
        <p style="clear: both;"></p>
    </div> -->
    <script type="text/javascript">

        var qqGeocoder,qqMap,qqMarker,citylocation = null;
        jQuery(function($){           
            $(window).load(function(){
                var mapCenter = null;
                qqMap = new qq.maps.Map(document.getElementById('qqMapContainer'),{
                    zoom: 14
                });
                qqMarker = new qq.maps.Marker({
                    map:qqMap,
                    draggable:true
                });

                //精确定位
                var isGeolocation = false;
                var translatePoint = function(position){
                    var currentLat = position.coords.latitude;
                    var currentLon = position.coords.longitude;
                    qq.maps.convertor.translate(new qq.maps.LatLng(currentLat, currentLon), 1, function(res){
                        isGeolocation = true;
                        latlng = res[0];
                        qqMap.setCenter(result.detail.latLng);
                        qqMarker.setPosition(result.detail.latLng);
                        $("#map_point").val(currentLat + ',' + currentLon);
                        qqcGeocoder.getAddress(latlng);
                    });
                }
                
                qq.maps.event.addListener(qqMarker, 'dragend', function(event) {
                    qqMarker.setPosition(event.latLng);
                    $("#map_point").val(event.latLng.getLat() + ',' + event.latLng.getLng());
                    qqcGeocoder.getAddress(event.latLng);
                });
                qq.maps.event.addListener(qqMap, 'click', function(event) {
                    qqMarker.setPosition(event.latLng);
                    $("#map_point").val(event.latLng.getLat() + ',' + event.latLng.getLng());
                    qqcGeocoder.getAddress(event.latLng);
                });
                
                citylocation = new qq.maps.CityService({
                    complete : function(result){
                        if (isGeolocation) {
                            return;
                        }
                        qqMap.setCenter(result.detail.latLng);
                        qqMarker.setPosition(result.detail.latLng);
                        $("#map_point").val(result.detail.latLng.getLat() + ',' + result.detail.latLng.getLng());
                        qqcGeocoder.getAddress(result.detail.latLng);
                    }
                });
                citylocation.searchLocalCity();



                qqcGeocoder = new qq.maps.Geocoder({
                    complete : function(result){
                        $("#address").val(result.detail.address);
                        $("#address1").val(result.detail.address);
                    }
                });

                qqGeocoder = new qq.maps.Geocoder({
                    complete : function(result){
                        qqMap.setCenter(result.detail.location);
                        qqMarker.setPosition(result.detail.location);
                        $("#map_point").val(result.detail.location.getLat() + ',' + result.detail.location.getLng());
                    }
                });
                $(".addr_save1").touchend(function() {
                    if($.trim($("#address1").val()) != ""){
                        qqGeocoder.getLocation($("#address1").val());
                        $("#detail_input").val($("#address1").val());
                        $("#address").val($("#address1").val());
                    }
                })

                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(translatePoint);
                }
                /*$("#address").change(function(){
                    if($.trim($("#address").val()) != ""){
                        qqGeocoder.getLocation($("#address").val());
                        $("#detail_input").val($("#address").val());
                    }
                })*/
            })
             $("#address").touchend(function(){
                $(".x-editadd").css("z-index",1);
                $("#qqMapContainer").css("display","block");
                $(".showx-tjdz").css("display","block");
                $(".x-editadd").css("display","none");
                $(".addr_save").data("addr",false);
            });


            //添加地址
            $(".addr_save").touchend(function(){
               var type =  $(this).data("addr");
               if(type == true){
                    // var provinceId = $("#provinceId").data("id");
                    // var cityId = $("#cityId").data("id");
                    // var areaId = $("#areaId").data("id");
                    var id =  $.trim($("#id").val());
                    var detailAddress =  $.trim($("#address").val());
                    var map_point = $.trim($("#map_point").val());
                    var name =  $.trim($("#name").val());
                    var mobile = $.trim($("#mobile").val());
                    var doorplate = $.trim($("#doorplate").val());

                    var data = {
                        "id":id,
                        "mobile":mobile,
                        "name":name,
                        "detailAddress":detailAddress,
                        "doorplate":doorplate,
                        "mapPoint":map_point
                    };
                    $.post("{{ u('UserCenter/saveaddress') }}",data,function(res){
                        if(res.code == 0){
                            var cartIds = "{{ Input::get('cartIds') }}";
                            if(cartIds == '') {
                                var return_url = "{{ u('UserCenter/address') }}";
                            }else{
                                var return_url = "{!! u('Order/order',['cartIds'=>Input::get('cartIds'),'addressId' => ADDID]) !!}".replace("ADDID", res.data.id);
                            }
                            $.showSuccess(res.msg,return_url);
                        }else{
                            $.showError(res.msg);
                        }
                    },"json");
                }else{
                    $(".x-editadd").css("z-index",400);
                    $("#qqMapContainer").css("display","none");
                    $(".showx-tjdz").css("display","none");
                    $(".x-editadd").css("display","block");
                    $(".addr_save").data("addr",true);
                }
            })
        })
    </script>
@stop