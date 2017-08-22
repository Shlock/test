@yizan_include('admin.common.personnel.edit')

@yizan_section('ServiceApply')
<yz:fitem name="$data.seller.name" label="机构简称" type="text"></yz:fitem>
<yz:fitem name="$data.seller.mobile" label="机构联系电话" type="text"></yz:fitem>
<yz:fitem name="companyName" label="机构全称" type="text"></yz:fitem>
<yz:fitem name="businessLicenceSn" label="营业执照号" type="text"></yz:fitem>  
<yz:fitem name="businessLicenceImg" label="营业执照">
	<a href="{{ $data['businessLicenceImg'] }}" target="_blank">
		<img src="{{ formatImage($data['businessLicenceImg'],0,160,0) }}" alt="" height="160">
	</a>
</yz:fitem>  
@yizan_stop 
@yizan_section('list_column')
@yizan_stop