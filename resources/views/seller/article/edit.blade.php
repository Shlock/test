@extends('seller._layouts.base')
@section('css')
@stop
@section('content')
	<div>
		<div class="m-zjgltbg">					
			<div class="p10">
				<!-- 服务管理 -->
				<div class="g-fwgl">
					<p class="f-bhtt f14 clearfix">
						<span class="ml15 fl">公告管理</span>
					</p>
				</div>
				<!-- 服务表格 -->
				<div class="m-tab m-smfw-ser pt20">
					@yizan_begin
	                    <yz:form id="yz_form" action="save">
							<yz:fitem name="title" label="标题"></yz:fitem>   
							<yz:fitem name="content" label="内容" type="textarea" ></yz:fitem>     
						</yz:form>
	                @yizan_end
				</div>
			</div>
		</div>
	</div> 
@stop 