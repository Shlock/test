@extends('wap.community._layouts.base')
@section('show_top')
	<div data-role="header" data-position="fixed" class="x-header">
		<h1>搜索结果</h1>
        <a href="@if(!empty($nav_back_url)) {{ $nav_back_url }} @else javascript:history.back(-1); @endif" data-iconpos="notext" class="x-back ui-nodisc-icon" data-shadow="false"></a>
	</div>
@stop

@section('content') 
	<div role="main" class="ui-content" style="padding-top:5px;">
		@if($data)
		<ul class="x-index4">
			<!-- 有搜索内容的时候 -->
	            @foreach($data as $key => $item)
	            <li class="clearfix" @if($item['isDelivery'] == 0)style="background:#f3f3f3;"@endif>
                    <php>
                        if($item['countGoods'] >= 0 && $item['countService'] == 0){
                            $url = u('Goods/index',['id'=>$item['id'],'type'=>1,'urltype'=>2]);
                        }elseif($item['countGoods'] == 0 && $item['countService'] > 0){
                            $url = u('Goods/index',['id'=>$item['id'],'type'=>2,'urltype'=>2]);
                        }else{
                            $url = u('Seller/detail',['id'=>$item['id'],'urltype'=>2]);
                        }
                    </php>
	                <a href="{{$url}}">
	                    <div class="x-naimg">
	                        <img src="@if(!empty($item['logo'])) {{formatImage($item['logo'],73,73)}} @else {{ asset('wap/community/client/images/sj.jpg')}} @endif" width="73" height="73"/>
	                    </div>
	                    <div class="x-index4r">
	                        <p class="c-black">{{$item['name']}}</p>
	                        <p class="c-green f12 mt5">
	                            <span>营业时间：{{$item['businessHours']}}</span>
                            </p>
                            <p class="c-green f12 mt5">
                                @if($item['isDelivery'] == 1)
                                <span>配送时间：{{$item['deliveryTime']}}</span>
                                @else
                                <span>商家休息</span>
                                @endif
	                            <span class="fr"><i class="x-addico"></i><span class="compute-distance" data-map-point-x="{{ $item['mapPoint']['x'] }}" data-map-point-y="{{ $item['mapPoint']['y'] }}"></span></span>
	                        </p>
	                        <p class="c-green f12 mt5">
	                            {!!$item['freight']!!}
	                        </p>
	                    </div>
	                </a>
	            </li>
	            @endforeach
        </ul>
     	@else
		<div class="x-serno c-green">
            <img src="{{ asset('wap/community/client/images/ico/cry.png') }}"  />
            <span>很抱歉！没有搜索到相关商家！</span>
        </div>
        @endif
	@include('wap.community._layouts.swiper')  
@stop 
@section('js')
<script charset="utf-8" src="http://map.qq.com/api/js?v=2.exp&key=2N2BZ-KKZA4-ZG4UB-XAOJU-HX2ZE-HYB4O&libraries=geometry,convertor"></script>
<script type="text/javascript">
    var clientLatLng = null;

        jQuery(function($){
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
                    $(obj).html(Math.round(distance) + 'm');
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

        });
</script>
@stop