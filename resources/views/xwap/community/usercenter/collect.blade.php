@extends('xwap.community._layouts.base')

@section('show_top')
    <header class="bar bar-nav">
        <a class="button button-link button-nav pull-left" href="{{u('UserCenter/index')}}" data-transition='slide-out'>
            <span class="icon iconfont">&#xe600;</span>
        </a>
        <h1 class="title f16">我的收藏</h1>
    </header>
@stop

@section('content')
    <div class="content infinite-scroll infinite-scroll-bottom pull-to-refresh-content" data-ptr-distance="55" data-distance="50" id=''>
        <div class="pull-to-refresh-layer">
            <div class="preloader"></div>
            <div class="pull-to-refresh-arrow"></div>
        </div>

        <div class="y-scbtn">
            <div class="buttons-row">
                <a href="{{ u('UserCenter/collect',['type'=>1])}}" class="button y-sc1 @if($args['type'] == 1) on @endif">商品</a>
                <a href="{{ u('UserCenter/collect',['type'=>2])}}" class="button y-sc2 @if($args['type'] == 2) on @endif">店铺</a>
            </div>
        </div>

        @if(!empty($list))
            <div class="list-block media-list y-sylist">
                <ul id="list">
                    @include('xwap.community.usercenter.collect_item')
                </ul>
            </div>
        @else
            <div class="x-null pa w100 tc">
                <i class="icon iconfont">&#xe645;</i>
                <p class="f12 c-gray mt10">很抱歉，你还没有收藏！</p>
            </div>
        @endif

        <!-- 加载完毕提示 -->
        <div class="pa w100 tc allEnd none">
            <p class="f12 c-gray mt5 mb5">数据加载完毕</p>
        </div>
        <!-- 加载提示符 -->
        <div class="infinite-scroll-preloader none">
            <div class="preloader"></div>
        </div>
    </div>
@stop

@section($js)
<script charset="utf-8" src="http://map.qq.com/api/js?v=2.exp&key=2N2BZ-KKZA4-ZG4UB-XAOJU-HX2ZE-HYB4O&libraries=geometry"></script>
<script src="http://m.sui.taobao.org/assets/js/demos.js"></script>
<script type="text/javascript">
 var clientLatLng = null;

    $(function() {
        $(document).on("touchend",".todetail",function(){
            var id = $(this).data('id');
            var type = "{{$args['type']}}";
            var isurl = typeof($(this).parent().parent().data('isurl')) == 'undefined' ? $(this).parent().parent().parent().data('isurl') : $(this).parent().parent().data('isurl');
            if (type == 2) {
                if (isurl > 0) {
                   $.router.load("{!! u('Seller/detail')!!}?id=" + id, true);
                };
            } else {
                $.router.load("{!! u('Goods/detail')!!}?goodsId=" + id, true);
            }
        })

        $(".y-wdscr").on("touchend",function(){
            $(this).parent().parent().parent().unbind('click');
            var id = $(this).data("id");
            var obj = $(this).parents("li");
            var type = $(this).data('type');

            $.confirm('确认取消收藏？', '操作提示', function () {
                 $.post("{{u('UserCenter/delcollect')}}",{'id':id, 'type':type},function(res){
                     $(".x-sctk .tips").text(res.msg);
                     if (res.code == 0) {
                        obj.remove();
                     }
                     if(document.getElementById("list").getElementsByTagName("li").length == 0){
                        html = '<div class="x-null pa w100 tc"><i class="icon iconfont">&#xe645;</i><p class="f12 c-gray mt10">很抱歉，你还没有收藏！</p></div>';
                        $("div.y-sylist").after(html).remove();
                     }
                 },"json");
                 $(".x-sctk").fadeIn();
                 setTimeout(function(){
                     $(".x-sctk").fadeOut();
                 },1500);
            });
            return false;
        });

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


            // 加载开始
            // 上拉加载
            var groupLoading = false;
            var groupPageIndex = 2;
            $(document).off('infinite', '.infinite-scroll-bottom');
            $(document).on('infinite', '.infinite-scroll-bottom', function() {
                // 如果正在加载，则退出
                if (groupLoading) {
                    return false;
                }
                //隐藏加载完毕显示
                $(".allEnd").addClass('none');

                groupLoading = true;

                $('.infinite-scroll-preloader').removeClass('none');
                $.pullToRefreshDone('.pull-to-refresh-content');

                var data = new Object;
                data.page = groupPageIndex;
                data.type = "{{$args['type']}}";

                $.post("{{ u('UserCenter/collectList') }}", data, function(result){
                    groupLoading = false;
                    $('.infinite-scroll-preloader').addClass('none');
                    result  = $.trim(result);
                    if (result != '') {
                        groupPageIndex++;
                        $('#list').append(result);
                        $.refreshScroller();
                    }else{
                        $(".allEnd").removeClass('none');
                    }
                });
            });

            // 下拉刷新
            $(document).off('refresh', '.pull-to-refresh-content');
            $(document).on('refresh', '.pull-to-refresh-content',function(e) {
                // 如果正在加载，则退出
                if (groupLoading) {
                    return false;
                }
                groupLoading = true;
                var data = new Object;
                data.page = 1;
                data.type = "{{$args['type']}}";

                $.post("{{ u('UserCenter/collectList') }}", data, function(result){
                    groupLoading = false;
                    result  = $.trim(result);
                    if (result != "") {
                        groupPageIndex = 2;
                    }
                    $('#list').html(result);
                    $.pullToRefreshDone('.pull-to-refresh-content');
                });
            });
            // 加载结束
     })

</script>
@stop
