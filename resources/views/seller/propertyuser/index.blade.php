@extends('seller._layouts.base')
@section('css')
@stop
@section('content')
<div>
	<div class="m-zjgltbg">					
		<div class="p10">
		<div class="g-fwgl">
			<p class="f-bhtt f14 clearfix">
				<span class="ml15 fl">业主管理</span>
			</p>
		</div>
		<div class="m-tab m-smfw-ser">
			@yizan_begin
			<yz:list>
				<search> 
					<row>
						<item name="name" label="业主名称"></item>  
						<item name="build" label="楼栋号"></item> 
						<br>
						<item name="roomNum" label="房间号"></item> 
						<item name="mobile" label="联系电话"></item> 
						<btn type="search" css="btn-gray"></btn>
					</row>
				</search>
				<btns>
					<linkbtn label="导出到EXCEL" type="export" url="{{ u('PropertyUser/export') }}" css="btn-gray"></linkbtn>
				</btns>
				<table>
					<columns>
						<column code="id" label="编号" width="30"></column>
						<column code="name" label="业主" width="50"></column>
						<column code="build" label="楼栋号" width="50">
							<p>{{ $list_item['build']['name'] }}</p>
						</column>
						<column code="roomNum" label="房间号" width="50">
							<p>{{ $list_item['room']['roomNum'] }}</p>
						</column>
						<column code="fee" label="物业费(元/月)" width="80">
							<p>{{ $list_item['room']['propertyFee'] }}</p>
						</column>
						<column code="mobile" label="电话" width="80"></column>
						<!-- <column code="accessStatus" label="是否申请门禁" width="80">
							@if($list_item['accessStatus'] == 1)
							<p>是</p>
							@else
							<p>否</p>
							@endif
						</column> -->
						<column code="showStatus" label="状态" width="50">
							@if($list_item['showStatus'] == 1)
			                    <i title="停用" class="fa fa-check text-success table-status table-status1" status="0" field="showStatus"> </i>
			                @else
			                    <i title="启用" class="fa table-status fa-lock table-status0" status="1" field="showStatus"> </i>
			                @endif
						</column>
						<actions width="80">
							<action label="门禁" >
								<attrs>
									<url>{{ u('PropertyUser/check',['puserId'=>$list_item['id']]) }}</url>
								</attrs>
							</action>
							<action type="destroy" css="red"></action>
						</actions>
					</columns>
				</table>
			</yz:list>
			@yizan_end
		</div>
		</div>
	</div>
</div>
@stop

@section('js')

@stop