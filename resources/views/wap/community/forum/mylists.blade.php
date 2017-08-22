@extends('wap.community._layouts.base')

@section('show_top')
    <div data-role="header" data-position="fixed" class="x-header">
        <h1>我的帖子</h1>
        <a href="@if(!empty($nav_back_url)) {!! $nav_back_url !!} @else javascript:history.back(-1); @endif" data-iconpos="notext" class="x-back ui-nodisc-icon" data-shadow="false"></a>
    </div>
@stop

@section('content')
    <div role="main" class="ui-content">
        <ul class="y-wdtz">
            <li @if($args['type'] == 0)class="on"@endif><a href="{{ u('Forum/mylists',['type'=>0]) }}">发表的帖子</a></li>
            <li @if($args['type'] == 1)class="on"@endif><a href="{{ u('Forum/mylists',['type'=>1]) }}">回复的帖子</a></li>
            <li @if($args['type'] == 2)class="on"@endif><a href="{{ u('Forum/mylists',['type'=>2]) }}">点赞的帖子</a></li>
        </ul>
        @if($list)
        <ul class="x-lifelst y-lifelst">
            @include('wap.community.forum.mylists_item')
        </ul>
        @else
            <div class="x-serno c-green">
                <img src="{{  asset('wap/community/client/images/ico/cry.png') }}"  />
                <span>你还没有帖子哦！</span>
            </div>
        @endif
    </div>
    @include('wap.community._layouts.swiper')
@stop
@section('js')
<script src="{{ asset('static/infinite-scroll/jquery.infinitescroll.js') }}"></script> 
<script type="text/javascript">
$(function(){
    var type = "{{$args['type']}}";
    if (type == 0) {
        $.SwiperInit('.y-lifelst', '.x-postlist',"{!! u('Forum/mylists',$args) !!}");
    };
    
    $(".x-lifelst").css("min-height",$(window).height()-102);

    $('.y-lifelst li > .y-tzli').on('touchstart', function(e) {
       
        $('.y-lifelst li > .y-tzli').css('left', '0px') // 关闭所有
        $(e.currentTarget).addClass('open')
        x = e.originalEvent.targetTouches[0].pageX // 锚点
    }).on('touchmove', function(e) {
     
        var change = e.originalEvent.targetTouches[0].pageX - x
        change = Math.min(Math.max(-140, change), 0) //左边-100px,右边0px
        e.currentTarget.style.left = change + 'px'
        if (change < -10) disable_scroll() // 当大于10px的滑动时，禁止滚动
    }).on('touchend', function(e) {
      
        var left = parseInt(e.currentTarget.style.left)
        var new_left;
        if (left < -35) {
            new_left = '-140px'
        } else if (left > 35) {
            new_left = '140px'
        } else {
            new_left = '0px'
        }
        // e.currentTarget.style.left = new_left
        $(e.currentTarget).animate({left: new_left}, 200)
        enable_scroll();
    });

    $('li .btn-del').on('touchend', function(e) {
        var id = $(this).data('id');
        var del = $(this);
        if(confirm("确认要删除吗?")) {
            $.post("{{ u('Forum/delete') }}",{'id':id},function(res){
                if (res.code == 0) {
                    e.preventDefault()
                    del.parents('li').slideUp('fast', function() {
                        del.remove()
                    })
                } else {
                    $.showError(res.msg);
                }
            },"json");
        } 
        return false;
    })
    
});
</script>
@stop