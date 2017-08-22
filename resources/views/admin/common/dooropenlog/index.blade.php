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
				<yz:fitem name="provinceId" label="所在地区">
					<yz:region name="provinceId" pval="$search_args['provinceId']" cval="$search_args['cityId']" aval="$search_args['areaId']" showtip="1"></yz:region>
				</yz:fitem>
				<item label="状态">
					<yz:select name="status" options="0,1,2,3" texts="全部,拒绝通过,未审核,通过审核" selected="$search_args['status']"></yz:select>
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
				<column label="管理" align="center" width="200">
					<a href="">业主</a>
					<a href="">公告</a>
					<a href="">保修</a>
					<a href="">楼宇</a>
					<a href="">房间</a>
					<a href="">门禁记录</a>
				</column>
				<column label="状态" type="status">
					{{ Lang::get('admin.property.'.$list_item['status']) }}
				</column>
				<actions> 
					<action label="详情" css="blu">
						<attrs>
							<url>{{ u('ForumPosts/detail',['id'=>$list_item['id']]) }}</url>
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