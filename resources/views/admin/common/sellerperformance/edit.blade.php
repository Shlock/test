@extends('admin._layouts.base')
@section('css')
@stop
@section('right_content')
@yizan_begin
<yz:form id="yz_form" action="update">
	<dl class="m-ddl">
		@if( $data['type'] == 2)
			<dt>服务人员信息</dt>
			<dd class="clearfix">
				<yz:fitem name="mobile" label="手机号"></yz:fitem>
				@if($data['id'])
				<yz:fitem name="pwd"  label="密码" type="password" tip="不修改请保留为空"></yz:fitem>
				@else
				<yz:fitem name="pwd"  label="密码" type="password">
					<attrs>
						<btip><![CDATA[如果手机号未注册会员，必须设置密码；<br/>如果手机号已经注册过会员，设置密码将重置会员登录密码；]]></btip>
					</attrs>
				</yz:fitem>
				@endif
				<yz:fitem name="name" label="机构简称"></yz:fitem>
				<yz:fitem name="logo" label="LOGO" type="image"></yz:fitem>
				<yz:fitem name="brief" label="简介" type="textarea"></yz:fitem>
				<yz:fitem name="photos" label="机构相册" type="image">
					<yz:imageList name="photos." images="$data['photos']"></yz:imageList>
				</yz:fitem>
			</dd>
		@else
			<dt>个人信息</dt>
			<dd class="clearfix">
				<yz:fitem name="mobile" label="手机号"></yz:fitem>
				@if($data['id'])
				<yz:fitem name="pwd"  label="密码" type="password" tip="不修改请保留为空"></yz:fitem>
				@else
				<yz:fitem name="pwd"  label="密码" type="password">
					<attrs>
						<btip><![CDATA[如果手机号未注册会员，必须设置密码；<br/>如果手机号已经注册过会员，设置密码将重置会员登录密码；]]></btip>
					</attrs>
				</yz:fitem>
				@endif
				<yz:fitem name="name" label="名称"></yz:fitem>
				<yz:fitem name="logo" label="LOGO" type="image"></yz:fitem>
				<yz:fitem name="sex" label="性别">
				    <yz:radio name="sex" options="1,2" texts="男,女" checked="$data['staff']['sex']" default="1"></yz:radio>
				</yz:fitem>
				<php>
				$data['birthday'] = $data['staff']['birthday'];
				</php>
				<yz:fitem name="birthday" label="生日" type="dateyear"></yz:fitem>
				<yz:fitem name="brief" label="简介" type="textarea"></yz:fitem>
				<yz:fitem name="photos" label="个人相册" type="image">
					<yz:imageList name="photos." images="$data['photos']"></yz:imageList>
				</yz:fitem>
			</dd>
		@endif
		<yz:fitem name="provinceId" label="所在地区">
			<yz:region pname="provinceId" pval="$data['province']['id']" cname="cityId" cval="$data['city']['id']" aname="areaId" aval="$data['area']['id']"></yz:region>
		</yz:fitem>
		<yz:fitem name="mapPos" label="服务范围">
			<yz:mapArea name="mapPos" pointVal="$data['mapPoint']" addressVal="$data['address']" posVal="$data['mapPos']"></yz:mapArea>
		</yz:fitem>
	</dl>
	<dl class="m-ddl">
		@if( $data['type'] == 1)
			<dt>身份认证信息</dt>
			<dd class="clearfix">
				<yz:fitem name="isAuthenticate" label="是否身份认证">
					<yz:radio name="isAuthenticate" options="0,1" texts="否,是" checked="$data['isAuthenticate']" default="0"></yz:radio>
				</yz:fitem> 
				<yz:fitem name="authenticate.realName" label="真实名称"></yz:fitem>
				<yz:fitem name="authenticate.idcardSn" label="身份证编号"></yz:fitem>  
				<yz:fitem name="authenticate.idcardPositiveImg" label="身份证正面" type="image"></yz:fitem>  
				<yz:fitem name="authenticate.idcardNegativeImg" label="身份证背面" type="image"></yz:fitem> 
			</dd>
		@else
			<dt>法人机构身份信息</dt>
			<dd class="clearfix">
				<yz:fitem name="isAuthenticate" label="是否认证机构">
					<yz:radio name="isAuthenticate" options="0,1" texts="否,是" checked="$data['isAuthenticate']" default="0"></yz:radio>
				</yz:fitem> 
				<yz:fitem name="authenticate.realName" label="法人名称"></yz:fitem>
				<yz:fitem name="authenticate.idcardSn" label="身份证编号"></yz:fitem>  
				<yz:fitem name="authenticate.idcardPositiveImg" label="身份证正面" type="image"></yz:fitem>  
				<yz:fitem name="authenticate.idcardNegativeImg" label="身份证背面" type="image"></yz:fitem> 	
				<yz:fitem name="authenticate.companyName" label="机构全称"></yz:fitem> 
				<yz:fitem name="authenticate.businessLicenceSn" label="营业执照号码"></yz:fitem> 
				<yz:fitem name="authenticate.businessLicenceImg" label="营业执照图片" type="image"></yz:fitem> 
			</dd>
		@endif
	</dl>
	<dl class="m-ddl">
		<dt>资质认证信息</dt>
		<dd class="clearfix">
			<yz:fitem name="isCertificate" label="是否资质认证"> 
		        <yz:radio name="isCertificate" options="0,1" texts="否,是" checked="$data['isCertificate']" default="0"></yz:radio>
			</yz:fitem>
			<yz:fitem name="certificate" label="资质认证" type="image">
				<yz:imageList name="certificate.certificates." images="$data['certificate']['certificates']"></yz:imageList>
			</yz:fitem>
		</dd>
	</dl>
	<dl class="m-ddl">
		<dt>其他信息</dt>
		<dd class="clearfix">
			<yz:fitem name="businessStatus" label="接单状态">
				<yz:radio name="businessStatus" options="0,1" texts="拒绝接单,正常接单" checked="$data['businessStatus']" default="1"></yz:radio>
			</yz:fitem>
			<yz:fitem name="businessDesc" label="营业说明" type="textarea"></yz:fitem> 
			<yz:fitem name="status" label="状态">
				<yz:radio name="status" options="0,1" texts="停业,正常" checked="$data['status']" default="1"></yz:radio>
			</yz:fitem>
			<yz:fitem name="sort" label="排序"></yz:fitem>
		</dd>
	</dl>
	@if($data['type'] == 1) 
	<dl class="m-ddl">
		<dt>预约时间管理</dt>
		<dd class="clearfix" style="padding:15px;"> 
			@include('admin.common.sellerperformance.showtime') 
		</dd>
	</dl>
	@endif
	<yz:fitem name="type" type="hidden" val="{{$data['type']}}"></yz:fitem>
</yz:form>
@yizan_end
@stop