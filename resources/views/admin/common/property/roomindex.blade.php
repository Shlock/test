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
					<item name="buildId" type="hidden"></item>
					<item name="roomNum" label="房间号"></item>
					<btn type="search"></btn>
				</row>
			</search>
			@if($sellerId > 0)
			<btns>
				<linkbtn type="add">
					<attrs>
						<url>{{ u('Property/roomcreate',['sellerId'=>$sellerId, 'buildId'=>$args['buildId']]) }}</url>
					</attrs>
				</linkbtn>
				<!-- <linkbtn label="导出到EXCEL" type="export">
					<attrs>
						<url>{{ u('Property/roomexport', ['sellerId'=>$sellerId, 'buildId'=>$args['buildId']] ) }}</url>
					</attrs>
				</linkbtn> -->
			</btns>
			@else 
			<!-- <btns>
				<linkbtn label="导出到EXCEL" type="export" url="{{ u('Property/roomexport') }}"></linkbtn>
			</btns> -->
			@endif
			<table>
				<columns>
					<column code="id" label="编号" width="30"></column>
					<column code="name" label="楼栋号" width="50">
						<p>{{ $list_item['build']['name'] }}</p>
					</column>
					<column code="roomNum" label="房间号" width="50"></column>
					<column code="owner" label="业主" width="50"></column>
					<column code="mobile" label="电话" width="80"></column>
					<column code="propertyFee" label="物业费(元/月)" ></column>
					<column code="structureArea" label="建筑面积(平方米)" ></column>
					<column code="roomArea" label="套内面积(平方米)" ></column>
					<actions width="80">
						<action label="编辑" >
							<attrs>
								<url>{{ u('Property/roomedit',['sellerId'=>$list_item['seller']['id'], 'id'=>$list_item['id'], 'buildId'=>$args['buildId']]) }}</url>
							</attrs>
						</action>
						<action label="删除" css="red">
							<attrs>
								<click>$.RemoveItem(this, '{!!u('Property/roomdestroy',['sellerId'=>$list_item['seller']['id'], 'id'=>$list_item['id'], 'buildId'=>$args['buildId']])!!}', '你确定要删除该数据吗？');</click>
							</attrs>
						</action>
					</actions>
				</columns>
			</table>
		</yz:list>
	@yizan_end
@stop

