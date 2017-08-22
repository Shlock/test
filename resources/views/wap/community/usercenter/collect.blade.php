@extends('wap.community._layouts.base')
@section('js')
    <script src="{{ asset('static/infinite-scroll/jquery.infinitescroll.js') }}"></script>
@stop
@section('show_top')
    <div data-role="header" data-position="fixed" class="x-header">
        <h1>我的收藏</h1>
        <a href="{{u('UserCenter/index')}}" data-iconpos="notext" class="x-back ui-nodisc-icon" data-shadow="false"></a>
    </div>
@stop

@section('content')
    <div role="main" class="">
        <div class="x-wdscmain">
            <div class="y-wdsc">
                <a href="{{ u('UserCenter/collect',['type'=>1])}}" class="y-sc1 @if($args['type'] == 1) on @endif">商品</a><a href="{{ u('UserCenter/collect',['type'=>2])}}" class="y-sc2 @if($args['type'] == 2) on @endif">店铺</a></div>
        </div>
        <div class="y-con">
            <ul>
                @include('wap.community.usercenter.collect_item')
                
            </ul>
        </div>
    </div>
     <div class="x-bgtk x-sctk none">
         <div class="x-bgtk1">
             <div class="x-tkbgi">
                 <div class="tips"></div>
             </div>
         </div>
     </div>
     @include('wap.community._layouts.swiper')
<script charset="utf-8" src="http://map.qq.com/api/js?v=2.exp&key=2N2BZ-KKZA4-ZG4UB-XAOJU-HX2ZE-HYB4O&libraries=geometry"></script>
 <script type="text/javascript">
 var clientLatLng = null;
     jQuery(function() {
        $.SwiperInit('.x-dinlst','li',"{{ u('UserCenter/collect',$args) }}");
         $(".y-wdscr").on("touchend",function(){
            $(this).parent().parent().parent().unbind('click');
             var id = $(this).data("id");
             var obj = $(this).parents("li");
             var type = $(this).data('type');
             $.post("{{u('UserCenter/delcollect')}}",{'id':id, 'type':type},function(res){
                 $(".x-sctk .tips").text(res.msg);
                 if (res.code == 0) {
                     obj.remove();
                 }
             },"json");
             $(".x-sctk").fadeIn();
             setTimeout(function(){
                 $(".x-sctk").fadeOut();
             },1500);
         })

         $.computeDistanceBegin = function() {
                if (clientLatLng == null) {
                    $.getClientLatLng();
                    return;
                }

                $(".compute-distance").each(function(){
                    var mapPoint = new qq.maps.LatLng($(this).data('map-point-x'), $(this).data('map-point-y')); 
                    $.computeDistanceBetween(this, mapPoint);
                    $(this).removeClass('compute-distance');
                })
            }

            $.getClientLatLng = function() {
                citylocation = new qq.maps.CityService({
                    complete : function(result){
                        clientLatLng = result.detail.latLng;
                        $.computeDistanceBegin();
                    }
                });
                citylocation.searchLocalCity();
            }

            $.computeDistanceBetween = function(obj, mapPoint) {
                var distance = qq.maps.geometry.spherical.computeDistanceBetween(clientLatLng, mapPoint);
                if (distance < 1000) {
                    $(obj).html(Math.round(distance) + 'M');
                } else {
                    $(obj).html(Math.round(distance / 1000) + 'Km');
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
     })

 </script>
@stop

