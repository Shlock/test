@extends('wap.community._layouts.base')
@section('show_top')
    <div data-role="header" data-position="fixed" class="x-header">
        <h1>论坛消息</h1>
        <a href="@if(!empty($nav_back_url)) {{ $nav_back_url }} @else javascript:history.back(-1); @endif" data-iconpos="notext" class="x-back ui-nodisc-icon" data-shadow="false"></a>
    </div>
@stop
@section('content')
    <div role="main" class="" style="background-color:#fff;">
        <ul class="y-xtxx" id="msg">
            @if(!empty($list))
                @include('wap.community.forummsg.index_item')
            @else
                <div class="x-serno c-green">
                    <img src="{{  asset('wap/community/client/images/ico/cry.png') }}"  />
                    <span>暂时没有消息</span>
                </div>
            @endif
        </ul>
    </div>
    <!--删除隐藏层-->
    <div data-role="navbar" class="x-xxdel none">
        <ul>
            <li class="fr1 cansel">取消</li>
            <li>删除</li>
        </ul>
    </div>
</div>
@include('wap.community._layouts.swiper')
</div>
<script src="{{ asset('static/infinite-scroll/jquery.infinitescroll.js') }}"></script> 
    <script type="text/javascript">
        // 列表删除效果
        $(function() {
            $(".y-xtxx").css("min-height",$(window).height()-45);
            $.SwiperInit('#msg','li',"{{ u('Froummsg/index') }}");

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
            $(".f-chkall").touchend(function(){
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
            // $(".y-xtxx li").click(function(){
            //     var id = $(this).data("id");
            //     $.post("{{u('Forummsg/readmsg')}}",{'id':id},function(res){
            //         if(res.code == 0){
            //             window.location.href = "{{ u('Forummsg/index')}}";
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

        });
    </script>
@stop

