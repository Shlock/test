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
						<span class="ml15 fl">物业费管理</span>
					</p>
				</div>
				<div class="m-tab m-smfw-ser">
					@yizan_begin
						<yz:list>
							<search> 
								<row>
									<item name="build" label="楼栋号"></item>  
									<item name="roomNum" label="房间号"></item> 
									<item name="name" label="业主名称"></item>  
									<div class="u-fitem clearfix ">
							            <span class="f-tt">
							                 应缴日期:
							            </span>
							            <div class="f-boxr">
							                  <input type="text" name="beginTime" id="beginTime" class="date u-ipttext" value="{{$args['beginTime']}}">
							            </div>
							            -
							            <div class="f-boxr">
							                  <input type="text" name="endTime" id="endTime" class="date u-ipttext" value="{{$args['endTime']}}">
							            </div>
							        </div>
					            	<item label="缴费状态">
										<yz:select name="status" options="0,1,2" texts="全部,未缴费,已缴费" selected="$args['status']"></yz:select>
									</item>
									<btn type="search" css="btn-gray"></btn>
								</row>
							</search>
							<btns>
								<linkbtn label="添加" css="btn-gray" url="{{ u('PropertyFee/create') }}"></linkbtn>
								<linkbtn label="CSV导入" url="{{ u('PropertyFee/import') }}" css="btn-gray"></linkbtn>
							</btns>
							<table>
								<columns>
									<column code="id" label="编号" width="40"></column>
									<column code="build" label="楼栋号" width="50">
										<p>{{ $list_item['build']['name'] }}</p>
									</column>
									<column code="roomNum" label="房间号" width="50">
										<p>{{ $list_item['room']['roomNum'] }}</p>
									</column>
									<column code="name" label="业主" ></column>
									<column code="payableTime" label="应缴日期" >
										<p>{{ yzday($list_item['payableTime']) }}</p>
									</column>
									<column code="payableFee" label="应缴金额(元)" ></column>
									<column code="payTime" label="实缴日期" >
										<p>{{ yzday($list_item['payTime']) }}</p>
									</column>
									<column code="payFee" label="实缴金额(元)" ></column>
									<actions width="80">
										<action label="编辑" >
											<attrs>
												<url>{{ u('PropertyFee/edit',['id'=>$list_item['id']]) }}</url>
											</attrs>
										</action>
										@if(empty($list_item['payTime']) && empty($list_item['payFee']))
										<action label="缴费" >
											<attrs>
												<url>{{ u('PropertyFee/payfee',['id'=>$list_item['id']]) }}</url>
											</attrs>
										</action>
										@endif
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
<script type="text/javascript">
	$(function() {
		$('.date').datepicker();
	})
</script>
@stop
