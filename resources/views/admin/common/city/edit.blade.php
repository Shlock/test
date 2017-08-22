@extends('admin._layouts.base')
@section('css')
@stop
@section('right_content')
	@yizan_begin
		<yz:form id="yz_form" action="save">
			<yz:fitem label="开通城市">
				<yz:Region pname="provinceId" cname="cityId" aname="areaId" showtip="1"></yz:Region>
			</yz:fitem>
			<yz:fitem name="sort" label="排序" val="100" ></yz:fitem>
		</yz:form>
	@yizan_end
@stop
@section('js')

@stop