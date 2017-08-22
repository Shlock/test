@extends('wap.community._layouts.base')


@section('show_top')
    <div data-role="header" data-position="fixed" class="x-header">
        <h1>{{$data['plate']['name']}}</h1>
        <a href="{{ u('Forum/lists',['plateId'=>$data['plate']['id']]) }}" data-iconpos="notext" class="x-back ui-nodisc-icon" data-shadow="false"></a>
        <a class="x-sjr ui-btn-right y-sjr"><img src="{{  asset('wap/community/client/images/ico/shenglue.png') }}" width="23" /></a>
    </div>
@stop

@section('content')
    <div class="y-lllt">
        <ul>
            <li>
                <div class="y-ltimg"><img src="@if(!empty($data['user']['avatar'])) {{formatImage($data['user']['avatar'],50,50)}} @else {{ asset('wap/community/client/images/shqimg1.png')}} @endif"></div>
                <div class="y-llltcont">
                    <p><strong class="f14">{{$data['user']['name']}}</strong><span class="f12 y-ltcolor">楼主</span><span class="f12 fr c-green">{{yztime($data['createTime'])}}</span></p>
                    <p><span class="f12 c-green">来自{{$data['plate']['name']}}</span></p>
                </div>
            </li>
            <li>
                <h3 class="f16">{{$data['title']}}</h3>
                <p class="f14">{{$data['content']}}</p>
                @if(count($data['images']) > 0)
                @foreach($data['images'] as $item)
                <div class="y-ltmain"><img src="{{$item}}"></div>
                @endforeach
                @endif
            </li>
            <li>
                <p class="f12">
                    <span>{{$data['address']['name']}}</span>
                    <span class="ml5">{{$data['address']['mobile']}}<a href="tel:{{$data['address']['mobile']}}"><img src="{{ asset('wap/community/client/images/ico/tzxqdh.png')}}" width="15" class="ml10"></a></span>
                </p>
                <p class="f12">
                    <span class="fl">地址：</span>
                    <span class="y-tzxqaddr">{{$data['address']['address']}}</span>
                </p>
            </li>
            <li class="y-pl">
                <div class="y-ltpl">
                    <span class="y-dz f12 zanNum">{{$data['goodNum']}}</span>
                    <span class="y-pl f12">{{$data['rateNum']}}</span>
                </div>
            </li>
        </ul>
        @if($data['childs'])
        <ul style="margin-top:10px;" class="replists">
            @foreach($data['childs'] as $key => $val)
            <li class="replist" >
                <div class="y-ltimg"><img src="@if(!empty($val['user']['avatar'])) {{formatImage($val['user']['avatar'],50,50)}} @else {{ asset('wap/community/client/images/shqimg1.png')}} @endif"></div>
                <div class="y-llltcont">
                    <p><strong class="f14">{{$val['user']['name']}}</strong><span class="f12 fr c-green">{{$key+1}}楼</span></p>
                    <p><span class="f12 c-green">来自{{$val['plate']['name']}}</span><span class="f12 fr c-green">{{yztime($val['createTime'])}}</span></p>
                    <p class="f12">{!!$val['content']!!}</p>
                    @if($val['replyContent'])
                    <div class="y-plhfmain">
                        <strong class="f12">{{$val['replyContent']}}</strong>
                        <p class="f12">{{$val['replyPosts']['content']}}</p>
                    </div>
                    <p class="y-lthfp"><span class="fr f12 y-lthfbtn reply" data-id="{{$val['id']}}" data-name="{{$val['user']['name']}}">回复</span></p>
                    @else
                    <p class="y-lthfp"><span class="fr f12 y-lthfbtn reply" data-id="{{$val['id']}}" data-name="{{$val['user']['name']}}">回复</span></p>
                    @endif
                </div>
            </li>
            @endforeach
        </ul>
        @endif
        
        <div class="y-post">
            <div class="y-text"><textarea id="repcontent" data-pid="{{$data['id']}}" data-id='0'></textarea></div>
            <input type="button" class="y-htbtn" value="回帖" id="subreply"/>
        </div>
    </div>
    @include('wap.community._layouts.swiper')
    <ul class="y-ltmore none">
        @if($args['isLandlord'] == 1)
        <li><a href="{{ u('Forum/detail',['id'=>$data['id'],'isLandlord'=>0]) }}" class="f12"><img src="{{  asset('wap/community/client/images/ico/ltimg1.png') }}">查看全部</a></li>
        @else
        <li><a href="{{ u('Forum/detail',['id'=>$data['id'],'isLandlord'=>1]) }}" class="f12"><img src="{{  asset('wap/community/client/images/ico/ltimg1.png') }}">只看楼主</a></li>
        @endif
        @if($args['sort'] == 1)
        <li><a href="{{ u('Forum/detail',['id'=>$data['id'],'sort'=>0]) }}" class="f12"><img src="{{  asset('wap/community/client/images/ico/ltimg2.png') }}">正序查看</a></li>
        @else
        <li><a href="{{ u('Forum/detail',['id'=>$data['id'],'sort'=>1]) }}" class="f12"><img src="{{  asset('wap/community/client/images/ico/ltimg2.png') }}">倒序查看</a></li>
        @endif
        <li><a href="javascript:;" class="f12 zan @if($data['isPraise'])on @endif" data-id="{{$data['id']}}"><img src="{{  asset('wap/community/client/images/ico/ltimg3.png') }}"><span class="like">@if($data['isPraise']) 取消喜欢 @else 喜欢 @endif</span></a></li>
        <li><a href="{{ u('Forum/complain',['id'=>$data['id']]) }}" class="f12"><img src="{{  asset('wap/community/client/images/ico/ltimg4.png') }}">举报</a></li>
    </ul>
@stop
@section('js')
<script src="{{ asset('static/infinite-scroll/jquery.infinitescroll.js') }}"></script> 
<script type="text/javascript">
$(function(){
    $.SwiperInit('.replists', '.replist',"{!! u('Forum/detail',$args) !!}");

    $(document).delegate(".y-lllt", "touchend", function(e) {  
        if(!$(".y-ltmore").hasClass("none")){
            $(".y-ltmore").addClass("none");
        }
    }); 
    $(".x-header .y-sjr").click(function(){
        if($(".y-ltmore").hasClass("none")){
            $(".y-ltmore").removeClass("none");
        }else{
            $(".y-ltmore").addClass("none");
        }
    });
    $(".y-lllt ul li .reply").click(function(){
        $(".y-post textarea").focus();
        var name = $(this).data('name');
        var id = $(this).data('id');
        $(".y-post textarea").attr('placeholder' ,'回复'+name+'：');
        $(".y-post textarea").attr('data-id', id);
    });
    
    $("#subreply").touchend(function() {
        var content = $(".y-post textarea").val();
        var pid = $(".y-post textarea").data('pid');
        var id = $(".y-post textarea").data('id');
        $.post("{{ u('Forum/replypost') }}",{'id':id, 'content': content, 'pid':pid},function(res){
            if (res.code == 0) {
                window.location.href="{!! u('Forum/detail',$args) !!}";
            } else {
                $.showError(res.msg);
            }
        },"json");

    })

    $(".y-ltmore .zan").touchend(function(){
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
                $.showError(res.msg);
            }
        },"json");
    });
});
</script>
@stop