@extends('admin._layouts.base')
@section('css')
@stop
@section('right_content')
@yizan_begin
<yz:form id="yz_form" action="update">
	@if($data['seller']['type'] == 1)
	<yz:fitem name="$data.seller.name" label="人员名称" type="text"></yz:fitem>
	<yz:fitem name="$data.seller.mobile" label="人员手机" type="text"></yz:fitem>	
	@else
	<yz:fitem name="$data.seller.name" label="机构简称" type="text"></yz:fitem>
	<yz:fitem name="$data.seller.mobile" label="机构手机" type="text"></yz:fitem>
	@endif
	<yz:fitem name="certificates" label="资质认证列表">
		@foreach($data['certificates'] as $img) 
		<a href="{{ $img }}" target="_blank"><img src="{{ formatImage($img,0,100,0) }}" alt="" height="100"></a>
   		@endforeach
	</yz:fitem>
	<yz:fitem name="remark" label="处理备注" type="textarea"></yz:fitem> 
	<yz:fitem name="status" label="认证状态">
		<php>
			if($data['status'] == 0){
				$data['status'] = -1;
			}
		</php>
		<yz:radio name="status" options="-1,1" texts="未通过,通过" checked="$data['status']"></yz:radio>
	</yz:fitem> 
	<yz:fitem type="hidden" name="sellerId"></yz:fitem>
	<yz:fitem name="type" type="hidden" val="{{ $data['seller']['type'] }}"></yz:fitem>
</yz:form>
@yizan_end 
@stop 