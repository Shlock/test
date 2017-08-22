@extends('xwap.community._layouts.base')

@section('css')
@stop

@section('show_top') 
	<header class="bar bar-nav y-barnav">
        <a class="button button-link button-nav pull-left" href="@if(!empty($nav_back_url)) {!! $nav_back_url !!} @else {{u('GoodsCart/index')}} @endif" data-transition='slide-out'>
            <span class="icon iconfont">&#xe600;</span>
        </a>
        <h1 class="title f16">错误提示</h1>
    </header>
@stop

@section('content')
	<div class="content">
        <div class="x-null pa w100 tc y-ordpay">
            <img src="{{ asset('wap/community/newclient/images/zfsb-img.png') }}" width="110">
            <p class="f12 c-gray mt10">{{ $msg }}</p>
            <p><span class="c-red" id="wait">3</span>秒后自动跳转</p>
            <a class="f16 c-white x-btn db" href="javascript:$.back();">直接跳转</a>
        </div>
    </div>
@stop

@section($js)
<script type="text/javascript">
$(function(){
	var wait = 3;
	var func = function(){
		wait--;
		if(wait < 1){
			@if (empty($url))
				$.back();
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
