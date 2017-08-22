@extends('seller._layouts.base')
@section('css')
<style type="text/css">
	.loginId,.password{margin-left: 10px;color:red;}
	.licenseImg{color: red;}
	#beginTime,#endTime{width: 96px;}
</style>
@stop
@section('content')
	@yizan_begin
	<yz:form id="yz_form" action="save">
		<div class="pageBox page_1">
            <div class="m-zjgltbg">
                <div class="p10">
                    <p class="f-bhtt f14 clearfix" style="border-bottom: none;">
                        <span class="ml15 fl">{{$title}}</span>
                        <a href="{{ u('Restaurant/index') }}" class="fr mr15 btn f-bluebtn" style="margin-top:8px;">返回</a>
                    </p>
                    <div class="g-szzllst pt10">
						<yz:fitem name="name" label="餐厅名称"></yz:fitem>
						<yz:fitem name="logo" label="餐厅LOGO" type="image"></yz:fitem>
						<yz:fitem name="contacts" label="负责人"></yz:fitem>
						<yz:fitem name="tel" label="联系电话1" append="1">
							<span class="loginId">登录账号</span>
						</yz:fitem>
						<yz:fitem name="mobile" label="联系电话2"></yz:fitem> 
						<yz:fitem name="password" label="密码" append="1">
							@if($args['id'] > 0)
								<span class="password">保留密码为空则不修改原密码</span>
							@endif
						</yz:fitem>
						<yz:fitem label="营业时间" type="text">
                            <yz:select name="beginTimeHour" options="$time['houer']" texts="$time['houer']" selected="$data['beginTimeHour']">
	                        </yz:select>
	                        <yz:select name="beginTimeMinute" options="$time['minute']" texts="$time['minute']" selected="$data['beginTimeMinute']">
	                        </yz:select>
	                        -
	                        <yz:select name="endTimeHour" options="$time['houer']" texts="$time['houer']" selected="$data['endTimeHour']">
	                        </yz:select>
	                        <yz:select name="endTimeMinute" options="$time['minute']" texts="$time['minute']" selected="$data['endTimeMinute']">
	                        </yz:select>
                        </yz:fitem>
						<yz:fitem name="licenseImg" label="营业执照" type="image" append="1">
							<span class="licenseImg">推荐分辨率640*200，最大允许上传2MB，必须上传营业执照、卫生许可证，可选择是否上传身份证</span>
						</yz:fitem>
						<yz:fitem name="license" label="营业执照号"></yz:fitem>
						<yz:fitem name="expired" label="执照有效期" type="date"></yz:fitem>
						<yz:fitem name="address" label="常驻地址"></yz:fitem>
						<yz:fitem label="所属服务站" type="text">
							{{$data['seller']['name']}}服务站
						</yz:fitem>
					</div>
                </div>
            </div>
        </div>
	</yz:form>
	@yizan_end
@stop

@section('js')

@stop


