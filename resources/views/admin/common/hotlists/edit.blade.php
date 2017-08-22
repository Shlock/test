@extends('admin._layouts.base')
@section('css')
@stop
@section('right_content')
@yizan_begin
<yz:form id="yz_form" action="save"> 
	<yz:fitem name="hotwords" label="热搜词"></yz:fitem>
	<yz:fitem name="" label="所在城市">
		<yz:region pname="provinceId" pval="$data['province']['id']" cname="cityId" cval="$data['city']['id']" aname="areaId" aval="$data['area']['id']"></yz:region>
	</yz:fitem>      
	<yz:fitem name="sort" label="排序"></yz:fitem>    
	<yz:fitem label="状态">
		<php> $status = isset($data['status']) ? $data['status'] : 1 </php>
		<yz:radio name="status" options="0,1" texts="关闭,正常"  checked="$status"></yz:radio>
	</yz:fitem> 
</yz:form>
@yizan_end 
@stop 