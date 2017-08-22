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
<?php 
function loopGoods($data){
	$str = "";
	foreach($data as $key=>$d){
		$class = $key>0?' none':'';
		$str .= " <div class='item-subtitle pr goodslst c-black f14 pr10 ".$class."'>
                                    <span class='mr10'>".$d['name']."</span>
                                    <span class='fr c-red f16'>￥".$d['price']."</span>
                                </div>";
	}
	echo $str;
}

?>
    <div class="content" id=''>
            <div class="content-block-title f12 c-gray">&nbsp;&nbsp;
            <a href="{{u('Seller/search',['search_type'=>'goods','keyword'=>$keyword])}}" class="fr c-red f12" external>按商品查看<i class="icon iconfont f12 c-gray">&#xe602;</i></a>
            </div>	
        @if($data)
            <div class="list-block media-list y-sylist x-serstore">
                <ul>
                    @foreach($data as $key => $item)
					
                    <li>
                        <a href="{{u('Seller/detail',['id'=>$item['id'],'urltype'=>1])}}" class="item-link item-content">
                            <div class="item-media"><img src="{{formatImage($item['logo'],100,100)}}" onerror='this.src="{{ asset("wap/community/newclient/images/no.jpg") }}"' width="73"></div>
                            <div class="item-inner">
                                <div class="item-subtitle f15 pr10">{{$item['name']}}</div>
                                <div class="item-title-row f12 c-gray mt5 mb5 pr10">
                                    <div class="item-title">
                                        <div class="y-starcont">
                                            <div class="c-gray4 y-star">
                                                <i class="icon iconfont vat mr2 f12">&#xe654;</i>
                                                <i class="icon iconfont vat mr2 f12">&#xe654;</i>
                                                <i class="icon iconfont vat mr2 f12">&#xe654;</i>
                                                <i class="icon iconfont vat mr2 f12">&#xe654;</i>
                                                <i class="icon iconfont vat mr2 f12">&#xe654;</i>
                                            </div>
                                            <div class="c-yellow f12 y-startwo" style="width:{{$item['score'] * 20}}%;">
                                                <i class="icon iconfont vat mr2 f12">&#xe654;</i>
                                                <i class="icon iconfont vat mr2 f12">&#xe654;</i>
                                                <i class="icon iconfont vat mr2 f12">&#xe654;</i>
                                                <i class="icon iconfont vat mr2 f12">&#xe654;</i>
                                                <i class="icon iconfont vat mr2 f12">&#xe654;</i>
                                            </div>
                                        </div>
                                        <span class="c-yellow f12 ml5">{{$item['score']}}分</span>
                                    </div>
                                </div>
                                <div class="item-subtitle f12 c-gray pr10 mb5">
                                    <span class="mr10">{{$item['serviceFee']}}元起送</span>@if($item['deliveryFee']==0) 免配送费 @else 配送费：{{$item['deliveryFee']}} @endif
									<span class="fr compute-distance" data-map-point-x="{{ $item['mapPoint']['x'] }}" data-map-point-y="{{ $item['mapPoint']['y'] }}"></span>
                                   
                                </div>
								{{loopGoods($item['goods'])}}
                            </div>
                        </a>
						@if(count($item['goods'])>1)
                        <div class="x-seegoods tc c-bgfff c-gray f12">查看其它{{count($item['goods'])-1}}个相关商品<i class="icon iconfont f12 c-gray ml5">&#xe601;</i></div>
						@endif
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
    $(".x-seegoods").click(function(){
        $(this).parents("li").find(".goodslst").removeClass("none");
        $(this).addClass("none");
    });		
    </script>
@stop