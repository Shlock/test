@extends('xwap.community._layouts.base')

@section('show_top')
    <header class="bar bar-nav">
        <a class="button button-link button-nav pull-left" href="{{u('UserCenter/index')}}" data-transition='slide-out'>
            <span class="icon iconfont">&#xe600;</span>
        </a>
        <h1 class="title f16">全部订单</h1>
    </header>
@stop

@section('content')
    @include('xwap.community._layouts.bottom')

    <div class="content infinite-scroll infinite-scroll-bottom pull-to-refresh-content" data-ptr-distance="55" data-distance="50" id="">
        <div class="pull-to-refresh-layer">
            <div class="preloader"></div>
            <div class="pull-to-refresh-arrow"></div>
        </div>
        <div class="buttons-tab y-ddnav">
            <a href="{{ u('Order/index',['status'=>0]) }}" class="button @if($args['status'] == 0 ) active @else pageloading @endif" data-no-cache="true">全部订单</a>
            <a href="{{ u('Order/index',['status'=>1]) }}" class="button @if($args['status'] == 1 ) active @else pageloading @endif" data-no-cache="true">待评价({{ $list['commentNum'] }})</a>
        </div>
        @if(!empty($list['orderList']))
            <div class="card-container" id="wdddmain">
                @include('xwap.community.order.item')
            </div>
            <div class="pa w100 tc allEnd none">
                <p class="f12 c-gray mt5 mb5">数据加载完毕</p>
            </div>
        @else
            <div class="x-null pa w100 tc">
                <i class="icon iconfont">&#xe645;</i>
                <p class="f12 c-gray mt10">很抱歉！你还没有@if($args['status'] == 1 ) 待评价 @endif订单！</p>
            </div>
        @endif
        <!-- 加载提示符 -->
        <div class="infinite-scroll-preloader none">
            <div class="preloader"></div>
        </div>
    </div>
@stop

@section($js)
@include('xwap.community.order.orderjs')
<script src="http://m.sui.taobao.org/assets/js/demos.js"></script>
<script type="text/javascript">
$(function(){
        //按钮事件
        $(document).on('click','.url', function () {
            $.router.load($(this).data('url'), true);
        }).on('click','.okorder', function () {
            var oid = $(this).data('id');
            $.confirm('确认删除订单吗？', '操作提示', function () {
                $.delOrders(oid);
            });
        }).on('click','.confirmorder', function () {
            var oid = $(this).data('id');
            $.confirm('确认完成订单吗？', '操作提示', function () {
                $.confirmOrder(oid);
            });
        }).on('click','.cancelorder', function () {
            var oid = $(this).data('id');
            var status = $(this).data('status');
            if(status == "{{ ORDER_STATUS_AFFIRM_SELLER }}"){
                $.alert('请联系商家取消订单', '取消提示');
            }else{
                $.confirm('确认取消订单吗？', '确认取消', function () {
                    $.cancelOrder(oid);
                });
            }
        });

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
            data.status = "{{ $args['status'] }}";

            $.post("{{ u('Order/indexList') }}", data, function(result){
                groupLoading = false;
                $('.infinite-scroll-preloader').addClass('none');
                result  = $.trim(result);
                if (result != '') {
                    groupPageIndex++;
                    $('#wdddmain').append(result);
                    $.refreshScroller();
                }else{
                    $(".allEnd").removeClass('none');
                }
            });
        });
        //下拉刷新
        $(document).off('refresh', '.pull-to-refresh-content');
        $(document).on('refresh', '.pull-to-refresh-content',function(e) {
            // 如果正在加载，则退出
            if (groupLoading) {
                return false;
            }
            groupLoading = true;
            var data = new Object;
            data.page = 1;
            data.status = "{{ $args['status'] }}";

            $.post("{{ u('Order/indexList') }}", data, function(result){
                groupLoading = false;
                result  = $.trim(result);
                if (result != "") {
                    groupPageIndex = 2;
                }
                $('#wdddmain').html(result);
                $.pullToRefreshDone('.pull-to-refresh-content');
            });
        });
        //js刷新
        $.pullToRefreshTrigger('.pull-to-refresh-content');
        //加载结束
    });
</script>
@stop
