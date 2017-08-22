@extends('admin._layouts.base')
@section('right_content')
<div class="m-tip mb15">
	<div class="clearfix bort pb15">
		<img src="{{ asset('images/ico/chaico.png') }}" alt="" class="fl imgfl">
		<div class="m-tipct">
			<p>错误提示</p>
			<p>{{ $msg }}</p>
		</div>
	</div>
	<p class="pt20">
		系统将在 <span id="wait">3</span> 秒后自动跳转，
		@if (empty($url))
		<a href="javascript:history.back();">点击直接跳转</a>
		@else
		<a href="{{ $url }}">点击直接跳转</a>
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

