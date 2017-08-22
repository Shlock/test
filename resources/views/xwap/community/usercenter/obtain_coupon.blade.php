@extends('wap.community._layouts.base')
@section('show_top')
@stop

@section('css')
    <style type="text/css">
        /*优惠券链接*/
        .d-content .y-ljsy{width: 60%;position: fixed; bottom:28%; left:50%; margin-left:-30%;}
        .d-content .y-yhq{width: 50%;position: fixed; top:25.5%; left:50%; margin-left:-25%; text-align:center;background: none;border:none;}
        .d-content .y-yhq p{ font-size:28px; color:#fff; font-family:"微软雅黑";}
        .d-content .y-yhqzhxg{width: 44%;position: fixed; top:51%; left:50%; margin-left:-22%; text-align:center;}
        .d-content .y-yhqzhxg p{ font-size:13px; font-weight:bold; color:#ffff00;}
        .d-content .y-yhqzhxg h3{ color:#fff; font-size:15px; font-weight:bold; margin-bottom:3px;}
        .d-content .y-yhqzhxg p span{ font-size:15px; padding-right:3px;}
        .d-content .y-yhqzhxg p a{ padding-left:3px;font-size:12px; color:#ffff00;}
        .d-content .y-yhqzhxg p a:hover{ color:#ffff00;}
        .begin-pic{position:fixed;z-index:0;top:0;width:100%;height:100%;margin:0;}
    </style>
@stop

@section('content')
    @if($info)
    <div data-role="content" class="d-content">
        <img class="begin-pic" src="{{ asset('wap/community/client/images/img_yhqbg.png') }}">
        <div class="y-yhq">
            <p>@if($share_active['promotion']['data'] > 0)<span>{{ $info['promotion']['data'] }}元</span>@endif优惠券</p>
        </div>
         @if(!$message)
            <div class="y-yhqzhxg">
                <h3>优惠券已放至账户</h3>
                <p><span>{{ $info['mobile'] }}</span></p>
            </div>
        @else
            <div class="y-yhqzhxg">
                <h3>{{ $message }}</h3>
            </div>
        @endif
         
        <div class="y-ljsy">
            <p><button class="x-btnsure" id="go-use">立即使用</button></p>
            <!--<p class="mt10"><button class="x-btnsure">分享</button></p>-->
        </div>
    </div>
    @else
        <div data-role="content" class="d-content">
            <img class="begin-pic" src="{{ asset('wap/community/client/images/img_yhqbg.png') }}">

            <div class="y-yhq">
                <p>优惠券</p>
            </div>

            @if(!empty($message))
                <div class="y-yhqzhxg">
                    {{ $message }}
                </div>
                <div class="y-ljsy">
                    <p><button class="x-btnsure">立即使用</button></p>
                    <!--<p class="mt10"><button class="x-btnsure">分享</button></p>-->
                </div>
            @else
                <div class="y-yhqzhxg">
                    <h3>亲，您还没有登录！</h3>
                </div>
                <div class="y-ljsy">
                    <p onclick="$.href('{{ u('User/login') }}')"><button class="x-btnsure">立即登录</button></p>
                </div>
            @endif


        </div>
    @endif
    <script type="text/javascript">
        $(document).on("touchend","#mobile",function(){
            var orderId = "{{ $args['orderId'] }}";
            $.router.load("{{ u('User/login') }}", true);
        });

        $(document).on("touchend","#go-use",function(){
            $.router.load("{{ u('Index/index') }}", true);
        });

        $(document).on("touchend","#get",function(){
       	 	location.reload();
        });
    </script>
@stop

@section('js')

@stop