@extends('xwap.community._layouts.base')

@section('css')
<style>
    .y-maptwo {
        max-height: none;
        position: relative;
        overflow: hidden;
        height: 500px;
    }
</style>
@stop

@section('show_top')
    <header class="bar bar-nav">
        <a class="button button-link button-nav pull-left" href="{{ u('UserCenter/index') }}" data-transition='slide-out'>
            <span class="icon iconfont">&#xe600;</span>
        </a>
        <a class="button button-link button-nav pull-right open-popup addr_save" data-popup=".popup-about" style="display:none">
            <span class="icon iconfont c-gray f24">&#xe610;</span>
        </a>
        <h1 class="title f16">我要开店</h1>
    </header>
@stop

@section('content')
    <!-- new -->
    <div class="content" id=''>
        <!-- 地图 -->
        <div style="display: none;" class="sellermap">
            <div class="searchbar row c-bgfff ml0 mt10 y-dpaddrsac">
                <div class="search-input col-80">
                    <input type="search" id="address1" class="f16" value="">
                </div>
                <a class="button button-fill col-20 y-searchbtn c-black f16 addr_save1">搜索</a>
            </div>
            <div class="y-maptwo">
                <div id="qqMapContainer" style="min-width:100%;max-width:640px; height:100%;min-height:100%; z-index:1;position:absolute;left:0px;top:0px;"></div>
            </div>
        </div>

        <!-- 表单 -->
        <div class="list-block media-list y-syt y-wykd">
            <ul>
                <php>
                    $option['sellerType'] = (int)$option['sellerType'] ? $option['sellerType'] : 2;
                </php>
                <li class="item-content">
                    <div class="item-inner f14">
                        <div class="item-title-row">
                            <div class="item-title">
                                <i class="c-red vat mr5">*</i>
                                <span>商户类型</span>
                            </div>
                            <div class="item-after y-league">
                                <span class="c-black">
                                    @if($option['sellerType'] == 2)
                                        商家加盟
                                    @else
                                        个人加盟
                                    @endif
                                </span>
                                <i class="icon iconfont ml10 c-gray2 y-down">&#xe601;</i>
                                <i class="icon iconfont ml10 c-gray2 y-up y-none">&#xe603;</i>
                            </div>
                        </div>
                    </div>
                </li>
                <li class="item-content">
                    <div class="item-inner f14">
                        <div class="item-title-row">
                            <div class="item-title">
                                <i class="c-red vat mr5">*</i>
                                <span>店铺LOGO</span>
                            </div>
                            <div class="item-after y-wykdimg">
                                <form>
                                    <span class="c-black">
                                        <label for="image-form-file-1" id="upload-a">
                                            @if(!empty($option['logo']))
                                                <img id="_logoInput" src="{{ formatImage($option['logo'],100,100) }}" alt="" class="y-logo">
                                            @else
                                                <img id="_logoInput" src="{{ asset('wap/community/client/images/wykdimg.png') }}" alt="" class="y-logo">
                                            @endif
                                        </label> 
                                        <input type="text" id="logoInput" value="{{$option['logo']}}" class="hideImg" style="display:none;">
                                    </span>
                                    <input id="image-form-file-1" type="file" onchange="image_form_file_1_onchange(this)" accept="image/*" style="display:none" />
                                    <script type="text/javascript">
                                        function image_form_file_1_onchange(sender)
                                        {
                                            PhotoCutUpload(sender, 200, 200, "{{u('Resource/upload')}}", function (result)
                                            {
                                                if (result.status == true)
                                                {
                                                    $("img", $(sender.form)).attr("src", result.data);
                                                    $(".hideImg", $(sender.form)).val(result.data);
                                                }
                                            });
                                        }
                                    </script>
                                </form>
                                <i class="icon iconfont ml10 mt20 c-gray2 vat">&#xe602;</i>
                            </div>
                        </div>
                    </div>

                </li>
                <li class="item-content">
                    <div class="item-inner f14">
                        <div class="item-title-row">
                            <div class="item-title">
                                <i class="c-red vat mr5">*</i>
                                <span>店铺名称</span>
                            </div>
                            <div class="item-after y-wykdwidth">
                                <span class="c-black"><input id="name" value="{{$option['name']}}" type="text" name="name" placeholder="请输入店铺名称"></span>
                            </div>
                        </div>
                    </div>
                </li>
                <li class="item-content business_type">
                    <div class="item-inner f14">
                        <div class="item-title-row">
                            <div class="item-title">
                                <i class="c-red vat mr5">*</i>
                                <span>经营类型</span>
                            </div>
                            <div class="item-after">
                                @if($cate_str)
                                    <span class="c-gray2">{{$cate_str}}</span>
                                @else
                                    <span class="c-gray2">未选择</span>
                                @endif
                                
                                <i class="icon iconfont ml10 c-gray2">&#xe602;</i>
                            </div>
                        </div>
                    </div>
                </li>
            </ul>
            <ul class="mt10">
                <li class="item-content">
                    <div class="item-inner f14">
                        <div class="item-title-row">
                            <div class="item-title">
                                <i class="c-red vat mr5">*</i>
                                <span>店铺地址</span>
                            </div>
                            <div class="item-after" id="address">
                                <span class="c-gray2">{{$option['address']}}</span>
                                <i class="icon iconfont ml10 c-gray2">&#xe602;</i>
                            </div>
                        </div>
                    </div>
                </li>
                <li class="item-content">
                    <div class="item-inner f14">
                        <div class="item-title-row">
                            <div class="item-title">
                            </div>
                            <div class="item-after y-wykdwidth">
                                <span class="c-black"><input type="text" id="address_detail" value="{{$option['addressDetail']}}" name="address_detail" placeholder="详细地址(门牌号/楼层等)"></span>
                            </div>
                        </div>
                    </div>
                </li>
                <li class="item-content">
                    <div class="item-inner f14">
                        <div class="item-title-row">
                            <div class="item-title">
                                <i class="c-red vat mr5">*</i>
                                <span>服务范围</span>
                            </div>
                            <div class="item-after" id="mapPos">
                                <span class="c-black">
                                    @if(!empty($option['mapPosStr']))
                                        已选择
                                    @else
                                        未选择
                                    @endif
                                    <input class="y-edittxt f14" id="mapPosStr" value="{{$option['mapPosStr']}}" type="hidden" name="mapPosStr" >
                                    <input class="y-edittxt f14" id="mapPoint" value="{{$option['mapPoint']}}" type="hidden" name="mapPoint" >
                                </span>
                                <i class="icon iconfont ml10 c-gray2">&#xe602;</i>
                            </div>
                        </div>
                    </div>
                </li>
                <li class="item-content">
                    <div class="item-inner f14">
                        <div class="item-title-row">
                            <div class="item-title">
                                <i class="c-red vat mr5">*</i>
                                <span>手机号码</span>
                            </div>
                            <div class="item-after y-wykdwidth">
                                <span class="c-black">
                                    <input class="y-edittxt f14" readonly="true" id="mobile" value="{{$login_user['mobile']}}" type="text" name="mobile" placeholder="请输入手机号码" maxlength="11">
                                </span>
                            </div>
                        </div>
                    </div>
                </li>
                <li class="item-content">
                    <div class="item-inner f14">
                        <div class="item-title-row">
                            <div class="item-title">
                                <i class="c-red vat mr5">*</i>
                                <span>店主/法人代表</span>
                            </div>
                            <div class="item-after y-wykdwidth">
                                <span class="c-black">
                                    <input class="y-edittxt f14" id="contacts" value="{{$option['contacts']}}" type="text" name="contacts" placeholder="请输入店主/法人代表姓名">
                                </span>
                            </div>
                        </div>
                    </div>
                </li>
                <li class="item-content">
                    <div class="item-inner f14">
                        <div class="item-title-row">
                            <div class="item-title">
                                <i class="c-red vat mr5">*</i>
                                <span>服务电话</span>
                            </div>
                            <div class="item-after y-wykdwidth">
                                <span class="c-black">
                                    <input class="y-edittxt f14" id="serviceTel" value="{{$option['serviceTel']}}" type="text" name="serviceTel" placeholder="请输入服务电话">
                                </span>
                            </div>
                        </div>
                    </div>
                </li>
                <li class="item-content">
                    <div class="item-inner f14">
                        <div class="item-title-row">
                            <div class="item-title">
                                <i class="c-red vat mr5">*</i>
                                <span>身份证号码</span>
                            </div>
                            <div class="item-after y-wykdwidth">
                                <span class="c-black">
                                    <input class="y-edittxt f14" id="idcardSn" value="{{$option['idcardSn']}}" type="text" name="idcardSn" placeholder="请输入身份证号码" maxlength="18">
                                </span>
                            </div>
                        </div>
                    </div>
                </li>
                <li class="item-content">
                    <div class="item-inner f14">
                        <div class="item-title-row">
                            <div class="item-title">
                                <i class="c-red vat mr5">*</i>
                                <span>身份证正面照片</span>
                            </div>
                            <div class="item-after y-wykdimg">

                                <form>
                                  <span class="c-black">
                                         <label for="image-form-file-2" id="upload-a">
                                            @if(!empty($option['idcardPositiveImg']))
                                                <img id="_idcardPositiveImgInput" src="{{ $option['idcardPositiveImg'] }}" alt="">
                                            @else
                                                <img id="_idcardPositiveImgInput" src="{{ asset('wap/community/client/images/sfzm.png') }}" alt="">
                                            @endif
                                        </label>
                                        <input type="text" id="idcardPositiveImgInput" value="{{$option['idcardPositiveImg']}}" class="hideImg" style="display:none;">
                                    </span>
                                    <input id="image-form-file-2" type="file" onchange="image_form_file_2_onchange(this)" accept="image/*" style="display:none" />
                                    <script type="text/javascript">
                                        function image_form_file_2_onchange(sender)
                                        {
                                            PhotoCutUpload(sender, 200, 200, "{{u('Resource/upload')}}", function (result)
                                            {
                                                if (result.status == true)
                                                {
                                                    $("img", $(sender.form)).attr("src", result.data);
                                                    $(".hideImg", $(sender.form)).val(result.data);
                                                }
                                            }, "", false);
                                        }
                                    </script>
                                </form>
                                <i class="icon iconfont ml10 mt20 c-gray2 vat">&#xe602;</i>
                            </div>
                        </div>
                    </div>
                </li>
                <li class="item-content">
                    <div class="item-inner f14">
                        <div class="item-title-row">
                            <div class="item-title">
                                <i class="c-red vat mr5">*</i>
                                <span>身份证背面照片</span>
                            </div>
                            <div class="item-after y-wykdimg">
                                <form>
                                    <span class="c-black">
                                        <label for="image-form-file-3" id="upload-a">
                                            @if(!empty($option['idcardNegativeImg']))
                                                <img id="_idcardNegativeImgInput" src="{{ $option['idcardNegativeImg'] }}" alt="">
                                            @else
                                                <img id="_idcardNegativeImgInput" src="{{ asset('wap/community/client/images/sfbm.png') }}" alt="">
                                            @endif
                                        </label>
                                        <input type="text" id="idcardNegativeImgInput" value="{{$option['idcardNegativeImg']}}" class="hideImg" style="display:none;">
                                    </span>
                                    <input id="image-form-file-3" type="file" onchange="image_form_file_3_onchange(this)" accept="image/*" style="display:none" />
                                    <script type="text/javascript">
                                        function image_form_file_3_onchange(sender)
                                        {
                                            PhotoCutUpload(sender, 200, 200, "{{u('Resource/upload')}}", function (result)
                                            {
                                                if (result.status == true)
                                                {
                                                    $("img", $(sender.form)).attr("src", result.data);
                                                    $(".hideImg", $(sender.form)).val(result.data);
                                                }
                                            }, "", false);
                                        }
                                    </script>
                                </form>
                                <i class="icon iconfont ml10 mt20 c-gray2 vat">&#xe602;</i>
                            </div>
                        </div>
                    </div>
                </li>
                <li class="item-content">
                    <div class="item-inner f14">
                        <div class="item-title-row">
                            <div class="item-title">
                                <i class="c-red vat mr5">*</i>
                                <span>证件照片</span>
                            </div>
                            <div class="item-after y-wykdimg">
                                <form>
                                    <span class="c-black">
                                        <label for="image-form-file-4" id="upload-a">
                                            @if(!empty($option['businessLicenceImg']))
                                                <img id="_businessLicenceImgInput" src="{{ $option['businessLicenceImg'] }}" alt="">
                                            @else
                                                <img id="_businessLicenceImgInput" src="{{ asset('wap/community/client/images/yyzz.png') }}" alt="">
                                            @endif
                                        </label>
                                        <input type="text" id="businessLicenceImgInput" value="{{$option['businessLicenceImg']}}" class="hideImg" style="display:none;">
                                    </span>
                                    <input id="image-form-file-4" type="file" onchange="image_form_file_4_onchange(this)" accept="image/*" style="display:none" />
                                    <script type="text/javascript">
                                        function image_form_file_4_onchange(sender)
                                        {
                                            PhotoCutUpload(sender, 200, 200, "{{u('Resource/upload')}}", function (result)
                                            {
                                                if (result.status == true)
                                                {
                                                    $("img", $(sender.form)).attr("src", result.data);
                                                    $(".hideImg", $(sender.form)).val(result.data);
                                                }
                                            }, "", false);
                                        }
                                    </script>
                                </form>
                                <i class="icon iconfont ml10 mt20 c-gray2 vat">&#xe602;</i>
                            </div>
                        </div>
                    </div>
                </li>
                <li class="item-content">
                    <div class="item-inner f14">
                        <div class="item-title-row">
                            <div class="item-title y-dpjsh">
                                <i class="c-red vat mr5">*</i>
                                <span>店铺介绍</span>
                            </div>
                            <div class="item-after y-wykddpjs">
                                <span class="c-black">
                                    <textarea class="f14 c-green" id="introduction" name="introduction" maxlength="200" placeholder="选填，200字以内" onfocus="javascript:this.style.textAlign='left';">{{$option['introduction']}}</textarea>
                                </span>
                            </div>
                        </div>
                    </div>
                </li>
            </ul>
        </div>

        <input type="hidden" id="map_point" value="{{$option['mapPointStr']}}" />
        <p class="y-bgnone mb10"><a class="y-paybtn f16 submit_btn">提交</a></p>
    </div>
