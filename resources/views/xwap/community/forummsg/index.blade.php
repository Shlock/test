@extends('xwap.community._layouts.base')

@section('show_top')
    <header class="bar bar-nav">
        <a class="button button-link button-nav pull-left pageloading back" href="@if(!empty($nav_back_url)) {{ $nav_back_url }} @else javascript:$.back(); @endif" data-transition='slide-out'>
            <span class="icon iconfont">&#xe600;</span>
        </a>
        <h1 class="title f16">论坛消息</h1>
    </header>
@stop

@section('content')
    <div class="content c-bgfff infinite-scroll infinite-scroll-bottom pull-to-refresh-content" data-ptr-distance="55" data-distance="50" id=''>
        <!-- 加载提示符 -->
        <div class="pull-to-refresh-layer">
            <div class="preloader"></div>
            <div class="pull-to-refresh-arrow"></div>
        </div>
        @if(!empty($list))
            <div class="list-block media-list x-comment x-bbsmsg bfh0">
                <ul id="list">
                    @include('xwap.community.forummsg.index_item')
                </ul>
            </div>
        @else
            <div class="x-null pa w100 tc">
                <i class="icon iconfont">&#xe645;</i>
                <p class="f12 c-gray mt10">暂时没有消息</p>
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
    // 列表删除效果
    $(function() {
        $(".y-xtxx").css("min-height",$(window).height()-45);

        function prevent_default(e) {
            e.preventDefault();
        }

        function disable_scroll() {
            $(document).on('touchmove', prevent_default);
        }

        function enable_scroll() {
            $(document).unbind('touchmove', prevent_default)
        }
        // 点击全选的时候
        var x;
        $('.f-chkall').on('touchend', function(e) {
            $('.m-timelst li > .m-ctsist').css('left', '0px')
            var $this=$(".m-timelst.m-hb li");
            if($this.hasClass("on")){
                $this.removeClass("on");
                $(".m-xxdeletenav").slideUp();
            }else{
                $this.addClass("on");
                $(".m-xxdeletenav").slideDown();
            }
        });
        // $(document).on("touchend",".y-xtxx li",function(){
        //     var id = $(this).data("id");
        //     $.post("{{u('Forummsg/readmsg')}}",{'id':id},function(res){
        //         if(res.code == 0){
        //             $.router.load("{{ u('Forummsg/index')}}", true);
        //         }
        //     },"json");
        // });
        $('.y-xtxx li > .y-ctsist')
                .on('touchstart', function(e) {
                    if ($(e.currentTarget).parent().hasClass("on")) {
                        return;
                    }
                    console.log(e.originalEvent.pageX)
                    $('.y-xtxx li > .y-ctsist').css('left', '0px') // 关闭所有
                    $(e.currentTarget).addClass('open')
                    x = e.originalEvent.targetTouches[0].pageX // 锚点
                })
                .on('touchmove', function(e) {
                    if ($(e.currentTarget).parent().hasClass("on")) {
                        return;
                    }
                    var change = e.originalEvent.targetTouches[0].pageX - x
                    change = Math.min(Math.max(-100, change), 0) //左边-100px,右边0px
                    e.currentTarget.style.left = change + 'px'
                    if (change < -10) disable_scroll() // 当大于10px的滑动时，禁止滚动
                })
                .on('touchend', function(e) {
                    if ($(e.currentTarget).parent().hasClass("on")) {
                        return;
                    }
                    var left = parseInt(e.currentTarget.style.left)
                    var new_left;
                    if (left < -35) {
                        new_left = '-100px'
                    } else if (left > 35) {
                        new_left = '100px'
                    } else {
                        new_left = '0px'
                    }
                    // e.currentTarget.style.left = new_left
                    $(e.currentTarget).animate({left: new_left}, 200)
                    enable_scroll()
                });

        $('li .delete-btn').on('touchend', function(e) {
            e.preventDefault();
            var id = $(this).data("id");
            $.post("{{ u('Forummsg/delete')}}",{id:id},function(res){},'json');
            $(this).parents('li').slideUp('fast', function() {
                $(this).remove()
            })
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

            $.post("{{ u('Forummsg/indexList') }}", data, function(result){
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

            $.post("{{ u('Forummsg/indexList') }}", data, function(result){
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
