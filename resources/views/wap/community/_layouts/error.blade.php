@extends('wap.community._layouts.base_order')
@section('css')
	<style>
		body .d-page{background:#FFF !important}
	</style>
@stop
@section('show_top') 
    <div data-role="header" data-position="fixed" class="x-header">
		<h1>错误提示</h1>
        <a href="" data-iconpos="notext" class="x-back ui-nodisc-icon" data-shadow="false"></a>
	</div>
@stop 
@section('content')  
	<div data-role="content" class="d-content">		 
		<!-- <div class="y-zfcgtop">
    		<p class="f14 c-red">某某某会员，您的订单：20150919123456789</p>
	    </div> -->
	    <div class="y-zfcgmain">
	    	<img src="{{ asset('wap/community/client/images/zfsb-img.png') }}">
	        <p class="f14">{{ $msg }}<br><span class="c-red" id="wait">3</span>秒后自动跳转</p>
	        <a href="javascript:history.back();">直接跳转</a>
	    </div>
	</div>
<script type="text/javascript">
jQuery(function($){
	var wait = 3;
	var func = function(){
		wait--;
		if(wait < 1){
			@if (empty($url))
				history.back();
			@else
				location.href = "{{ $url }}";
			@endif
		}else{
			$("#wait").html(wait);
			setTimeout(func,1000);
		}
	}
	setTimeout(func,1000);
});
</script>
@stop 
