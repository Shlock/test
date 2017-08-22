@extends('wap.community._layouts.base')
@section('show_top')
@stop
@section('js')
    <script src="{{ asset('static/infinite-scroll/jquery.infinitescroll.js') }}"></script>
@stop
<php>
    $sorts = array(
        0 => '综合排序',
        1 => '按销量倒序',
        2 => '按起送价',
        3 => '距离最近',
        4 => '评分最高',
    );
</php>

@section('content') 
    <div role="main" class="ui-content">
        <ul class="x-sjsort clearfix">
            <li class="on">
                <p class="x-sortl x-sjsortt"><span>@if($args['id'] > 0 && $args['id'] == $cate['id']){{$cate['name']}} @else 全部 @endif</span><i class="x-sdown"></i></p>
                <div class="x-sortmask">
                    <div class="mask1"></div>
                    <div data-role="tabs" id="tabs" class="x-fwtype x-sortlst clearfix">
                        <div data-role="navbar" class="x-fwtypett">
                            <ul>
                                @foreach($seller_cates as $key => $item)
                                <li><a href="#row_{{$key+1}}" data-ajax="false" class="@if($key == 0)ui-btn-active first @endif" >{{$item['name']}}</a></li>
                                @endforeach
                            </ul>
                        </div>
                        @foreach($seller_cates as $k => $item)
                            <div id="row_{{$k+1}}">
                                <ul class="x-typelst typecate">
                                    <li data-id="{{$item['id']}}">全部</li>
                                    @foreach($seller_cates[$k]['childs'] as $val)
                                    <li data-id="{{$val['id']}}">{{$val['name']}}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endforeach
                    </div>
                </div>
            </li>
            <li class="x-sortr1">
                <p class="x-sortr x-sjsortt"><span>{{$sorts[$args['sort']]}}</span><i class="x-sdown"></i></p>
                <div class="x-sort2 typesort">
                    @foreach($sorts as $key => $st)
                    <p data-id="{{$key}}">{{$st}}</p>
                    @endforeach
                </div>
            </li>
        </ul>
        @if(!empty($data))
        <ul class="x-index4">
            @include('wap.community.seller.seller_item')
        </ul>
        @else
            <div class="x-serno c-green">
                <img src="{{ asset('wap/community/client/images/ico/cry.png') }}"  />
                <span>很抱歉！没有找到相关商家！</span>
            </div>
        @endif
    </div>

    @include('wap.community._layouts.swiper')
    <script charset="utf-8" src="http://map.qq.com/api/js?v=2.exp&key=2N2BZ-KKZA4-ZG4UB-XAOJU-HX2ZE-HYB4O&libraries=geometry"></script>
    <script>
        $.SwiperInit('.x-index4','li',"{{ u('Seller/index',$args) }}");
        var clientLatLng = null;

        jQuery(function($){

            $(document).on("touchend",".data-content ul li",function(){
                var id = parseInt($(this).data('id'));
                if(id > 0){
                    $.router.load("{{u('Seller/detail')}}"+"?staffId="+id, true);
                }
            });

            var clientLatLng = null;
            $.gpsposition = function ()
            {
                var translatePoint = function (position)
                {
                    var currentLat = position.coords.latitude;
                    var currentLon = position.coords.longitude;
                    clientLatLng = new qq.maps.LatLng(currentLat, currentLon);
                    $.computeDistanceBegin();
                }

                var citylocation = new qq.maps.CityService({
                    complete: function (result)
                    {
                        clientLatLng = result.detail.latLng;
                        $.computeDistanceBegin();
                    }
                });

                if (navigator.geolocation)
                {
                    navigator.geolocation.getCurrentPosition(translatePoint, function (error)
                    {
                        citylocation.searchLocalCity();
                    },
                    {
                        enableHighAccuracy: true,
                        maximumAge: 30000,
                        timeout: 5000
                    });
                }
                else
                {
                    citylocation.searchLocalCity();
                }
            }
   
            $.computeDistanceBegin = function ()
            {
                if (clientLatLng == null)
                {
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

            $.SwiperInit = function(box, item, url) {
                $(box).infinitescroll({
                    itemSelector    : item,
                    debug           : false,
                    dataType        : 'html', 
                    nextUrl         : url
                }, function(data) {
                    $.computeDistanceBegin();
                });
            }
            $.computeDistanceBegin();

            $(".typecate li").touchend(function() {
                $(this).addClass("on").siblings().removeClass("on");
                var id = $(this).data('id');
                $.post("{{u('Seller/index')}}",{'id':id},function(result){
                    if(result.code == 0){
                        $.router.load("{!! u('Seller/index',['sort'=>$args['sort'],'type'=>$args['type']])!!}&id=" + id, true);
                    } else {
                        $.showError(result.msg);
                    }
                },'json');
            });
            $(".typesort p").touchend(function() {
                var sort = $(this).data('id');
                $.post("{{u('Seller/index')}}",{'sort':sort},function(result){
                    if(result.code == 0){
                        $.router.load("{!! u('Seller/index',['id'=>$args['id'],'type'=>$args['type']])!!}&sort=" + sort, true);
                    } else {
                        $.showError(result.msg);
                    }
                },'json');
            });
			$(".ui-content").css("min-height",$(window).height()-210);

            // 商家分类下拉
            var n = 1;
            $(".x-sjsort li .x-sortl").touchend(function(){
                $(this).siblings(".x-sortmask").toggle();
                var fwtype = $(this).siblings(".x-sortmask").find(".x-sortlst .x-typelst").height();
                $(this).siblings(".x-sortmask").find(".x-sortlst .x-fwtypett").css("min-height",fwtype);
                $(this).parents(".x-sjsort li").siblings().find(".x-sort2").hide();
                n++;
            });
            // $(".x-sortlst .x-typelst li").touchend(function(){
            //     $(this).addClass("on").siblings().removeClass("on");
            // });
            $(".x-sortmask .mask1").touchend(function(){
                $(this).parents(".x-sortmask").hide();
            });
            $(".x-sjsort li .x-sortr").touchend(function(){
                n++;
                $(this).siblings(".x-sort2").toggle();
                $(this).parents(".x-sjsort li").siblings().find(".x-sortmask").hide();
            });
            $(".x-sort2 p").touchend(function(){
                var txt = $(this).parents(".x-sjsort div").siblings("p").find("span");
                txt.text($(this).text());
                $(this).parents(".x-sort2").hide();
                n++;
            });
            $(document).touchend(function(e){
                if(n%2==0){
                    if($(e.target).is(".x-sjsort li .x-sortr,.x-sjsort .x-sortl")){
                        return false;
                    }else{
                        $(".x-sort2").hide();
                        n++;
                    }
                }
            })

        });
    </script>
@stop 
