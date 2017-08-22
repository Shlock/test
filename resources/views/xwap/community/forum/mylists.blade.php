@extends('xwap.community._layouts.base')

@section('show_top')
    <header class="bar bar-nav">
        <a class="button button-link button-nav pull-left pageloading" href="@if(!empty($nav_back_url)) {!! $nav_back_url !!} @else {{u('Forum/index')}} @endif" data-transition='slide-out'>
            <span class="icon iconfont">&#xe600;</span>
        </a>
        <h1 class="title f16">我的帖子</h1>
    </header>
@stop

@section('content')
    <div class="content c-bgfff infinite-scroll infinite-scroll-bottom pull-to-refresh-content" data-ptr-distance="55" data-distance="50" id=''>
        <!-- 加载提示符 -->
        <div class="pull-to-refresh-layer">
            <div class="preloader"></div>
            <div class="pull-to-refresh-arrow"></div>
        </div>
        <div class="x-goodstop x-posttabs buttons-tab">
            <a href="{{ u('Forum/mylists',['type'=>0]) }}" class="@if($args['type'] == 0) active @endif button f15 tab-item">发表的帖子</a>
            <a href="{{ u('Forum/mylists',['type'=>1]) }}" class="@if($args['type'] == 1) active @endif button f15 tab-item">回复的帖子</a>
            <a href="{{ u('Forum/mylists',['type'=>2]) }}" class="@if($args['type'] == 2) active @endif button f15 tab-item">点赞的帖子</a>
        </div>
        <div class="tabs">
            <div id="tab1" class="tab active">
                @if($list)
                    <ul class="x-post oh c-bgfff" id="list">
                        @include('xwap.community.forum.mylists_item')
                    </ul>
                @else
                    <div class="x-null pa w100 tc">
                        <i class="icon iconfont">&#xe645;</i>
                        <p class="f12 c-gray mt10">你还没有帖子哦！</p>
                    </div>
                @endif
            </div>
        </div>
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
    var type = "{{$args['type']}}";
    if (type == 0) {
        //$.SwiperInit('.y-lifelst', '.x-postlist',"{!! u('Forum/mylists',$args) !!}");
    };
    
    $(".x-lifelst").css("min-height",$(window).height()-102);

    function prevent_default(e) {
        e.preventDefault();
    }

    function disable_scroll() {
        $(document).on('touchmove', prevent_default);
    }

    function enable_scroll() {
        $(document).unbind('touchmove', prevent_default);
    }

    var x;

    $(document).on('touchstart', '.x-post li.li-left .top-con', function (e) {
        $('.x-post li .top-con').css('left', '0');
        $(e.currentTarget).addClass('open');
        x = e.targetTouches[0].pageX;

    })

    $(document).on('touchmove', '.x-post li.li-left .top-con', function (e) {
        var change = e.targetTouches[0].pageX - x;
        change = Math.min(Math.max(-8, change), 0);
        e.currentTarget.style.left = change + 'rem';
        if (change < -1) disable_scroll();
    })
    $(document).on('touchend', '.x-post li.li-left .top-con', function (e) {
        var left = parseInt(e.currentTarget.style.left)
        var new_left;
        if (left < -4) {
            new_left = '-4rem';
        } else if (left > 4) {
            new_left = '4rem';
        } else {
            new_left = '0rem';
        }
        $(e.currentTarget).on('animate', function () {
            left: new_left
        }, 200);
        enable_scroll();
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
        data.type = "{{$_GET['type']}}";

        $.post("{{ u('Forum/mylistsList') }}", data, function(result){
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
        data.type = "{{$_GET['type']}}";

        $.post("{{ u('Forum/mylistsList') }}", data, function(result){
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

    function listdelete(id) {
        $.confirm("确认删除？", "操作提示", function(){
            $.post("{{ u('Forum/delete') }}",{'id':id},function(res){
                $.alert(res.msg);
                if (res.code == 0) {
                    $("#li_"+id).remove();
                }
            },"json");
        });
    }

</script>
@stop