@extends('admin._layouts.base')
@section('css')
<style type="text/css">
</style>
@stop
@section('right_content')
	@yizan_begin
		<yz:list> 
		<search >
			<row>
				<item name="name" label="公司名称"></item> 
                <item name="districtName" label="小区名称"></item> 
            </row>
            <row>  
				<yz:fitem name="provinceId" label="所在地区">
					<yz:region name="provinceId" pval="$search_args['provinceId']" cval="$search_args['cityId']" aval="$search_args['areaId']" showtip="1"></yz:region>
				</yz:fitem>
				<item label="状态">
					<yz:select name="isCheck" options="0,1,2,3" texts="全部,拒绝通过,未审核,通过审核" selected="$search_args['isCheck']"></yz:select>
				</item>
				<btn type="search"></btn>
			</row>
		</search>
		<btns>
			<linkbtn label="添加物业公司" url="{{ u('Property/create') }}"></linkbtn>
			<linkbtn label="导出到EXCEL" url="{{ u('Property/export', $search_args) }}"></linkbtn>
		</btns>
		<table >
			<columns> 
				<column code="name" label="公司名称"  ></column>  
				<column label="小区名称" align="center" >
					{{$list_item['district']['name']}}
				</column> 
				<column code="contacts" label="联系人"  ></column>  
				<column code="mobile" label="联系电话" ></column>
				<column label="管理" align="center" width="220">
					<a href="{{u('Property/puserindex', ['sellerId'=>$list_item['id']])}}">业主</a>
					<a href="{{u('Property/articleindex', ['sellerId'=>$list_item['id']])}}">公告</a>
					<a href="{{u('Property/repairindex', ['sellerId'=>$list_item['id']])}}">报修</a>
					<a href="{{u('Property/buildingindex', ['sellerId'=>$list_item['id']])}}">房产</a>
					<!-- <a href="{{u('Property/roomindex', ['sellerId'=>$list_item['id']])}}">房间</a> -->
					<a href="{{u('Property/dooraccess', ['sellerId'=>$list_item['id']])}}">门禁</a>
					<a href="{{u('Property/dooropenlog', ['sellerId'=>$list_item['id'], 'districtId'=>$list_item['district']['id']])}}">门禁记录</a>
				</column>
				<column label="状态" code="status" type="status" width="40"></column>
				<actions> 
					<action label="详情" css="blu">
						<attrs>
							<url>{{ u('Property/edit',['id'=>$list_item['id']]) }}</url>
						</attrs>
					</action>
					<action type="destroy" css="red"></action> 
				</actions>
			</columns>
		</table>
	</yz:list>
	@yizan_end
	<script type="text/javascript">
	$(function(){ 

	})
	</script>
@stop