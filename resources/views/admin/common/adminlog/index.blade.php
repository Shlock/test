@extends('admin._layouts.base')
@section('css') 
@stop
@section('right_content')
	@yizan_begin 
<!-- 列表 -->
		<yz:list>
<!-- 搜索 -->
		<search>  
			<row>
				<item name="beginDate" label="开始时间" type="date"></item>
				<item name="endDate" label="结束时间" type="date"></item>
				<item name="adminId" label="管理员编号"></item>
				<btn type="search"></btn> 
			</row> 
		</search> 
			<btns> 
				<linkbtn label="导出到Excel" type="export" url="{{ u('AdminLog/export?'.$excel) }}"></linkbtn>
				<linkbtn url="clear" label="清除所有日志"></linkbtn> 
			</btns>
			<table>
				<columns>				 
					<column code="api" label="操作模块" width="100"></column> 
					<column code="request" label="操作名" width="50"></column> 
					<column code="admin" label="操作人" width="50">
						<p>{{ $list_item['admin']['name'] }} </p> 
					</column> 
					<column code="ip" label="IP"></column> 
					<column code="status" label="操作结果"></column>   
					<column code="logTime" type="time" label="记录时间"></column>   
					<actions width="70">    
						<action type="destroy" css="red"></action>
					</actions>
				</columns>  
			</table>
		</yz:list> 
	@yizan_end
@stop
@section('js')
@stop