@extends('wap.community._layouts.base')
@section('show_top')
<div data-role="header" data-position="fixed" class="x-header">
    <h1>我的订单</h1>
    <a href="@if(!empty($nav_back_url)) {{ $nav_back_url }} @else javascript:history.back(-1); @endif" data-ajax="false" data-iconpos="notext" class="x-back ui-nodisc-icon" data-shadow="false"></a>
</div>
@stop

@section('js') 
    <script src="{{ asset('static/infinite-scroll/jquery.infinitescroll.js') }}"></script> 
@stop 
@section('content')
<ul class="y-wddd">
    <li @if($args['status'] == 0 ) class="on" @endif><a href="{{ u('Order/index',array('status'=>0)) }}">全部订单</a></li>
    <li @if($args['status'] == 1 ) class="on" @endif><a href="{{ u('Order/index',array('status'=>1)) }}">待评价({{ $list['commentNum'] }})</a></li>
</ul>
@if(!empty($list))
    <ul class="y-wdddmain" id="wdddmain">
        @include('wap.community.order.item')
    </ul>
 @else
    <div class="x-serno c-green">
        <img src="{{  asset('wap/community/client/images/ico/cry.png') }}"  />
        <span>很抱歉！你还没有订单！</span>
    </div>
@endif
@include('wap.community._layouts.bottom')
@include('wap.community._layouts.swiper')
<script type="text/javascript">
jQuery(function(){
    $.SwiperInit('#wdddmain','.list_item',"{{ u('Order/index',$args) }}");
    
    $(document).on("touchend",".url",function(){
    	window.location.href = $(this).data('url');
    }).on("touchend",".okorder",function(){
    	var oid = $(this).data('id');
    	$.showOperation('确认删除订单吗？',"javascript:$.delOrders("+oid+");",'确认删除');
    }).on("touchend",".confirmorder",function(){
    	var oid = $(this).data('id');
    	$.showOperation('确认完成订单吗？',"javascript:$.confirmOrder("+oid+");",'确认完成');
    }).on("touchend",".cancelorder",function(){
    	var oid = $(this).data('id');
        var status = $(this).data('status');
		var seller = $(this).data('seller');
		var tel = $(this).data('tel');
        //alert(status);
        if(status == "1"){
            $.showOrderCancelNotice("商家已接单,如需取消订单请电话联系"+ seller +":" + tel,"tel:" + tel,"提示");
        }else{
            $.showOperation('确认取消订单吗？',"javascript:$.cancelOrder("+oid+");",'确认取消');
        }

    })
});

</script>
@include('wap.community.order.orderjs')
@stop
