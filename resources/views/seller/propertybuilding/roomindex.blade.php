@extends('seller._layouts.base')
@section('css')
@stop
@section('content')
<div>
		<div class="m-zjgltbg">					
			<div class="p10">
				<!-- 房间管理 -->
				<div class="g-fwgl">
					<p class="f-bhtt f14 clearfix">
						<span class="ml15 fl">房间管理</span>
					</p>
				</div>
				<div class="m-tab m-smfw-ser">
					@yizan_begin
						<yz:list>
							<search> 
								<row>
									<item name="build" label="楼栋号">
										<input type="hidden" name="buildId" value="{{$build['id']}}">
										{{$build['name']}}
									</item>  
									<item name="owner" label="业主名称"></item>  
									<item name="mobile" label="联系电话"></item> 
									<btn type="search" css="btn-gray"></btn>
								</row>
							</search>
							<btns>
								<linkbtn label="添加" css="btn-gray">
									<attrs>
										<url>{{ u('PropertyBuilding/roomcreate',['buildId'=>$build['id']]) }}</url>
									</attrs>
								</linkbtn>
								<linkbtn label="CSV导入" css="btn-gray">
									<attrs>
										<url>{{ u('PropertyBuilding/import', ['buildId'=>$build['id']]) }}</url>
									</attrs>
								</linkbtn>
							</btns>
							<table>
								<columns>
									<column code="id" label="编号" width="40"></column>
									<column code="roomNum" label="房间号" width="50"></column>
									<column code="owner" label="业主" ></column>
									<column code="mobile" label="电话" ></column>
									<column code="propertyFee" label="物业费(元/月)" ></column>
									<column code="structureArea" label="建筑面积(平方米)" ></column>
									<column code="roomArea" label="套内面积(平方米)" ></column>
									<actions width="80">
										<action label="编辑" >
											<attrs>
												<url>{{ u('PropertyBuilding/roomedit',['buildId'=>$list_item['build']['id'],'id'=>$list_item['id']]) }}</url>
											</attrs>
										</action>
										<action label="删除"  css="red">
											<attrs>
												<click>$.RemoveItem(this, '{!!u('PropertyBuilding/roomdestroy',['buildId'=>$list_item['build']['id'], 'id'=>$list_item['id']])!!}', '你确定要删除该数据吗？');</click>
											</attrs>
										</action>
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
