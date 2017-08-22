@extends('seller._layouts.sign_base')
@section('css')
@stop
@section('content') 
	<div class="p20">
		<div class="m-zjgltbg">
			<div class="p10">						
				<p class="f-bhtt f14 clearfix">
					<span class="ml15 fl">密码找回成功</span>
				</p>
				<div class="m-quyu1">
					<p class="tc f-tjghtip">
						<img src="{{ asset('images/ico/xlico.png') }}" alt="">
						<span>恭喜！密码找回成功，请妥善保管。</span>
					</p>
				</div>
				<p class="tc mt20 mb20">
					<a href="{{ u('Public/login') }}" class="btn f-170btn ml20">返回重新登录</a>
				</p>
			</div>
		</div>
	</div>
@stop
@section('js')
<script type="text/javascript">
	$(function(){
		$("#title").text("忘记密码");
	});
</script>
@stop
