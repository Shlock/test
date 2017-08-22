@extends('seller._layouts.base')
@section('css')
@stop
@section('content')
	<div>
		<div class="m-zjgltbg">					
			<div class="p10">
				<!-- 人员管理 -->
				<div class="g-fwgl">
					<p class="f-bhtt f14 clearfix">
						<span class="ml15 fl">餐厅管理</span>
					</p>
				</div>
				<!-- 人员表格 -->
				<div class="m-tab m-smfw-ser">
					@yizan_begin
	                    <yz:list>
	                    	<search> 
								<row>
									<item name="name" label="餐厅名称"></item>
									<btn type="search" css="btn-gray"></btn>
								</row>
							</search>
							<btns>
								<linkbtn label="添加餐厅" url="{{ u('Restaurant/create') }}" css="btn-green"></linkbtn>
							</btns>
	                        <table>
                                <columns>
									<column code="mobile" label="餐厅信息" align="left" width="150">
										<p>餐厅名称：{{$list_item['name']}} </p> 
										<p>负责人员：{{$list_item['contacts']}}</p>
										<p>联系电话：{{$list_item['tel']}}</p>
										<p>联系电话：{{$list_item['mobile']}}</p>
									</column>
									<column code="address" label="所在地址" align="left">
										<p>所属服务站：{{$list_item['seller']['name']}}</p>
										<p>常驻地址：{{$list_item['address']}}</p>
										<p>营业时间：{{$list_item['beginTime']}}-{{$list_item['endTime']}}</p>
									</column> 
									<column code="" label="菜单" width="110">
										当前菜肴数量：{{$list_item['num'] or 0}}种
									</column> 
									<column code="status" label="状态" width="60">
										@if($list_item['status'] == 0)
										<span class="red">禁用</span>
										@elseif($list_item['status'] == 1)
										正常
										@endif
									</column>
									<actions width="60">
										<p>
											<action label="菜单管理">
												<attrs>
													<url>{{ u('Restaurant/carte',['restaurantId'=>$list_item['id']]) }}</url>
												</attrs>
											</action>
										</p>
										<p><action type="edit" css="blu"></action></p>
										<p><action type="destroy" css="red"></action></p>
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
