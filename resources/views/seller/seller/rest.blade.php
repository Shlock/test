@extends('seller._layouts.base')
@section('css')
@stop
@section('content')
	<div class="p20">
		<div class="m-zjgltbg">
			<div class="p10">						
				<p class="f-bhtt f14 clearfix">
					<span class="ml15 fl">其他设置</span>
					<a href="{{ u('Seller/index') }}" class="fr mr15 btn f-bluebtn" style="margin-top:8px;">返回</a>
				</p>
				<div class="m-quyu1">
					<div class="m-inforct" style="padding-top:78px;width:750px;"> 
						@yizan_begin
						<yz:form id="yz_form" action="updaterest">
							<yz:fitem name="brief" label="简介" type="textarea"></yz:fitem> 
							<yz:fitem name="status" label="状态">
								<yz:radio name="status" options="0,1" texts="停业,正常" checked="$data['status']"></yz:radio>
							</yz:fitem>
							<yz:fitem name="sort" label="排序"></yz:fitem>
						</yz:form>		
						@yizan_end 
					</div>
				</div>
			</div>
		</div>
	</div>
@stop

@section('js') 
@stop
