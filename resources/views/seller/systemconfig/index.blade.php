@extends('seller._layouts.base')
@stop
@section('content')
	<div class="p20">
		<div class="m-zjgltbg">
			<div class="p10">						
				<p class="f-bhtt f14 clearfix">
					<span class="ml15 fl">系统设置</span>
				</p>
				<p >
					<span class="ml15 fl mt15">如需修改公司信息请联系平台客服人员</span>
				</p>
				<div class="m-quyu1">
					<div class="m-inforct" style="padding-top:78px;width:750px;">  
						@yizan_begin
						<yz:form id="yz_form" action="updatebasic">
							<yz:fitem name="mobile" label="账号" value="{{$data['mobile']}}" type="text" ></yz:fitem>
							<yz:fitem label="密码" type="text">
								<input type="text" value="******">
								<a href="{{ u('SystemConfig/changepwd') }}" class="fr mr15 btn f-bluebtn" style="margin-top:8px;">修改密码</a>
							</yz:fitem>
							<yz:fitem name="name" label="公司名称" value="{{$data['name']}}" type="text" ></yz:fitem>
							<yz:fitem name="district.name" label="小区名称" value="{{$data['district']['name']}}" type="text" ></yz:fitem>
							<yz:fitem name="contacts" label="联系人" value="{{$data['contacts']}}" type="text" ></yz:fitem>
							<yz:fitem name="serviceTel" label="联系电话" value="{{$data['serviceTel']}}" type="text" ></yz:fitem>
							<yz:fitem name="businessLicenceImg" label="营业执照" type="image"></yz:fitem>
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
