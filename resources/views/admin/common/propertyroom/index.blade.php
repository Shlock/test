@extends('admin._layouts.base')

@section('right_content')
	@yizan_begin
		<yz:list>
			<search> 
				<row>
					<item label="物业公司">
						{{$seller['name']}}
					</item>
					<item label="小区名称">
						{{$seller['district']['name']}}
					</item>
					<item name="sellerId" type="hidden"></item>
					<item name="roomNum" label="房间号"></item>
					<btn type="search"></btn>
				</row>
			</search>
			@if($sellerId > 0)
			<btns>
				<linkbtn type="add">
					<attrs>
						<url>{{ u('PropertyRoom/create',['sellerId'=>$sellerId]) }}</url>
					</attrs>
				</linkbtn>
				<linkbtn label="导出到EXCEL" type="export">
					<attrs>
						<url>{{ u('PropertyRoom/export', ['sellerId'=>$sellerId] ) }}</url>
					</attrs>
				</linkbtn>
			</btns>
			@else 
			<btns>
				<linkbtn label="导出到EXCEL" type="export" url="{{ u('PropertyRoom/export') }}"></linkbtn>
			</btns>
			@endif
			<table>
				<columns>
					<column code="id" label="编号" width="30"></column>
					<column code="seller" label="公司名称">
						<p>{{ $list_item['seller']['name'] }}</p>
					</column>
					<column code="district" label="小区名称" width="60">
						<p>{{ $list_item['district']['name'] }}</p>
					</column>
					<column code="name" label="楼栋号" width="50">
						<p>{{ $list_item['build']['name'] }}</p>
					</column>
					<column code="roomNum" label="房间号" width="50"></column>
					<column code="owner" label="业主" width="50"></column>
					<column code="mobile" label="电话" width="80"></column>
					<column code="remark" label="备注" width="120"></column>
					<actions width="80">
						<action label="编辑" >
							<attrs>
								<url>{{ u('PropertyRoom/edit',['sellerId'=>$list_item['seller']['id'], 'id'=>$list_item['id']]) }}</url>
							</attrs>
						</action>
						<action type="destroy" css="red"></action>
					</actions>
				</columns>
			</table>
		</yz:list>
	@yizan_end
@stop

