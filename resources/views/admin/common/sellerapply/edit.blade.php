@extends('admin._layouts.base')
@section('css')
@stop
@section('right_content')
@yizan_begin
<yz:form id="yz_form" action="update">
	<yz:fitem name="name" label="商家名称" type="text"></yz:fitem>
	<yz:fitem name="mobile" label="联系电话" type="text"></yz:fitem>
	@if($data['type'] == 2)
	<yz:fitem name="contacts" label="法人/店主" type="text"></yz:fitem>
	@endif
	<yz:fitem name="city" label="所在地区" type="text">
        <span>{{$data['province']['name']}}{{$data['city']['name']}}{{$data['area']['name']}}</span>
	</yz:fitem> 
	<yz:fitem name="address" label="详细地址" type="text"></yz:fitem> 
	<yz:fitem name="map" label="服务定位" type="text">
	<php>
		$map = explode(',', $data['mapPointStr']);
		$point[0] = $map[1];
		$point[1] = $map[0];
		$mappoint = implode(',',$point);
	</php>
		<img src="http://st.map.qq.com/api?size=400*260&center={{$mappoint}}&zoom=16" />
	</yz:fitem> 
	<yz:fitem name="logo" label="商家图片">
		<a href="{{ $data['logo'] }}" target="_blank"><img src="{{ formatImage($data['logo'],0,160,0) }}" alt="" height="160"></a>
	</yz:fitem>
    @if($data['type'] == 1)
        <yz:fitem name="contacts" label="真实姓名" type="text"></yz:fitem>
    @endif
	<yz:fitem name="idcardSn" label="身份证编号" type="text">
		<span>{{ $data['authenticate']['idcardSn'] }}</span>
	</yz:fitem> 
	<yz:fitem name="idcardPositiveImg" label="身份证正面">
		<a href="{{ $data['authenticate']['idcardPositiveImg'] }}" target="_blank"><img src="{{ formatImage($data['authenticate']['idcardPositiveImg'],0,160,0) }}" alt="" height="160"></a>
	</yz:fitem>  
	<yz:fitem name="idcardNegativeImg" label="身份证背面">
		<a href="{{ $data['authenticate']['idcardNegativeImg'] }}" target="_blank"><img src="{{ formatImage($data['authenticate']['idcardNegativeImg'],0,160,0) }}" alt="" height="160"></a>
	</yz:fitem>  
	@if($data['type'] == 2)
	<yz:fitem name="businessLicenceImg" label="营业执照">
		<a href="{{ $data['authenticate']['businessLicenceImg'] }}" target="_blank">
			<img src="{{ formatImage($data['authenticate']['businessLicenceImg'],0,160,0) }}" alt="" height="160">
		</a>
	</yz:fitem>  
	@else
	<yz:fitem name="businessLicenceImg" label="资质证书">
		<a href="{{ $data['authenticate']['certificateImg'] }}" target="_blank">
			<img src="{{ formatImage($data['authenticate']['certificateImg'],0,160,0) }}" alt="" height="160">
		</a>
	</yz:fitem>  
	@endif
	<yz:fitem label="佣金比例"> 
          <input type="text" name="deduct" class="u-ipttext" value="{{ $data['deduct'] or '0'}}">
          <span style="color:#000;">%</span> 
	</yz:fitem>
	<yz:fitem name="checkVal" label="审核原因" type="textarea"></yz:fitem>
	<yz:fitem name="isCheck" label="审核状态">
		<php>
			if($data['isCheck'] == 0){
				$data['isCheck'] = 1;
			}
		</php>
		<yz:radio name="isCheck" options="-1,1" texts="拒绝,通过" checked="$data['isCheck']" default="0"></yz:radio>
	</yz:fitem>
</yz:form>
@yizan_end

@stop 


