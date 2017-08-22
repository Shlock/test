@extends('xwap.community._layouts.base')


@section('show_top')
    <header class="bar bar-nav x-header">
        <a class="button button-link button-nav pull-left pageloading back" href="{{ u('Forum/lists',['plateId'=>$data['plate']['id']]) }}" data-transition='slide-out'>
            <span class="icon iconfont">&#xe600;</span>
        </a>
        <h1 class="title f16">{{$data['plate']['name']}}</h1>
        <a class="button button-link button-nav pull-right open-popup toedit y-sjr" data-popup=".popup-about">
            <img src="{{ asset('wap/community/newclient/images/ico/shenglue.png') }}" class="vam" width="20">
        </a>
    </header>
@stop

@section('content')
    <nav class="bar bar-tab x-replypost">
        <div class="search-input fl">
          <input type="search" placeholder="" class="m0" id="repcontent" data-pid="{{$data['id']}}" data-id='0'>
        </div>
        <a class="button button-fill button-primary c-white fl c-bg"  id="subreply">回帖</a>
    </nav>
    <ul class="x-ltmore f12 c-gray y-ltmore none">
        @if($args['isLandlord'] == 1)
            <li onclick="$.href('{{ u('Forum/detail',['id'=>$data['id'],'isLandlord'=>0]) }}')">
                <i class="icon iconfont mr5">&#xe656;</i>查看全部
            </li>
        @else
            <li onclick="$.href('{{ u('Forum/detail',['id'=>$data['id'],'isLandlord'=>1]) }}')">
                <i class="icon iconfont mr5">&#xe656;</i>只看楼主
            </li>
        @endif

        @if($args['sort'] == 1)
            <li onclick="$.href('{{ u('Forum/detail',['id'=>$data['id'],'sort'=>0]) }}')">
                <i class="icon iconfont mr5">&#xe631;</i>正序查看
            </li>
        @else
            <li onclick="$.href('{{ u('Forum/detail',['id'=>$data['id'],'sort'=>1]) }}')">
                <i class="icon iconfont mr5">&#xe631;</i>倒序查看
            </li>
        @endif
        <li data-id="{{$data['id']}}" class="zan">
            <i class="icon iconfont mr5">&#xe651;</i><span class="like">@if($data['isPraise']) 取消喜欢 @else 喜欢 @endif</span>
        </li>
        <li onclick="$.href('{{ u('Forum/complain',['id'=>$data['id']]) }}')">
            <i class="icon iconfont mr5">&#xe626;</i>举报
        </li>
    </ul>

    <div class="content" id=''>
        <!-- 内容区 -->
        <div class="list-block media-list x-comment nobor">
            <ul>
                <li class="ml10">
                    <div class="item-content pl0">
                        <div class="item-media mt5">
                            <img src="@if(!empty($data['user']['avatar'])) {{formatImage($data['user']['avatar'],50,50)}} @else {{ asset('wap/community/client/images/shqimg1.png')}} @endif">
                        </div>
                        <div class="item-inner">
                            <div class="item-title-row">
                                <div class="item-title c-black f14">{{$data['user']['name']}}<span class="c-white c-bg ml10 x-poster">楼主</span></div>
                                <div class="item-after c-gray f12">{{yztime($data['createTime'])}}</div>
                            </div>
                            <div class="item-title-row">
                                <div class="item-title c-gray f12">来自{{$data['plate']['name']}}</div>
                            </div>
                        </div>
                    </div>
                </li>
                <li>
                    <div class="item-content">
                        <div class="item-inner">
                            <div class="item-title-row f16">
                                <div class="item-title c-black">{{$data['title']}}</div>
                            </div>
                            <div class="f14 c-black mt5 lh20">{{$data['content']}}</div>
                            @if(count($data['images']) > 0)
                                @foreach($data['images'] as $item)
                                    @if(!empty($item))
                                        <img src="{{$item}}" class="w100">
                                    @endif
                                @endforeach
                            @endif
                            @if(!empty($data['address']['mobile']))
                                <p class="f12 c-black">{{$data['address']['name']}} {{$data['address']['mobile']}}<a class="icon iconfont mt5 c-red f18 vat ml10">&#xe60a;</a></p>
                            @endif
                            @if(!empty($data['address']['address']))
                                <p class="f12">地址：{{$data['address']['address']}}</p>
                            @endif
                            <div class="tc p10">
                                <div class="c-bg x-postpl c-white">
                                    <i class="icon iconfont mt5 love">&#xe651;</i>
                                    <p class="f12 zanNum">{{$data['goodNum']}}</p>
                                </div>
                                <div class="c-bg x-postpl c-white">
                                    <i class="icon iconfont mt5 comment">&#xe64f;</i>
                                    <p class="f12">{{$data['rateNum']}}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </li>
            </ul>
        </div>
        <!-- 评论区 -->
        @if($data['childs'])
            <div class="list-block media-list x-comment nobor">
                <ul>
                    @foreach($data['childs'] as $key => $val)
                        <li class="ml10">
                            <div class="item-content pl0">
                                <div class="item-media">
                                    <img src="@if(!empty($val['user']['avatar'])) {{formatImage($val['user']['avatar'],50,50)}} @else {{ asset('wap/community/client/images/shqimg1.png')}} @endif">
                                </div>
                                <div class="item-inner">
                                    <div class="item-title-row">
                                        <div class="item-title c-black f14">{{$val['user']['name']}}</div>
                                        <div class="item-after c-gray f12">{{$key+1}}楼</div>
                                    </div>
                                    <div class="item-title-row">
                                        <div class="item-title c-gray f12">来自{{$val['plate']['name']}}</div>
                                        <div class="item-after c-gray f12">{{yztime($val['createTime'])}}</div>
                                    </div>
                                    <div class="f12 lh16 c-black">{!!$val['content']!!}</div>
                                    @if($val['replyContent'])
                                        <div class="f12 x-reply mt10 c-black p10">
                                            <p class="bold">{{$val['replyContent']}}</p>
                                            <p>{{$val['replyPosts']['content']}}</p>
                                        </div>
                                    @endif
                                    <a href="#" class="fr x-borbtn c-red f12 mt10 reply" data-id="{{$val['id']}}" data-name="{{$val['user']['name']}}">回复</a>
                                </div>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>
