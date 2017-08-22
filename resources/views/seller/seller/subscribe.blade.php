@extends('seller._layouts.base')
@section('css')
@stop
@section('content')
	<div class="p20">
		<div class="m-zjgltbg">
			<div class="p10">						
				<p class="f-bhtt f14 clearfix">
					<span class="ml15 fl">预约状态设置</span>
					<a href="{{ u('Seller/index') }}" class="fr mr15 btn f-bluebtn" style="margin-top:8px;">返回</a>
				</p>
				<div class="m-quyu1">
					<div class="m-inforct" style="padding-top:28px;width:750px;"> 
						<div>
							<span>日期选择：</span> 
							<input type="text" name="appointTimeymd" class="appointdate u-ipttext" onchange="onblurs(this)" value="点击选择时间" id="datetimepicker4"/>
    						<span class="msg"></span> 
						</div>
						@yizan_begin 
							@include('seller.seller.showtime')   
						@yizan_end 
					</div>
				</div>
			</div>
		</div>
	</div>
@stop

@section('js') 
@include('seller._layouts.alert')
@stop
