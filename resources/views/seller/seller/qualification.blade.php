@extends('seller._layouts.base')
@section('css')
@stop
@section('content')
	<div class="p20">
		<div class="m-zjgltbg">
			<div class="p10">						
				<p class="f-bhtt f14 clearfix">
					<span class="ml15 fl">认证设置</span>
					<a href="{{ u('Seller/index') }}" class="fr mr15 btn f-bluebtn" style="margin-top:8px;">返回</a>
				</p>
				<div class="m-quyu1">
					<div class="m-inforct" style="padding-top:78px;width:750px;"> 
						@yizan_begin
						<yz:form id="yz_form" action="updaqualification">
							<yz:fitem name="authenticate.idcardSn" label="身份证编号"></yz:fitem>  
							<yz:fitem name="authenticate.idcardPositiveImg" label="身份证正面" type="image"></yz:fitem>  
							<yz:fitem name="authenticate.idcardNegativeImg" label="身份证背面" type="image"></yz:fitem> 	
							@if($data['type'] == 2)
							<yz:fitem name="authenticate.businessLicenceImg" label="营业执照" type="image"></yz:fitem>  
							@elseif($data['type'] == 1)
							<yz:fitem name="authenticate.certificateImg" label="资质认证" type="image"></yz:fitem>	
							@endif
							<yz:fitem name="isAuthenticate" label="认证状态">
								@if($data['isAuthenticate'] == 1)
									已认证
								@else
									未认证
								@endif
							</yz:fitem>
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
