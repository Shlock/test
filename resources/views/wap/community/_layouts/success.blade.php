@extends('wap.run._layouts.base')

@section('show_top') 
	<nav class="m-nyy">
        <span>成功提示</span>
        <a href="javascript:;" class="f-navdh"></a>
    </nav> 
@stop 
 
@section('content')  
	<div data-role="content" class="d-content">
		<img src="{{ asset("wap/images/success.png") }}" width="100%" />
		<p class="pt10 tc">{{ $msg }}</p>
		<p class="pt10 tc">
		将在 <span id="wait">3</span> 秒后自动跳转，
		@if (empty($url))
		<a href="javascript:history.back();" style="color:#333;">直接跳转</a>
		@else
		<a href="{{ $url }}" style="color:#333;">直接跳转</a>
		@endif
	</p>
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
