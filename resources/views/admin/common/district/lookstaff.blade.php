@extends('admin._layouts.base')

@section('css')

@section('right_content')
	@yizan_begin
		<yz:list>
			<search>
				<row>
					<item name="name" label="员工姓名"></item>
					<item name="mobile" label="员工电话"></item>
					<item name="districtId" type="hidden"></item>
					<btn type="search"></btn>
				</row>
			</search>
			<table>
				<columns>
					<column code="avatar" label="头像" type="image" width="80" iscut="1"></column>
					<column label="员工信息" align="left">
						<p>名称：{{$list_item['name']}}</p>
						<p>电话：{{$list_item['mobile']}}</p>
						<p>地址：{{$list_item['address']}}</p>
					</column>
					<column code="status" label="状态" type="status" width="50"></column>
					<actions width="100">
						<action label="移除服务人员" css="red" click="$.removeresponsible({{$list_item['id']}})"></action>
					</actions>
				</columns>
			</table>
		</yz:list>
	@yizan_end
@stop

@section('js')
<script type="text/javascript">
	$(function(){
		//添加小区
		$.removeresponsible = function(staffId){
			var districtId = "{{$districtId}}";
			$.post("{{ u('District/removeresponsible') }}",{'districtId':districtId,'staffId':staffId},function(res){
				$.ShowAlert(res.msg);
				if(res.code == 0){
					window.location.reload();
				}
					

			})
		}
	})
</script>
@stop
