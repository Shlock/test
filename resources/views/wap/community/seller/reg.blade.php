@extends('wap.community._layouts.base')
@section('css')
    <style>
        #avatar-upload-loading {
            background:rgba(255,255,255,0.5);
            width:64px;
            height:64px;
            position:absolute;
            left:20px;
            top:0px;
            color:#08c894;
            line-height:64px;
            text-align: center;
            display: none;
            overflow: hidden;
            font-size:0.75em
        }
        #avatar-img{
            display: block;
            border-radius:64px; 
            height: 64px;
            z-index:0;
            top:-7px;
            left: 20px;
        }
        #idcardPositiveImg-img{
            display: block;  
            height: 64px;
            z-index:0;
            top:-7px;
            left: 20px;
        }
        #idcardNegativeImg-img{
            display: block;  
            height: 64px;
            z-index:0;
            top:-7px;
            left: 20px;
        }
        #businessLicenceImg-img{
            display: block; 
            height: 64px;
            z-index:0;
            top:-7px;
            left: 20px;
        }
        #upload-a{
              height: 64px;
              border-radius: 100%;
              position: absolute;
              right: 2.5em;
              top: 1em;
        }
        .upload-div{
            display:none
        }
        .tjdz1 .search{width: 80%; float: left;}
        .tjdz1 .search .ui-input-text{margin: 0; border-radius: 5px 0 0 5px;}
        .tjdz1 .search input{line-height: 45px; padding: 0 10px;}
        .tjdz1 .btn{width: 20%; float: left;}
        .tjdz1 .x-btnsure{padding: 0; width: 100%; line-height: 45px; border-radius: 0 5px 5px 0; text-shadow: none; background: #ff2d4b;}
    </style>
@stop
@section('js')
    <script charset="utf-8" src="http://map.qq.com/api/js?v=2.exp&key=2N2BZ-KKZA4-ZG4UB-XAOJU-HX2ZE-HYB4O&libraries=geometry,convertor"></script>
@stop
@section('show_top')
    <div data-role="header" data-tap-toggle="false" data-position="fixed" class="x-header">
        <h1>我要开店</h1>
        <a href="@if(!empty($nav_back_url)) {{ $nav_back_url }} @else javascript:history.back(-1); @endif" data-iconpos="notext" class="x-back ui-nodisc-icon" data-shadow="false"></a>
        <a href="javascript:;" class="x-sjr ui-btn-right addr_save none" data-addr="true"><i class="x-okico"></i></a>
    </div> 
@stop
@section('content')
    <div class="x-tjdz showx-tjdz" style="z-index:2;position: absolute;display: none;width: 95%;">
        <div class="tjdz1" style="margin:5px 0px 0px 10px;">
            <div class="search">
                <input type="text" id="address1" placeholder="点击输入详细地址" />
            </div>
            <div class="btn">
                <button class="x-btnsure addr_save1">搜索</button>
            </div>
        </div>
    </div>
    <!-- /content -->
    <div class="y-wykd">
        <ul style="border-top:0;">
            <php>
                $option['sellerType'] = (int)$option['sellerType'] ? $option['sellerType'] : 2;
            </php>
            <li class="y-wykdshlx on"><span><i class="c-red">*</i>商户类型</span>
                <span class="y-wykdr y-sjjm curent_type f14">
                @if($option['sellerType'] == 2)
                商家加盟
                @else
                个人加盟
                @endif
                </span> 
            </li>
            <ul class="y-sjlx none seller_type">
                <li class="@if($option['sellerType'] == 1) on @endif" data-type="1">个人加盟</li>
                <li class="@if($option['sellerType'] == 2) on @endif" data-type="2">商家加盟</li>
            </ul>
            <li class="y-dplogoh"><span><i class="c-red">*</i>店铺LOGO</span>

                <label for="image-form-file-1" id="upload-a">
                    @if(!empty($option['logo']))
                        <img id="avatar-img" src="{{ formatImage($option['logo'],100,100) }}" alt="">
                    @else
                        <img id="avatar-img" src="{{ asset('wap/community/client/images/wykdimg.png') }}" alt="">
                    @endif
                </label> 
                <div id="avatar-upload-loading">
                    上传中...
                </div>
                <div class="upload-div">
                    @yizan_begin
                    <yz:imageFrom iscropper="1"  maxwidth="200" maxhight="200" name="logo" id="logoInput" image="$option['logo']" toimg="avatar-img" loading="avatar-upload-loading"></yz:imageFrom>
                    @yizan_end
                </div>                
                <i class="x-rightico"></i>
            </li>
            <li><span><i class="c-red">*</i>店铺名称</span><span class="c-green f14 y-wykdr"><input id="name" value="{{$option['name']}}" type="text" name="name" placeholder="请输入店铺名称"></span></li>
            <li class="business_type"><span><i class="c-red">*</i>经营类型</span>
                @if($cate_str)
                <span class="c-green f14 y-wykdrjt">{{$cate_str}}</span>
                @else
                <span class="c-green f14 y-wykdrjt">未选择</span>
                @endif
                <i class="x-rightico"></i></li>
        </ul>
        <ul>
            <li><span><i class="c-red">*</i>店铺地址</span><span class="c-green f14 y-wykdr"><input class="y-edittxt f14" id="address" value="{{$option['address']}}" type="text" name="address" placeholder="请输入店铺地址"></span></li>
            <li><span></span><span class="c-green f14 y-wykdr"><input class="y-edittxt f14" id="address_detail" value="{{$option['addressDetail']}}" type="text" name="address_detail" placeholder=""></span></li>
            <li><span><i class="c-red">*</i>服务范围</span><span class="c-green f14 y-wykdr" id="mapPos"> 
                @if(!empty($option['mapPosStr']))
                已选择
                @else
                未选择
                @endif 
                <input class="y-edittxt f14" id="mapPosStr" value="{{$option['mapPosStr']}}" type="hidden" name="mapPosStr" >
                <input class="y-edittxt f14" id="mapPoint" value="{{$option['mapPoint']}}" type="hidden" name="mapPoint" >
            </span></li>
            <li><span><i class="c-red">*</i>手机号码</span><span class="c-green f14 y-wykdr"><input class="y-edittxt f14" readonly="true" id="mobile" value="{{$login_user['mobile']}}" type="text" name="mobile" placeholder="请输入手机号码"></span></li>
            <li><span><i class="c-red">*</i>店主/法人代表</span><span class="c-green f14 y-wykdr"><input class="y-edittxt f14" id="contacts" value="{{$option['contacts']}}" type="text" name="contacts" placeholder="请输入店主/法人代表姓名"></span></li>
            <li><span><i class="c-red">*</i>服务电话</span><span class="c-green f14 y-wykdr"><input class="y-edittxt f14" id="serviceTel" value="{{$option['serviceTel']}}" type="text" name="serviceTel" placeholder="请输入服务电话"></span></li>
            <!-- <li><span>起送费</span><span class="c-green f14 y-wykdr"><input class="y-edittxt f14" id="serviceFee" value="{{$login_user['serviceFee']}}" type="text" name="serviceFee" placeholder="请输入起送费"></span></li>
            <li><span>配送费</span><span class="c-green f14 y-wykdr"><input class="y-edittxt f14" id="deliveryFee" value="{{$login_user['deliveryFee']}}" type="text" name="deliveryFee" placeholder="请输入配送费"></span></li> -->
            <li><span><i class="c-red">*</i>身份证号码</span><span class="c-green f14 y-wykdr"><input class="y-edittxt f14" id="idcardSn" value="{{$option['idcardSn']}}" type="text" name="idcardSn" placeholder="请输入身份证号码" maxlength="18"></span></li>
            <li class="y-dplogoh"><span><i class="c-red">*</i>身份证正面照片</span>

                <label for="image-form-file-2" id="upload-a">
                    @if(!empty($option['idcardPositiveImg']))
                        <img id="idcardPositiveImg-img" src="{{ formatImage($option['idcardPositiveImg'],100,100) }}" alt="">
                    @else
                        <img id="idcardPositiveImg-img" src="{{ asset('wap/community/client/images/sfzm.png') }}" alt="">
                    @endif
                </label>  
                <div class="upload-div">
                    @yizan_begin
                    <yz:imageFrom iscropper="1" name="idcardPositiveImg" id="idcardPositiveImgInput" image="$option['idcardPositiveImg']" toimg="idcardPositiveImg-img" ></yz:imageFrom>
                    @yizan_end
                </div>                
                <i class="x-rightico"></i>
            </li>
            <li class="y-dplogoh"><span><i class="c-red">*</i>身份证背面照片</span>

                <label for="image-form-file-3" id="upload-a">
                    @if(!empty($option['idcardNegativeImg']))
                        <img id="idcardNegativeImg-img" src="{{ formatImage($option['idcardNegativeImg'],100,100) }}" alt="">
                    @else
                        <img id="idcardNegativeImg-img" src="{{ asset('wap/community/client/images/sfbm.png') }}" alt="">
                    @endif
                </label>  
                <div class="upload-div">
                    @yizan_begin
                    <yz:imageFrom iscropper="1" name="idcardNegativeImg" id="idcardNegativeImgInput" image="$option['idcardNegativeImg']" toimg="idcardNegativeImg-img" ></yz:imageFrom>
                    @yizan_end
                </div>                
                <i class="x-rightico"></i>
            </li> 
            <li class="y-dplogoh"><span><i class="c-red">*</i>证件照片</span>

                <label for="image-form-file-4" id="upload-a">
                    @if(!empty($option['businessLicenceImg']))
                        <img id="businessLicenceImg-img" src="{{ formatImage($option['businessLicenceImg'],100,100) }}" alt="">
                    @else
                        <img id="businessLicenceImg-img" src="{{ asset('wap/community/client/images/yyzz.png') }}" alt="">
                    @endif
                </label>  
                <div class="upload-div">
                    @yizan_begin
                    <yz:imageFrom iscropper="1" name="businessLicenceImg" id="businessLicenceImgInput" image="$option['businessLicenceImg']" toimg="businessLicenceImg-img" ></yz:imageFrom>
                    @yizan_end
                </div>                
                <i class="x-rightico"></i>
            </li> 
            <li>
                <strong class="y-dpjs f16">店铺介绍</strong>
                <textarea class="f14 c-green" id="introduction" name="introduction" maxlength="200" placeholder="选填，200字以内" onfocus="javascript:this.style.textAlign='left';">{{$option['introduction']}}</textarea>
            </li>
        </ul>

        <div class="y-but"><button class="submit_btn">提交</button></div>
    </div> 

    <div id="qqMapContainer" style="display: none;min-width:100%;max-width:640px; height:100%;min-height:100%; z-index:1;position:absolute;left:0px;top:0px;"></div>
    <input type="hidden" id="map_point" value="{{$option['mapPointStr']}}" />
    <script type="text/javascript">
        function getData(){
            var obj = new Object();
            obj.sellerType = $(".seller_type .on").data('type');
            obj.logo = $("#logoInput").val();
            obj.name = $("#name").val();
            obj.mobile = $("#mobile").val();
            obj.address = $("#address").val();
            obj.addressDetail = $("#address_detail").val();
            obj.idcardSn = $("#idcardSn").val();
            obj.contacts = $("#contacts").val();
            obj.serviceFee = $("#serviceFee").val();
            obj.serviceFee = $("#serviceFee").val();
            obj.serviceTel = $("#serviceTel").val();
            obj.mapPointStr = $("#map_point").val();
            obj.mapPosStr = $("#mapPosStr").val();
            obj.mapPoint = $("#mapPoint").val();
            obj.idcardNegativeImg = $("#idcardNegativeImgInput").val();
            obj.idcardPositiveImg = $("#idcardPositiveImgInput").val();
            obj.businessLicenceImg = $("#businessLicenceImgInput").val();
            obj.introduction = $("#introduction").val();
           // console.log(obj);
            if (obj.sellerType == 'undefined') {
                obj.sellerType = 1;
            };
            return obj;
        }
        // $(".y-sfzdt").click(function() {
        //     $(".y-sfzall").removeClass('none').show();
        //     return false;
        // })
        // $(document).on("touchend",".y-sfzdalw",function(){
        //     $(".y-sfzall").hide();
        //     return false;
        // })

        // $(".y-del").click(function() {
        //     $(this).parent().remove();
        //     if(!$(".y-wykd ul li p b").hasClass("y-sfz")){
        //         $(".y-wykd ul li p").append('身份证正反面必选<input type="file" class="y-file">');
        //     }
        // })
        $(".y-wykdshlx").touchend(function() {  
            if($(".y-sjjm").hasClass("on")){
                $(".y-sjjm").removeClass("on");
                $(".y-sjlx").addClass('none').hide();
            }else{
                $(".y-sjlx").removeClass('none').show();
                $(".y-sjjm").addClass("on");
            }
        })
        /*$(".y-wykdshlx").click(function() {
            $(".y-sjlx").removeClass('none').show();
            $(".y-sjjm").addClass("on");
        })*/
        $(".y-sjlx li").touchend(function(){
            $(this).addClass("on").siblings().removeClass("on");
            if($(this).data('type') == 1){
                $(".curent_type").text('个人加盟');
            } else {
                $(".curent_type").text('商家加盟');
            }
        });
        $(".business_type").click(function(){
            var data = getData();
            $.post("{{u('Seller/saveRegData')}}", data, function(res){ 
                window.location.href = "{{u('Seller/cate')}}";
            }, 'json');
        });
        $(".submit_btn").click(function(){
            var data = getData();
            $.post("{{u('Seller/doreg')}}", data, function(res){
                if(res.status == true){
                    $.showSuccess('开店申请提交成功,请等待审核');
                    window.location.href = "{{u('Index/index')}}";
                } else {
                    $.showError(res.msg);
                }
            }, 'json');
        });

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
                $(".addr_save").touchend(function() {
                    $(".y-wykd").css("z-index",400);
                    $("#qqMapContainer").css("display","none");
                    $(".showx-tjdz").css("display","none");
                    $(".y-wykd").css("display","block");
                    $(".addr_save").addClass("none");
                })

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
                $(".y-wykd").css("z-index",1);
                $("#qqMapContainer").css("display","block");
                $(".showx-tjdz").css("display","block");
                $(".y-wykd").css("display","none");
                 $(".addr_save").removeClass("none");
            });
            
            $("#mapPos").click(function(){
                var data = getData();
                $.post("{{u('Seller/saveRegData')}}", data, function(res){ 
                    window.location.href="{{u('Seller/map')}}";
                }, 'json'); 
            })
        })
    </script>
@stop 

