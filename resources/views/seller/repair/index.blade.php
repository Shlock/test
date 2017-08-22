@extends('seller._layouts.base')
@section('css')
@stop
@section('content')
<php>
	$status = [
		0 => '待处理',
		1 => '进行中',
		2 => '已完成',
	];

</php>
<div>
	<div class="m-zjgltbg">					
		<div class="p10">
			<div class="g-fwgl">
				<p class="f-bhtt f14 clearfix">
					<span class="ml15 fl">报修管理</span>
				</p>
			</div>
			<div class="m-tab m-smfw-ser">
				@yizan_begin
					<yz:list>
						<search url="{{ $searchUrl }}"> 
							<row>
								<item name="name" label="业主名称"></item>  
								<item name="build" label="楼栋号"></item> 
								<item name="roomNum" label="房间号"></item> 
								<btn type="search" css="btn-gray"></btn>
							</row>
						</search>
						<tabs>
                            <navs>
                                <nav label="待处理">
                                    <attrs>
                                        <url>{{ u('Repair/index',['status'=>'0','nav'=>'1']) }}</url>
                                        <css>@if( $nav == 1) on @endif</css>
                                    </attrs>
                                </nav>
                                <nav label="进行中">
                                    <attrs>
                                        <url>{{ u('Repair/index',['status'=>'1','nav'=>'2']) }}</url>
                                        <css>@if( $nav == 2) on @endif</css>
                                    </attrs>
                                </nav>
                                <nav label="已完成">
                                    <attrs>
                                        <url>{{ u('Repair/index',['status'=>'2','nav'=>'3']) }}</url>
                                        <css>@if( $nav == 3) on @endif</css>
                                    </attrs>
                                </nav>
                            </navs>
                        </tabs>
						<table>
							<columns>
								<column code="id" label="编号" width="50"></column>
								<column code="type" label="类型" width="50">
									<p>{{ $list_item['types']['name'] }}</p>
								</column>
								<column code="build" label="楼栋号" width="100">
									<p>{{ $list_item['build']['name'] }}</p>
								</column>
								<column code="roomNum" label="房间号" >
									<p>{{ $list_item['room']['roomNum'] }}</p>
								</column>
								<column code="owner" label="业主" >
									<p>{{ $list_item['room']['owner'] }}</p>
								</column>
								<column code="mobile" label="电话" >
									<p>{{ $list_item['room']['mobile'] }}</p>
								</column>
								<column code="status" label="状态" >
									<p>{{ $status[$list_item['status']] }}</p>
								</column>
								<column code="createTime" label="提交日期" >
									<p>{{ yzday($list_item['createTime']) }}</p>
								</column>
								<actions width="80">
									<action label="查看详情" >
										<attrs>
											<url>{{ u('Repair/edit',['id'=> $list_item['id']]) }}</url>
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
