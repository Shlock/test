@extends('admin._layouts.base')

@section('css')
@section('right_content')
	@yizan_begin
		<yz:form id="yz_form" action="save">
			<yz:fitem name="provinceId" label="所属地区">
			<yz:region pname="provinceId" pval="$data['provinceId']" cname="cityId" cval="$data['cityId']" aname="areaId" aval="$data['areaId']"></yz:region>
			</yz:fitem>
			<yz:fitem name="row" label="街道名称"></yz:fitem>
			<yz:fitem name="name" label="小区名称"></yz:fitem>
			<yz:fitem name="mapPos" label="地理位置">
                <yz:mapArea name="mapPos" pointVal="$data['mapPointStr']" addressVal="$data['address']" posVal="$data['mapPosStr']"></yz:mapArea>
            </yz:fitem>
		</yz:form>
	@yizan_end
@stop

@section('js')

@stop
