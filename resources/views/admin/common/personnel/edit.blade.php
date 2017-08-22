@extends('admin._layouts.base')
@section('css')
@stop
@section('right_content')
@yizan_begin
<yz:form id="yz_form" action="update">
	@if($data['seller']['type'] == 1)
	<yz:fitem name="$data.seller.name" label="人员名称" type="text"></yz:fitem>
	<yz:fitem name="$data.seller.mobile" label="人员手机" type="text"></yz:fitem>
	<yz:fitem name="realName" label="真实名称" type="text"></yz:fitem>
	<yz:fitem name="idcardSn" label="身份证编号" type="text"></yz:fitem> 
	<yz:fitem name="idcardPositiveImg" label="身份证正面">
		<a href="{{ $data['idcardPositiveImg'] }}" target="_blank"><img src="{{ formatImage($data['idcardPositiveImg'],0,160,0) }}" alt="" height="160"></a>
	</yz:fitem>  
	<yz:fitem name="idcardNegativeImg" label="身份证背面">
		<a href="{{ $data['idcardNegativeImg'] }}" target="_blank"><img src="{{ formatImage($data['idcardNegativeImg'],0,160,0) }}" alt="" height="160"></a>
	</yz:fitem>  
	@else
	<yz:fitem name="$data.seller.name" label="机构简称" type="text"></yz:fitem>
	<yz:fitem name="$data.seller.mobile" label="机构联系电话" type="text"></yz:fitem>
	<yz:fitem name="companyName" label="机构全称" type="text"></yz:fitem>
	<yz:fitem name="businessLicenceSn" label="营业执照号" type="text"></yz:fitem>  
	<yz:fitem name="businessLicenceImg" label="营业执照">
		<a href="{{ $data['businessLicenceImg'] }}" target="_blank">
			<img src="{{ formatImage($data['businessLicenceImg'],0,160,0) }}" alt="" height="160">
		</a>
	</yz:fitem>  
	@endif
	<yz:fitem name="remark" label="处理备注" type="textarea" val="{{$data['disposeRemark']}}"></yz:fitem> 
	<yz:fitem name="status" label="认证状态">
		<php>
			if($data['status'] == 0){
				$data['status'] = -1;
			}
		</php>
		<yz:radio name="status" options="-1,1" texts="未通过,通过" checked="$data['status']" default="0"></yz:radio>
	</yz:fitem>
	<yz:fitem name="sellerId" type="hidden"></yz:fitem>
	<yz:fitem name="type" type="hidden" val="{{ $data['seller']['type'] }}"></yz:fitem>
</yz:form>
@yizan_end

@stop 


