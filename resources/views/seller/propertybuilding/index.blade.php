@extends('seller._layouts.base')
@section('css')
@stop
@section('content')
<div>
		<div class="m-zjgltbg">					
			<div class="p10">
				<!-- 楼宇管理 -->
				<div class="g-fwgl">
					<p class="f-bhtt f14 clearfix">
						<span class="ml15 fl">楼宇管理</span>
					</p>
				</div>
				<div class="m-tab m-smfw-ser">
					@yizan_begin
						<yz:list>
							<btns>
								<linkbtn label="添加" url="{{ u('PropertyBuilding/create') }}" css="btn-gray"></linkbtn>
							</btns>
							<table>
								<columns>
									<column code="id" label="编号" width="50"></column>
									<column code="name" label="楼栋号" width="100">
										<p>{{ $list_item['name'] }}</p>
									</column>
									<column code="build" label="房间号" width="100">
										<p><a href="{{ u('PropertyBuilding/roomindex',['buildId'=>$list_item['id']])}}">房间号列表</a></p>
									</column>
									<column code="remark" label="备注" ></column>
									<actions width="80">
										<action type="edit" ></action>
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
