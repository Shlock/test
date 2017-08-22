@extends('xwap.community._layouts.base')

@section('show_top')
    <header class="bar bar-nav">
        <h1 class="title">生活圈</h1>
    </header>
@stop

@section('content')
    @include('xwap.community._layouts.bottom')

    <div class="content pull-to-refresh-content infinite-scroll infinite-scroll-bottom" data-ptr-distance="55" id=''>

        <div class="pull-to-refresh-layer">
            <div class="preloader"></div>
            <div class="pull-to-refresh-arrow"></div>
        </div>

        <ul class="x-lifeindex c-bgfff clearfix tc">
            <li>
                <a href="{{ u('Forum/mylists') }}">
                    <img src="{{  asset('wap/community/client/images/tz.png') }}">
                    <p class="f12">我的帖子<span class="c-gray">({{$postsnum}})</span></p>
                </a>
            </li>
            <li>
                <a href="{{ u('Forummsg/index') }}">
                    <img src="{{  asset('wap/community/client/images/lt.png') }}">
                    <p class="f12">论坛消息@if($messagenum > 0)<span class="c-gray">({{$messagenum}})</span>@endif</p>
                </a>
            </li>
        </ul>
        
        @if($plates)
        <ul class="y-nav clearfix mt10">
            @foreach($plates as $v)
                @if($v['id'] != 0)
                    <li>
                        <a href="@if($v['id'] == 1){{u('Property/index',['id'=>$v['id']])}} @else{{u('Forum/lists',['plateId'=>$v['id']])}}@endif" class="db pageloading">
                            <img src="@if(!empty($v['icon'])) {{formatImage($v['icon'],1000,1000)}} @else {{ asset('wap/community/client/images/b12.png')}} @endif">
                            <p class="f13">{{$v['name']}}</p>
                        </a>
                    </li>
                @else
                    <li>
                        <a href="{{ u('Forum/plates') }}" class="db pageloading">
                            <img src="{{ asset('wap/community/client/images/b11.png')}}">
                            <p class="f13">更多</p>
                        </a>
                    </li>
                @endif
            @endforeach
        </ul>
        @endif

        <div class="list-block media-list x-comment nobor mt10">
            <ul class="list-container" id="list">
                @include('xwap.community.forum.index_item')
            </ul>
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
    $(function() {
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

            $.post("{{ u('Forum/indexList') }}", data, function(result){
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

            $.post("{{ u('Forum/indexList') }}", data, function(result){
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