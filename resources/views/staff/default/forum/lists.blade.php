@extends('wap.community._layouts.base')

@section('show_top')
    <div data-role="header" data-position="fixed" class="x-header">
        <h1>{{$plate['name']}}</h1>
        <a href="{{ u('Forum/index') }}" data-iconpos="notext" class="x-back ui-nodisc-icon" data-shadow="false"></a>
        <a class="x-sjr ui-btn-right" href="{{ u('Forum/search') }}"><i class="x-serico"></i></a>
    </div>
@stop

@section('content')
    <div role="main" class="ui-content" class="lists">
        @if(count($list['top']) > 0)
        <ul class="x-toplst">
            @foreach($list['top'] as $item)
            <li>
                <i class="x-topico"></i>
                <p><a href="{{ u('Forum/detail',['id'=>$item['id']]) }}">{{$item['title']}}</a></p>
            </li>
            @endforeach
        </ul>
        @endif
        @if(count($list['nottop']) > 0)
        @foreach($list['nottop'] as $item)
        <div class="x-post">
            <div class="post1 clearfix" onclick="window.location.href='{{ u('Forum/detail',['id'=>$item['id']]) }}'">
                @if($item['images'][0])
                <a href="{{ $item['images'][0] }}"><img src="{{ formatImage($item['images'][0], 100, 100) }}" /></a>
                @endif
                <p><a href="{{ u('Forum/detail',['id'=>$item['id']]) }}">{{$item['title']}}</a></p>
            </div>
            <p class="f12 c-green">
                <img src="@if(!empty($item['user']['avatar'])) {{formatImage($item['user']['avatar'],46,46)}} @else {{ asset('wap/community/client/images/shqimg1.png')}} @endif" class="post-pic" />
                <span class="c-black">{{$item['user']['name']}}</span>
                <span class="zan @if($item['praise']) on @endif" data-id="{{$item['id']}}" data-num="{{$item['goodNum']}}">{{$item['goodNum']}}</span>
                <span class="cmd">{{$item[rateNum]}}</span>
                <span class="time fr">{{  formatTime($item['createTime']) }}前</span>
            </p>
        </div>
        @endforeach
        @endif
        <a href="{{ u('Forum/addbbs',['plateId'=>$plate['id']]) }}" data-ajax="false" class="x-posted">发帖</a>
    </div>
    @include('wap.community._layouts.swiper')
@stop
@section('js')
    <script src="{{ asset('static/infinite-scroll/jquery.infinitescroll.js') }}"></script> 
    <script type="text/javascript">
        $(function() {
            $.SwiperInit('.lists','.x-post',"{{ u('Forum/lists',$args) }}");

            $(".x-post .zan").touchend(function(){
                var num = parseInt($(this).data('num'));
                var id = $(this).data('id');
                var zan = $(this);
                $.post("{{ u('Forum/updateLike') }}",{'id':id},function(res){
                    if (res.code == 0) {
                        //alert(zan.hasClass("on"))
                        if(zan.hasClass("on")){//取消点赞
                            zan.removeClass("on");
                            zan.text(num);
                            zan.attr('data-num', num);
                        }else{//点赞
                            zan.addClass("on");
                            zan.text(num+1);
                            zan.attr('data-num', num+1);
                        }
                    } else {
                        $.showError(res.msg);
                    }
                },"json");
            });

        });
    </script>
@stop