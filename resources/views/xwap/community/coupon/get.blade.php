@extends('wap.community._layouts.base')
@section('show_top')
<div data-role="header" data-position="fixed" class="x-header">
    <h1>领取优惠券</h1>
    <a href="javascript:$.back();" data-iconpos="notext" class="x-back ui-nodisc-icon" data-shadow="false"></a>
</div>
@stop
@section('js') 
    <script src="{{ asset('static/infinite-scroll/jquery.infinitescroll.js') }}"></script> 
@stop 
@section('content')
    <div role="main">
        @if(!empty($list))
            <ul class="y-xcqlst">
                @include('wap.community.coupon.get_item')
            </ul>
        @else
            <div class="y-null1">
                <div class="y-null11">
                    <img src="{{ asset('wap/community/client/images/null1.png') }}" class="y-imgnull">
                    <p><a href="{{ u('Coupon/index') }}">空空如也，快去兑换吧！</a></p>
                </div>
            </div>
        @endif
    </div>
@include('wap.community._layouts.swiper')
<script type="text/javascript">
jQuery(function(){
    $.SwiperInit('.y-start','li',"{{ u('Coupon/get',$args) }}");
    $(".y-xcqlst li").touchend(function(){
        var proid = $(this).data("proid");
        $.post("{{ u('Coupon/excoupon') }}",{id:proid},function(res){
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
