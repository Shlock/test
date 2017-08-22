@extends('wap.community._layouts.base')

@section('show_top')
    <div data-role="header" data-position="fixed" class="x-header">
        <h1>搜索帖子</h1>
        <a href="@if(!empty($nav_back_url)) {!! $nav_back_url !!} @else {{ u('Forum/index') }} @endif" data-iconpos="notext" class="x-back ui-nodisc-icon" data-shadow="false"></a>
    </div>
@stop

@section('content')
    <div role="main" class="ui-content searchs">
        <div class="x-search clearfix">
            <form id="search_form" >
            <input type="text" placeholder="请输入帖子关键词" name="keywords" value="{{$option['keywords']}}" id="keywords"/>
            <div class="x-serbtn">
                <i class="x-serico search_submit"></i>
            </div>
            </form >
        </div>
        @if(!empty($option['keywords']))
            <div class="x-lh45 c-green">搜索结果</div>
            <ul class="x-serhis x-tzsearch posts">
                @if($data)
                @foreach($data as $item)
                <li><a href="{{ u('Forum/detail',['id'=>$item['id']]) }}"><span>{{$item['title']}}</span><i class="x-rightico fr"></i></a></li>
                @endforeach
                @else
                <li class="x-clearhis">暂无搜索结果</li>
                @endif
            </ul>
        @else
        <!-- 有搜索记录的时候 -->
            @if($history_search)
            <div class="x-lh45 c-green">历史搜索</div>
            <ul class="x-serhis x-tzsearch histories">
                @foreach($history_search as $key => $item)
                <li><a href="javascript:;"><span onclick="window.location.href='{{ u('Forum/search',['keywords'=>$item]) }}'">{{$item}}</span><i class="x-delico2 fr" data-keywords="{{$item}}"></i></a></li>
                @endforeach
                <li class="x-clearhis"><i class="x-delico"></i>清除历史记录</li>
            </ul>
            @else
            <div class="x-lh45 c-green x-clearhis">暂无搜索记录</div>
            @endif
        @endif
    </div>

@stop
@section('js')
<script type="text/javascript">
    $(function(){
        // 清除历史记录
        $(".x-clearhis").touchend(function(){
            $.post("{{u('Forum/clearsearch')}}", function(result){
                window.location.href = "{!! u('Forum/search')!!}";
            });
        });
        $(".x-tzsearch .x-delico2").touchend(function(){
            var clear = $(this);
            var keywords = clear.data('keywords');
            $.post("{{u('Forum/clearsearch')}}", {'keywords':keywords}, function(result){
                clear.parents("li").slideUp('fast', function() {
                    clear.parents("li").remove();
                });
            });
            
        });

        $(".search_submit").click(function(){
            var keywords = $("#keywords").val();
            window.location.href="{!! u('Forum/search') !!}?keywords=" + keywords;
            //$("#search_form").submit();
        });


    });
</script>
@stop