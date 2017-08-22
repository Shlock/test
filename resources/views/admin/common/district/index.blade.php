@extends('admin._layouts.base')

@section('css')
@section('right_content')
	@yizan_begin
		<yz:list>
			<search> 
				<row>
					<item name="districtName" label="小区名称"></item>  
					<yz:fitem name="provinceId" label="所在地区">
						<yz:region name="provinceId" pval="$search_args['provinceId']" cval="$search_args['cityId']" aval="$search_args['areaId']" showtip="1"></yz:region>
					</yz:fitem>
					<btn type="search"></btn> 
				</row>
			</search>
			<btns>
				<linkbtn label="添加小区" url="{{ u('District/create') }}" css="btn-green"></linkbtn>
			</btns>
			<table> 
				<columns>
					<column code="id" label="编号" width="40"></column> 
					<column code="name" label="小区" ></column> 
					<column label="省/市">
						{{ $list_item['province']['name'] }}
					</column>
					<column label="市/区">
						{{ $list_item['city']['name'] }}
					</column>
					<column code="address" label="详细地址"></column>
					<column code="status" label="物业进驻状态" >
						@if($list_item['sellerId'] > 0)
						是
						@else
						否
						@endif
					</column>
					<actions width="60">
						<action label="编辑" type="edit" css="blu"></action>
						@if($list_item['sellerId'] < 1)
						<action label="删除" type="destroy" css="red"></action> 
						@endif
					</actions>
				</columns>
			</table>
		</yz:list>
	@yizan_end
@stop

@section('js')
@stop
