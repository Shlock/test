@extends('wap.community._layouts.base')
@section('show_top')
<div data-role="header" data-position="fixed" class="x-header">
    <h1>优惠券</h1>
    <a href="javascript:history.back(-1);" data-iconpos="notext" class="x-back ui-nodisc-icon" data-shadow="false"></a>
    <!--<a class="x-sjr ui-btn-right y-yd" href="{{ u('Coupon/get') }}">领券@if($count > 0)<span></span>@endif</a>-->
</div>
@stop

@section('js') 
    <script src="{{ asset('static/infinite-scroll/jquery.infinitescroll.js') }}"></script> 
@stop 
@section('content')
    <div role="main">
        <ul class="y-yhq">
            <li class="y-available @if((int)$args['status'] == 0) on @endif"><a href="{{ u('Coupon/index') }}">未使用</a></li>
            <li class="y-failure @if((int)$args['status'] == 1) on @endif"><a href="{{ u('Coupon/index',['status' => 1]) }}">已失效</a></li>
        </ul>
        <div class="y-yhqsm f12">
            <span>有<span class="c-red">{{ $list['count'] }}</span>张优惠券</span>
            <span class="fr c-red">
                <img src="{{ asset('default') }}">
                <a href="{{ u('More/detail',['code' => 5]) }}">优惠券说明</a>
            </span>
        </div>
        @if(!empty($list['list']))
        <ul class="y-xcqlst y-start">
             @include('wap.community.coupon.item')
        </ul>
        @else
            <div class="y-null1">
                <div class="y-null11">
                    <img src="{{ asset('wap/community/client/images/null.png')}}" class="y-imgnull">
                    <!-- <p><a href="{{ u('Coupon/index') }}">空空如也，快去兑换吧！</a></p> -->
                </div>
            </div>
        @endif
        <div class="y-post">
            <div class="y-text"><textarea placeholder="我有兑换码…" id="sn"></textarea></div>
            <input type="submit" class="y-htbtn" value="立即兑换" id="exchange"/>
        </div>
    </div>
@include('wap.community._layouts.swiper')
<script type="text/javascript">
jQuery(function(){
    $.SwiperInit('.y-start','li',"{{ u('Coupon/index',$args) }}");
    $("#exchange").touchend(function(){
        var sn = $("#sn").val();
        $.post("{{ u('Coupon/excoupon') }}",{sn:sn},function(res){
            if(res.code == 0){
                $.showSuccess(res.msg,"{{ u('Coupon/index') }}");
            }else{
                $.showError(res.msg);
            }
        },"json");
    })
});
</script>
@stop
