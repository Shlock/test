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
					<item name="name" label="楼栋号"></item>
					<btn type="search"></btn>
				</row>
			</search>
			@if($sellerId > 0)
			<btns>
				<linkbtn type="add">
					<attrs>
						<url>{{ u('PropertyBuilding/create',['sellerId'=>$sellerId]) }}</url>
					</attrs>
				</linkbtn>
				<linkbtn label="导出到EXCEL" type="export">
					<attrs>
						<url>{{ u('PropertyBuilding/export', ['sellerId'=>$sellerId] ) }}</url>
					</attrs>
				</linkbtn>
			</btns>
			@else 
			<btns>
				<linkbtn label="导出到EXCEL" type="export" url="{{ u('PropertyBuilding/export') }}"></linkbtn>
			</btns>
			@endif
			<table>
				<columns>
					<column code="id" label="编号" width="50"></column>
					<column code="name" label="楼栋号" width="70">
						<p>{{ $list_item['name'] }}</p>
					</column>
					<column code="remark" label="备注" width="200"></column>
					<actions width="80">
						<action label="编辑" >
							<attrs>
								<url>{{ u('PropertyBuilding/edit',['sellerId'=>$list_item['seller']['id'], 'id'=>$list_item['id']]) }}</url>
							</attrs>
						</action>
						<action type="destroy" css="red"></action>
					</actions>
				</columns>
			</table>
		</yz:list>
	@yizan_end
@stop

