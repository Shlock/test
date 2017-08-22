@extends('xwap.community._layouts.base')
@section('show_top')
    <header class="bar bar-nav">
        <a class="button button-link button-nav pull-left" href="@if(!empty($nav_back_url)) {{ $nav_back_url }} @else javascript:$.back(); @endif" data-transition='slide-out'>
            <span class="icon iconfont">&#xe600;</span>
        </a>
        <h1 class="title f16">优惠券</h1>
    </header>
@stop


@section('content')
    <nav class="bar bar-tab y-cdkey">
        <div class="searchbar row">
            <div class="search-input col-80">
                <input type="search" placeholder="我有兑换码..."  id="sn">
            </div>
            <a class="button button-fill button-primary col-20"  id="exchange">立即兑换</a>
        </div>
    </nav>
    
    <div class="content infinite-scroll infinite-scroll-bottom pull-to-refresh-content" data-ptr-distance="55" data-distance="50" id=''>

        <!-- 加载提示符 -->
        <div class="pull-to-refresh-layer">
            <div class="preloader"></div>
            <div class="pull-to-refresh-arrow"></div>
        </div>
        
        <div class="buttons-tab y-couponsnav">
            <a href="{{ u('Coupon/index',['status' => 0]) }}" class="button @if($args['status'] != 1) active @endif pageloading">未使用</a>
            <a href="{{ u('Coupon/index',['status' => 1]) }}" class="button @if($args['status'] == 1) active @endif pageloading">已失效</a>
        </div>
        
        <div class="content-block-title f12">
            <span>有<span class="c-red">{{ $list['count'] }}</span>张优惠券</span>
            <span class="fr c-red" onclick="$.href('{{ u('More/detail',['code' => 5]) }}')"><i class="icon iconfont mr5">&#xe64c;</i>优惠券说明</span>
        </div>

        @if(!empty($list['list']))
            <div id="list">
                @include('xwap.community.coupon.item')
            </div>
        @else
            <div class="x-null pa w100 tc">
                <i class="icon iconfont">&#xe645;</i>
                <p class="f12 c-gray mt10">亲，这里什么都没有！</p>
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
<script src="http://m.sui.taobao.org/assets/js/demos.js"></script>
<script type="text/javascript">
$(function(){
    $(document).on("touchend", "#exchange", function(){
        var sn = $("#sn").val();
        $.post("{{ u('Coupon/excoupon') }}",{sn:sn},function(res){
            if(res.code == 0){
                $.alert(res.msg, function(){
                    $.router.load("{{ u('Coupon/index') }}", true);
                });
            }else{
                $.alert(res.msg);
            }
        },"json");
    });

    // $(document).on("touchend", ".buttons-tab a.button", function(){
    //     var qhid;
    //     if(!$(this).hasClass("active")){
    //         $(this).addClass("active").siblings().removeClass("active");
    //         qhid = $(this).attr("href");
    //         $("#"+qhid).addClass("active").siblings().removeClass("active");
    //     }
    // });

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
        data.status = "{{$args['status']}}";

        $.post("{{ u('Coupon/indexList') }}", data, function(result){
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
        data.status = "{{$args['status']}}";

        $.post("{{ u('Coupon/indexList') }}", data, function(result){
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

});
</script>
@stop