@stop

@section($js)
<script type="text/javascript">
$(function(){

    $(document).delegate(".y-lllt", "click", function(e) {  
        if(!$(".y-ltmore").hasClass("none")){
            $(".y-ltmore").addClass("none");
        }
    }); 
    $(document).on("touchend",".x-header .y-sjr",function(){
        if($(".y-ltmore").hasClass("none")){
            $(".y-ltmore").removeClass("none");
        }else{
            $(".y-ltmore").addClass("none");
        }
    });
    $(document).on("touchend","a.reply",function(){
        var name = $(this).attr('data-name');
        var id = $(this).attr('data-id');
        $("#repcontent").attr('placeholder' ,'回复'+name+'：');
        $("#repcontent").attr('data-id', id);
        $("#repcontent").focus();
        return false;
    });

    $(document).on("touchend","#subreply",function(){
        var content = $("#repcontent").val();
        var pid = $("#repcontent").data('pid');
        var id = $("#repcontent").data('id');
        $.post("{{ u('Forum/replypost') }}",{'id':id, 'content': content, 'pid':pid},function(res){
            if (res.code == 0) {
                $.alert(res.msg,function(){
                    $.router.load("{!! u('Forum/detail',$args) !!}", true);
                });
            }else{
                $.alert(res.msg);
            }
        },"json");

    })
    $(document).on("touchend",".y-ltmore .zan",function(){
        var num = parseInt($(this).text());
        var id = $(this).data('id');
        var zan = $(this);
        $.post("{{ u('Forum/updateLike') }}",{'id':id},function(res){
            if (res.code == 0) {
                if(zan.hasClass("on")){//取消点赞
                    zan.removeClass("on");
                    var zannum = parseInt($(".zanNum").text());
                    $('.zanNum').text(zannum-1);
                    $(".like").text('喜欢');
                }else{//点赞
                    zan.addClass("on");
                    var zannum = parseInt($(".zanNum").text());
                    $('.zanNum').text(zannum+1);
                    $(".like").text('取消喜欢');
                }
            } else {
                $.alert(res.msg);
            }
        },"json");
    });

});
</script>
@stop