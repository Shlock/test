@extends('admin._layouts.base')
@section('css')

@stop
@section('right_content')
	@yizan_begin
		<yz:form id="yz_form" action="save">
        <dl class="m-ddl">
            @yizan_yield('title')
            <dt>员工信息</dt>
            @yizan_stop
            <dd class="clearfix">
			<yz:fitem name="sellerId" type="hidden" val="{{ $seller['id'] }}"></yz:fitem>
            <yz:fitem name="sellerName" label="所属商家" type="text" val="{{ $seller['name'] }}"></yz:fitem>
			<yz:fitem name="mobile" label="手机号" attr="maxlength=11"></yz:fitem>
			@if($data)
			<yz:fitem name="pwd"  label="密码" type="password" tip="不修改请保留为空"></yz:fitem> 
			@else
			<yz:fitem name="pwd"  label="密码" type="password">
				<attrs>
					<btip><![CDATA[如果手机号未注册会员，必须设置密码；<br/>如果手机号已经注册过会员，设置密码将重置会员登录密码；]]></btip>
				</attrs>
			</yz:fitem>
			@endif
			<yz:fitem name="name" label="名称"></yz:fitem>
            @if($seller['type'] != 1)
            <yz:fitem name="type" label="类型">
                <yz:radio name="type" options="1,2,3" texts="配送人员,服务人员,配送和服务人员" checked="$data['type']"></yz:radio>
            </yz:fitem>
            @endif
			<yz:fitem name="avatar" label="头像" type="image"></yz:fitem>
            <yz:fitem name="sex" label="性别">
                <yz:radio name="sex" options="1,2" texts="男,女" checked="$data['sex']" default="1"></yz:radio>
            </yz:fitem>
			<yz:fitem name="provinceId" label="所在地区">
				<yz:region pname="provinceId" pval="$data['province']['id']" cname="cityId" cval="$data['city']['id']" aname="areaId" aval="$data['area']['id']"></yz:region>
			</yz:fitem>
            <yz:fitem name="mapPos" label="服务范围">
                <yz:mapArea name="mapPos" pointVal="$data['mapPoint']" addressVal="$data['address']" posVal="$data['mapPos']"></yz:mapArea>
            </yz:fitem>
			<yz:fitem name="authentication" label="证书号码"></yz:fitem>
            <yz:fitem name="authenticateImg" label="证书图片" type="image"></yz:fitem>
			<yz:fitem name="status" label="状态">
                <yz:radio name="status" options="1,0" texts="正常,锁定" checked="$data['status']" default="1"></yz:radio>
            </yz:fitem>
            </dd>
        </dl>
		</yz:form>


	@yizan_end
@stop
@section('js')
<script type="text/javascript">
    $(function(){

        $("#birthday").change(function(){
            $.post("{{ u('Staff/get_zodiac_sign') }}",{'time':$(this).val()},function(res){
                $("#constellation2").text(res);
                $("#constellation").val(res);
            });
        });
        
    });
</script>
@stop
