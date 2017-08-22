@extends('seller._layouts.base')
@section('css')
@stop
@section('content')
<div>
	<div class="m-zjgltbg">					
		<div class="p10">
			<div class="g-fwgl">
				<p class="f-bhtt f14 clearfix">
					<span class="ml15 fl">业主身份审核</span>
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
					<table>
						<columns>
							<column code="id" label="编号" width="30"></column>
							<column code="name" label="楼栋号" width="50">
								<p>{{ $list_item['build']['name'] }}</p>
							</column>
							<column code="roomNum" label="房间号" width="50">
								<p>{{ $list_item['room']['roomNum'] }}</p>
							</column>
							<column code="name" label="业主" width="50"></column>
							<column code="mobile" label="电话" width="80"></column>
							<column code="createTime" label="申请时间" width="80">
								<p>{{ yztime($list_item['createTime']) }}</p>
							</column>
							<column code="status" label="状态" width="50">
								<p>@if($list_item['status'] == 1)已通过@elseif($list_item['status'] == 0)待审核@else已拒绝@endif</p>
							</column>
							<actions width="80">
								<action label="查看" >
									<attrs>
										<url>{{ u('PuserApply/edit',['id'=>$list_item['id']]) }}</url>
									</attrs>
								</action>
								<!-- <action label="通过" >
									<attrs>
										<url>{{ u('PuserApply/update',['id'=>$list_item['id'],'status'=>1]) }}</url>
									</attrs>
								</action>
								<action label="拒绝" >
									<attrs>
										<url>{{ u('PuserApply/update',['id'=>$list_item['id'],'status'=>-1]) }}</url>
									</attrs>
								</action> -->
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