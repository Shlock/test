@extends('seller._layouts.base')
@section('css')
@stop
@section('content')
	@yizan_begin
		<div>
			<div class="m-zjgltbg">
				<div class="p10">
					<p class="f-bhtt f14 clearfix">
						<span class="ml15 fl">提交成功</span>
					</p>
					<div class="x-quyu1">
						<p class="x-ml x-mtc0">服务状态：<span>等待审核...</span></p>
                        <p class="x-ml">您的服务信息已提交，预计1至2个工作日内审核完成，请您耐心等待！</p>
					</div>
					<p class="tc mt20 mb20">
						<a href="{{ u('Goods/index') }}" class="btn f-bluebtn f-170btn" style="line-height:30px; padding:0;">进入服务管理</a>
                        <a href="{{ u('Goods/audit') }}" class="btn f-170btn ml20" style="line-height:30px;">进入服务审核</a>
					</p>
				</div>
			</div>
		</div>
	@yizan_end
@stop

@section('js')
@stop
