@yizan_include('admin.common.sellerperformance.edit')

<!-- @yizan_section('staff_info')
<yz:fitem name="sex" label="性别">
    <yz:radio name="sex" options="1,2" texts="男,女" checked="$data['staff']['sex']" default="1"></yz:radio>
</yz:fitem>
<php>
$data['birthday'] = $data['staff']['birthday'];
</php>
<yz:fitem name="birthday" label="生日" type="dateyear"></yz:fitem>
@yizan_stop

@yizan_section('authenticate')
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
@yizan_stop -->