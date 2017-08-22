@extends('wap.community._layouts.base')
@section('show_top')
<div data-role="header" data-position="fixed" class="x-header">
    <h1>选择优惠券</h1>
    <a href="javascript:history.back(-1);" data-iconpos="notext" class="x-back ui-nodisc-icon" data-shadow="false"></a>
    <a class="x-sjr ui-btn-right y-yd" href="{{ u('Order/order',['cartIds'=>$args['cartIds'],'addressId'=>$args['addressId'], 'cancel'=>1]) }}">取消选择</a>
</div>
@stop
@section('js') 
    <script src="{{ asset('static/infinite-scroll/jquery.infinitescroll.js') }}"></script> 
@stop 
@section('content')
    <div role="main">
        @if(!empty($list))
            <ul class="y-xcqlst">
                @include('wap.community.coupon.use_item')
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
    $.SwiperInit('.y-start','li',"{{ u('Coupon/usepromotion',$args) }}");
});
</script>
@stop
