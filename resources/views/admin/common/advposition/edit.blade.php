@extends('admin._layouts.base')

@section('right_content')
@yizan_begin
<yz:form id="yz_form" action="update">
	@if($data['id'] > 0)
	<yz:fitem name="code" label="广告位代码" attr="disabled"></yz:fitem>
	@else
	<yz:fitem name="code" label="广告位代码" append="1">
		<yz:checkbox name="isAutoCode" options="1" texts="自动生成"></yz:checkbox>
	</yz:fitem>
	@endif
	
	<yz:fitem name="name" label="广告位名称"></yz:fitem>

	@yizan_yield('client_type')
	<yz:fitem name="clientType" label="类型">
		<yz:select name="clientType" options="buyer,wap" texts="买家APP,微信端" selected="$data['clientType']"></yz:select>
	</yz:fitem>
	@yizan_stop

	<yz:fitem name="width" label="宽度"></yz:fitem>
	<yz:fitem name="height" label="高度"></yz:fitem>

	@yizan_yield('form_fitem')
	<yz:fitem name="brief" label="描述"></yz:fitem>
	<yz:fitem name="style" label="样式" type="textarea"></yz:fitem>
	@yizan_stop
</yz:form>
@yizan_end
@stop