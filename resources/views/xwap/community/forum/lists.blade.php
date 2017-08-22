@extends('xwap.community._layouts.base')

@section('show_top')
    <header class="bar bar-nav">
      <a class="button button-link button-nav pull-left pageloading" href="{{ u('Forum/index') }}" data-transition='slide-out'>
        <span class="icon iconfont">&#xe600;</span>
      </a>
      <h1 class="title f16">{{$plate['name']}}</h1>
      <a class="button button-link button-nav pull-right open-popup pageloading" href="{{ u('Forum/search') }}" data-popup=".popup-about">
        <i class="icon iconfont c-gray x-searchico">&#xe65e;</i>
    </a>
    </header>
@stop

@section('content')
    <div class="content" id=''>
        <!-- 顶过的帖子列表 -->
        @if(count($list['top']) > 0)
            <div class="list-block bfh0">
                <ul class="x-postlst f12">
                    @foreach($list['top'] as $item)
                        <li class="item-content" onclick="$.href('{{ u('Forum/detail',['id'=>$item['id']]) }}')">
                            <div class="item-inner">
                                <div class="item-title"><i class="icon iconfont">&#xe60c;</i>{{$item['title']}}</div>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif
        <!-- 帖子列表加赞和评论数 -->
        @if(count($list['nottop']) > 0)
            @foreach($list['nottop'] as $item)
                <div class="card x-postdelst x-post">
                    <div class="card-content">
                      <div class="card-content-inner">

                        <div class="post clearfix" onclick="$.href('{{ u('Forum/detail',['id'=>$item['id']]) }}')">
                            @if($item['images'][0])
                                <a href="" class="ui-link pageloading"><img src="{{ formatImage($item['images'][0], 100, 100) }}"></a>
                            @endif
                            <p class="f16 pt5">
                                <a href="{{ u('Forum/detail',['id'=>$item['id']]) }}" class="pageloading">
                                    {{$item['title']}}
                                </a>
                            </p>
                        </div>

                        <p class="f12 c-green">
                            <img src="@if(!empty($item['user']['avatar'])) {{formatImage($item['user']['avatar'],46,46)}} @else {{ asset('wap/community/client/images/shqimg1.png')}} @endif" class="post-pic">
                            <span>{{$item['user']['name']}}</span>
                            <span class="c-gray ml20">
                              
                                <span class="zan @if($item['praise']) on @endif" data-id="{{$item['id']}}" data-num="{{$item['goodNum']}}">
                                    <i class="icon iconfont mr5 f14 vam">&#xe651;</i>{{$item['goodNum']}}
                                </span>
                              
                                <span class="cmd">
                                    <i class="icon iconfont mr5 ml10">&#xe64f;</i>{{$item[rateNum]}}
                                </span>
                              <span class="fr">{{ formatTime($item['createTime']) }}前</span>
                            </span>
                        </p>
                      </div>
                    </div>
                </div>
            @endforeach
        @endif
        <!-- 发帖 -->
        <div class="x-posted tc" onclick="$.href('{{ u('Forum/addbbs',['plateId'=>$plate['id']]) }}')">
            <i class="icon iconfont mb5">&#xe63f;</i>
            <p class="f15">发帖</p>
        </div>
    </div>

    <!-- @include('wap.community._layouts.swiper') -->
@stop
@section($js)
    <!-- <script src="{{ asset('static/infinite-scroll/jquery.infinitescroll.js') }}"></script> -->
    <script type="text/javascript">
        $(function() {
            // $.SwiperInit('.lists','.x-post',"{{ u('Forum/lists',$args) }}");

            $(document).on("touchend",".x-post .zan",function(){
                var num = parseInt($(this).data('num'));
                var id = $(this).data('id');
                var zan = $(this);

                $.post("{{ u('Forum/updateLike') }}",{'id':id},function(res){
                    if (res.code == 0) {
                        //alert(zan.hasClass("on"))
                        if(zan.hasClass("on")){//取消点赞
                            zan.removeClass("on");
                            zan.html('<i class="icon iconfont mr5 f14 vam">&#xe651;</i>'+num);
                            zan.attr('data-num', num);
                        }else{//点赞
                            var _num = num+1;
                            zan.addClass("on");
                            zan.html('<i class="icon iconfont mr5 f14 vam">&#xe651;</i>'+_num);
                            zan.attr('data-num', num+1);
                        }
                    } else {
                        $.alert(res.msg);
                    }
                },"json");
            });

        });
    </script>
@stop