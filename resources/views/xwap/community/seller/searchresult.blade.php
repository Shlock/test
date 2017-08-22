@extends('xwap.community._layouts.base')

@section('show_top')
	<header class="bar bar-nav">
		<a class="button button-link button-nav pull-left" href="javascript:$.back();" data-transition='slide-out' external>
			<span class="icon iconfont">&#xe600;</span>
		</a>
		<div class="searchbar x-tsearch">
		<!-- 搜索商家\商品 -->
		
			<div class="search-input pr dib">
			<form id="search_form" >
				<input type="search" id='search' placeholder='搜索附近商品或门店' name="keyword" value="{{$option['keyword']}}"/>
				<i class="icon iconfont f14 none">&#xe605;</i>
			</form>
			</div>
			<a class="button button-fill button-primary c-bg cq_search_btn" onclick="searchSub()" >搜索</a>
		</div>
	</header>
@stop

@section('content')
    <div class="content" id=''>
            <div class="content-block-title f12 c-gray">&nbsp;&nbsp;
            <a href="{{u('Seller/search',['search_type'=>'goods','keyword'=>$keyword])}}" class="fr c-red f12" external>按商品查看<i class="icon iconfont f12 c-gray">&#xe602;</i></a>
            </div>	
        @if($data)
            <div class="list-block media-list y-sylist">
                <ul>
                    @foreach($data as $key => $item)
                        <li @if($item['isDelivery'] == 0)style="background:#f3f3f3;"@endif>
                            <php>
                                if($item['countGoods'] >= 0 && $item['countService'] == 0){
                                $url = u('Goods/index',['id'=>$item['id'],'type'=>1,'urltype'=>1]);
                                }elseif($item['countGoods'] == 0 && $item['countService'] > 0){
                                $url = u('Goods/index',['id'=>$item['id'],'type'=>2,'urltype'=>1]);
                                }else{
                                $url = u('Seller/detail',['id'=>$item['id'],'urltype'=>1]);
                                }
                            </php>
                            <a href="{{$url}}" class="item-link item-content pageloading">
                                <div class="item-media">
                                    <img src="{{formatImage($item['logo'],100,100)}}" onerror='this.src="{{ asset("wap/community/newclient/images/no.jpg") }}"' width="73">
                                </div>
                                <div class="item-inner">
                                    <div class="item-subtitle f16">{{$item['name']}}</div>
                                    <div class="item-title-row f12 c-gray mt5 mb5">
                                        <div class="item-title">
                                            <div class="y-starcont">
                                                <div class="c-gray4 y-star">
                                                    <i class="icon iconfont vat mr2 f12">&#xe654;</i>
                                                    <i class="icon iconfont vat mr2 f12">&#xe654;</i>
                                                    <i class="icon iconfont vat mr2 f12">&#xe654;</i>
                                                    <i class="icon iconfont vat mr2 f12">&#xe654;</i>
                                                    <i class="icon iconfont vat mr2 f12">&#xe654;</i>
                                                </div>
                                                <div class="c-red f12 y-startwo" style="width:{{$item['score'] * 20}}%;">
                                                    <i class="icon iconfont vat mr2 f12">&#xe654;</i>
                                                    <i class="icon iconfont vat mr2 f12">&#xe654;</i>
                                                    <i class="icon iconfont vat mr2 f12">&#xe654;</i>
                                                    <i class="icon iconfont vat mr2 f12">&#xe654;</i>
                                                    <i class="icon iconfont vat mr2 f12">&#xe654;</i>
                                                </div>
                                            </div>
                                            @if($item['orderCount'] > 0)
                                                <span class="c-gray f12">已售{{$item['orderCount']}}</span>
                                            @else
                                                <span class="c-gray f12"></span>
                                            @endif
                                        </div>
                                    <span class="item-after">
                                        <i class="icon iconfont c-gray2 f18">&#xe60d;</i>
                                        <span class="compute-distance" data-map-point-x="{{ $item['mapPoint']['x'] }}" data-map-point-y="{{ $item['mapPoint']['y'] }}"></span>
                                    </span>
                                    </div>
                                    <div class="item-subtitle c-gray">
                                        <span class="mr10">{!! $item['freight'] !!}</span>
                                    </div>
                                </div>
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
            @else
            
		
            <!-- 没有搜索到信息 -->
            <div class="x-serno tc c-gray">
                <img src="{{ asset('wap/community/newclient/images/cry.png') }}" class="mr5">
                <span>很抱歉！没有搜索到您的信息！</span>
            </div>
        @endif
    </div>
    <!-- @include('wap.community._layouts.swiper')   -->
@stop
@section($js)
    <script charset="utf-8" src="http://map.qq.com/api/js?v=2.exp&key=2N2BZ-KKZA4-ZG4UB-XAOJU-HX2ZE-HYB4O&libraries=geometry,convertor"></script>
    <script type="text/javascript">
        var clientLatLng = null;

        $(function(){
            $.computeDistanceBegin = function() {
                if (clientLatLng == null) {
                    $.getClientLatLng();
                    return;
                }

                $(".compute-distance").each(function(){
                    var mapPoint = new qq.maps.LatLng($(this).attr('data-map-point-x'), $(this).attr('data-map-point-y'));
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

            $(document).on("touchend",".search_submit",function(){
                var keyword = $("#keyword").val();
                $.router.load("{!! u('Seller/search') !!}?keyword=" + keyword, true);
            });

        });

        $(document).on("touchend",".x-clearhis",function(){
            $(this).siblings("li").remove();
            $(this).find("span").text("暂无历史记录")
        });
		//caiq 
		function searchSub(){
			if($.trim($("#search").val())==''){
				$.toast('请输入关键字！');
				return false;
			}else{
				document.forms.search_form.submit();
			}
			
		};		
    </script>
@stop