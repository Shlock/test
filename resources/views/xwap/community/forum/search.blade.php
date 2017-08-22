@extends('xwap.community._layouts.base')

@section('show_top')
    <header class="bar bar-nav">
        <a class="button button-link button-nav pull-left pageloading back" href="@if(!empty($nav_back_url)) {!! $nav_back_url !!} @else {{ u('Forum/index') }} @endif" data-transition='slide-out'>
            <span class="icon iconfont">&#xe600;</span>
        </a>
        <h1 class="title f16">搜索帖子</h1>
    </header>
@stop

@section('content')
    <div class="content" id=''>
        <!-- 搜索商家\商品 -->
        <form id="search_form" >
            <div class="search-input x-gsearch mb0">
                <input type="search" id="search" placeholder="请输入帖子关键词" name="keywords" value="{{$option['keywords']}}">
                <label class="icon iconfont icon-search c-gray search_submit">&#xe65e;</label>
            </div>
        </form >
        
        
        @if(!empty($option['keywords']))
            <!-- 搜索结果 -->
            <div class="content-block-title f12 c-gray">搜索结果</div>
            <div class="list-block x-splotlst nobor f14">
                <ul>
                    @if($data)
                        @foreach($data as $item)
                            <li class="item-content" onclick="$.href('{{ u('Forum/detail',['id'=>$item['id']]) }}')">
                                <div class="item-inner">
                                    <div class="item-title" data-keywords="{{$item}}">{{$item['title']}}</div>
                                    <div class="item-after"><i class="icon iconfont c-gray">&#xe602;</i></div>
                                </div>
                            </li>
                        @endforeach
                    @else
                    <li class="item-content">
                        <div class="item-inner">
                            <div class="item-title">暂无搜索记录</div>
                        </div>
                    </li>
                    @endif
                </ul>
            </div>
        @else
            <!-- 搜索历史 -->
            @if($history_search)
                <div class="list-block ml10 mr10 searchlst none">
                    <ul>
                        @foreach($history_search as $key => $item)
                            @if( !is_array($item) )
                                <li class="item-content pl0 x-tzsearch">
                                    <div class="item-inner pl10">
                                        <div class="item-title f14" style="width:100%" onclick="$.href('{{ u('Forum/search',['keywords'=>$item]) }}')" data-keywords="{{$item}}">
                                            {{$item}}
                                        </div>
                                        <!-- <div class="item-after"><i class="icon iconfont c-gray x-delico2">&#xe605;</i></div> -->
                                    </div>
                                </li>
                            @endif
                        @endforeach
                        <li class="item-content pl0 x-clearhis">
                            <div class="item-inner pl10">
                                <div class="item-title f14 tc c-gray">清除历史记录</div>
                            </div>
                        </li>
                    </ul>
                </div>
            @endif
        @endif

    </div>
@stop
@section($js)
<script type="text/javascript">
    $(function(){
        // 清除历史记录
        $(document).on("touchend",".x-clearhis",function(){
            $.post("{{u('Forum/clearsearch')}}", function(result){
                $.router.load("{!! u('Forum/search')!!}", true);
            });
        });
        // $(document).on("touchend",".x-tzsearch .x-delico2",function(){
        //     var clear = $(this);
        //     var keywords = clear.data('keywords');

        //     $.post("{{u('Forum/clearsearch')}}", {'keywords':keywords}, function(result){
        //         clear.parents("li").slideUp('fast', function() {
        //             clear.parents("li").remove();
        //         });
        //     });
            
        // });

        $(document).on("touchend",".search_submit",function(){
            var keywords = $.trim($("#search").val());
            if(keywords == ""){
                $.alert("请输入关键词");
                return false;
            }
            $.router.load("{!! u('Forum/search') !!}?keywords=" + keywords, true);
            //$("#search_form").submit();
        });

        $("#search_form").submit(function(){
            if($.trim($("#search").val()) == ""){
                $.alert("请输入关键词");
                return false;
            }
            $("#search").val($.trim($("#search").val()));
        });

        $("#search").focus(function(){
            $(".searchlst").removeClass("none");
        });
        $("#search").keyup(function(){
            if($(this).val()!=""){
                $(".searchlst").addClass("none");
            }else{
                $(".searchlst").removeClass("none");
            }
        });
        
        $(document).on("touchend",".searchlst .icon",function(){
            var clear = $(this);
            var keywords = clear.data('keywords');
            
            $.post("{{u('Forum/clearsearch')}}", {'keywords':keywords}, function(result){
                clear.parents("li").slideUp('fast', function() {
                    clear.parents("li").remove();
                });
            });

            $(this).parents("li").remove();
        });
    });
</script>
@stop