@stop

@section($js)
    <script charset="utf-8" src="http://map.qq.com/api/js?v=2.exp&key=2N2BZ-KKZA4-ZG4UB-XAOJU-HX2ZE-HYB4O&libraries=geometry,convertor"></script>
    
    <script type="text/javascript">
        var seller_type = 2; //个人加盟 商家加盟 , 默认商家加盟

        $.getData = function(){
            var obj = new Object();
            // obj.sellerType = $(".seller_type .on").data('type');
            obj.sellerType = seller_type;
            obj.logo = $("#logoInput").val();
            obj.name = $("#name").val();
            obj.mobile = $("#mobile").val();
            obj.address = $("#address span").text();
            obj.addressDetail = $("#address_detail").val();
            obj.idcardSn = $("#idcardSn").val();
            obj.contacts = $("#contacts").val();
            // obj.serviceFee = $("#serviceFee").val();
            // obj.serviceFee = $("#serviceFee").val();
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

        // $(document).on("touchend",".y-sfzdt",function(){
        //     $(".y-sfzall").removeClass('none').show();
        //     return false;
        // })
        // $(document).on("touchend",".y-sfzdalw",function(){
        //     $(".y-sfzall").hide();
        //     return false;
        // })

        // $(document).on("touchend",".y-del",function(){
        //     $(this).parent().remove();
        //     if(!$(".y-wykd ul li p b").hasClass("y-sfz")){
        //         $(".y-wykd ul li p").append('身份证正反面必选<input type="file" class="y-file">');
        //     }
        // })
        $(document).on("touchend",".y-wykdshlx",function(){
            if($(".y-sjjm").hasClass("on")){
                $(".y-sjjm").removeClass("on");
                $(".y-sjlx").addClass('none').hide();
            }else{
                $(".y-sjlx").removeClass('none').show();
                $(".y-sjjm").addClass("on");
            }
        })
        /*$(document).on("touchend",".y-wykdshlx",function(){
            $(".y-sjlx").removeClass('none').show();
            $(".y-sjjm").addClass("on");
        })*/
        $(document).on("touchend",".y-sjlx li",function(){
            $(this).addClass("on").siblings().removeClass("on");
            if($(this).data('type') == 1){
                $(".curent_type").text('个人加盟');
            } else {
                $(".curent_type").text('商家加盟');
            }
        });
        $(document).on("touchend",".business_type",function(){
            var data = $.getData();
            $.post("{{u('Seller/saveRegData')}}", data, function(res){ 
                $.router.load("{{u('Seller/cate')}}", true);
            }, 'json');
        });
        $(document).on("touchend",".submit_btn",function(){
            var data = $.getData();
            $.post("{{u('Seller/doreg')}}", data, function(res){
                if(res.status == true){
                    $.alert('开店申请提交成功,请等待审核');
                    $.router.load("{{u('Index/index')}}", true);
                } else {
                    $.alert(res.msg);
                }
            }, 'json');
        });

        var qqGeocoder,qqMap,qqMarker,citylocation = null;
        $(function(){ 
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
                    $("#address span").text(result.detail.address);
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
            // 确认地图
            $(document).on("touchend",".addr_save",function(){
                $(".y-wykd,.y-bgnone").css("z-index",400);
                // $("#qqMapContainer").css("display","none");
                $(".sellermap,.addr_save").css("display","none");
                // $(".showx-tjdz").css("display","none");
                $(".y-wykd,.y-bgnone").css("display","block");
                $(".addr_save").addClass("none");
            })
            // 搜索位置
            $(document).on("touchend",".addr_save1",function(){
                if($.trim($("#address1").val()) != ""){
                    qqGeocoder.getLocation($("#address1").val());
                    $("#detail_input").val($("#address1").val());
                    $("#address span").text($("#address1").val());
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

            // 显示地图
            $(document).on("touchend","#address",function(){
                $(".y-wykd,.y-bgnone").css("z-index",1);
                // $("#qqMapContainer").css("display","block");
                $(".sellermap,.addr_save").css("display","block");
                
                // $(".showx-tjdz").css("display","block");
                $(".y-wykd,.y-bgnone").css("display","none");
                $(".addr_save").removeClass("none");
            });
            
            $(document).on("touchend","#mapPos",function(){
                var data = $.getData();
                $.post("{{u('Seller/saveRegData')}}", data, function(res){
                    $.router.load("{{u('Seller/map')}}", true);
                }, 'json'); 
            })
        })


        $(document).on('touchend','.y-league', function () {
            $(".y-league .y-down").addClass("y-none").siblings("i").removeClass("y-none");
            var buttons1 = [
                {
                  text: '请选择',
                  label: true
                },
                {
                  text: '商家加盟',
                  onClick: function() {
                    $(".y-league span").text("商家加盟");
                    seller_type = 2;
                  }
                },
                {
                  text: '个人加盟',
                  onClick: function() {
                    $(".y-league span").text("个人加盟");
                    seller_type = 1; 
                  }
                }
            ];
            var buttons2 = [
                {
                  text: '取消',
                  bg: 'danger'
                }
            ];
            var groups = [buttons1, buttons2];
            $.actions(groups);
            
            $(document).on("touchend",".actions-modal-button",function(){
                $(".y-league .y-up").addClass("y-none").siblings("i").removeClass("y-none");
            });
        });
    </script>
@stop

