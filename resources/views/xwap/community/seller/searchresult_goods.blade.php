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
<style type="text/css">.x-distancetab li.on {border-bottom: 3px solid #ff2d4b;}</style>
    <div class="content" id=''>
            <div class="content-block-title f12 c-gray">&nbsp;&nbsp;
            <a href="{{u('Seller/search',['search_type'=>'seller','keyword'=>$keyword])}}" class="fr c-red f12" external>按门店查看<i class="icon iconfont f12 c-gray">&#xe602;</i></a>
            </div>
        @if($data)
            
            <ul class="x-distancetab c-bgfff clearfix mb10">
                <li class="@if($option['sort']!=2) on @endif"><a href="{{u('Seller/search',['search_type'=>'goods','keyword'=>$keyword,'sort'=>1])}}" external>距离</a></li>
                <li class="@if($option['sort']==2) on @endif"><a href="{{u('Seller/search',['search_type'=>'goods','keyword'=>$keyword,'sort'=>2])}}" external>价格</a></li>
            </ul>
		
			<ul class="x-fwlst pl5 pr5 clearfix c-bgfff pt10 x-seagoodslst">
					@foreach($data as $key => $item)
						<li>
							<div class="goods">
								<a href="{{u('Goods/detail',['goodsId'=>$item['goods_id']])}}" class="" external>
									<div class="x-fwpic pr">
										<img src="{{ preg_replace('/,.*/','',$item['goods_image'])}}" />
									</div>
									<p class="f12 c-black goodsname">
										<span class="fl na">{{$item['goods_name']}}</span>
										<span class="time c-red">￥{{$item['price']}}</span>
									</p>
								</a>
								<a href="{{u('Seller/detail',['id'=>$item['id']])}}" class="c-gray f12 storename">
									<i class="icon iconfont c-gray f13 vat">&#xe632;</i>
									<span>{{$item['name']}}</span>
									<i class="icon iconfont c-gray fr f12">&#xe602;</i>
								</a>
							</div>
						</li>
					@endforeach
			</ul>
		
               
            
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