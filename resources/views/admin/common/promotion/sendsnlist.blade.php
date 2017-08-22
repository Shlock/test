@extends('admin._layouts.base')
@section('css')
<style type="text/css">
</style>
@stop

@section('right_content')
	@yizan_begin
		<yz:list>
			<search> 
				<row>
					<item name="title" label="分类名称" ></item> 
					<item name="sellerName" label="服务人员" ></item> 
					<item name="sellerMobile" label="服务人员手机" ></item> 
					<item name="beginTime" label="开始时间" type="date"></item>
					<item name="endTime" label="结束时间" type="date"></item>  
					<btn type="search"></btn>
				</row>
			</search>
			<btns>
				<linkbtn label="添加优惠券" url="{{ u('Promotion/create') }}"></linkbtn>
			</btns>
			<table>
				<columns>
					<column code="id" label="编号"></column> 
					<column code="sn" label="SN"></column>
					<column label="服务信息" align="left"></column>
					<column label="过期天数"></column>
					<column label="优惠券" align="left"></column> 
					<column code="status" label="状态" type="status"></column>
					<actions> 
						<action type="destroy"></action>
					</actions>
				</columns>
			</table>
		</yz:list>
	@yizan_end
@stop

@section('js')
<script type="text/javascript">
	$(function(){
		$('#cate_id').prepend("<option value='0' selected>全部分类</option>");
	});
</script>
@stop

