@extends('seller._layouts.base')
@section('css')
@stop
@section('content')
<?php
$disposeStatus = [
	['id'=>0, 'name' => '全部状态'],
	['id'=>3, 'name' => '通过'],
	['id'=>1, 'name' => '驳回'],
	['id'=>2, 'name' => '等待审核']
];
// dd($list);
?>
	<div>
		<div class="m-zjgltbg">					
			<div class="p10">
				<!-- 人员管理 -->
				<div class="g-fwgl">
					<p class="f-bhtt f14 clearfix">
						<span class="ml15 fl">餐厅审核</span>
					</p>
					<div class="m-bgct" style="margin-top:0;">
						<table id="statusBox">
							<tr>
								<td width="25%" @if($args['disposeStatus']==0) class="on" @endif>
									<a href="{{ u('RestaurantAudit/index',['disposeStatus'=>0]) }}">全部状态</a>
								</td>
								<td width="25%" @if($args['disposeStatus']==3) class="on" @endif>
									<a href="{{ u('RestaurantAudit/index',['disposeStatus'=>3]) }}">通过</a>
								</td>
								<td width="25%" @if($args['disposeStatus']==1) class="on" @endif>
									<a href="{{ u('RestaurantAudit/index',['disposeStatus'=>1]) }}">驳回</a>
								</td>
								<td width="25%" @if($args['disposeStatus']==2) class="on" @endif>
									<a href="{{ u('RestaurantAudit/index',['disposeStatus'=>2]) }}">等待审核</a>
								</td>
							</tr>
						</table>
					</div>
				</div>
				<!-- 人员表格 -->
				<div class="m-tab m-smfw-ser">
					@yizan_begin
	                    <yz:list>
	                    	<search> 
								<row>
									<item name="name" label="餐厅名称"></item>
                           			<item label="审核状态">
										<yz:select name="disposeStatus" options="$disposeStatus" textfield="name" valuefield="id" selected="$search_args['disposeStatus']">
										</yz:select>
									</item>
									<btn type="search" css="btn-gray"></btn>
								</row>
							</search>
                        <table css="goodstable">
                             <columns>
                                 <column label="餐厅信息" width="60" align="left">
                                 	<p>负责人：{{$list_item['contacts']}}</p>
                                 	<p>餐厅名称：{{$list_item['name']}}</p>
                                 	<p>联系电话1：{{$list_item['tel']}}</p>
                                 	<p>联系电话2：{{$list_item['mobile']}}</p>
                                 </column>
                                 <column label="所在地址" width="60" align="left">
                                 	<p>所在服务站：{{$list_item['contacts']}}</p>
                                 	<p>常驻地址：{{$list_item['address']}}</p>
                                 	<p>营业时间：{{$list_item['beginTime']}}-{{$list_item['endTime']}}</p>
                                 </column>
                                 <column label="申请时间" width="20">
                                 	<p>{{yztime($list_item['createTime'])}}</p>
                                 </column>
                                 <column label="状态" width="20">
                                 	@if($list_item['disposeStatus']==-1)
													驳回
                                 	@elseif($list_item['disposeStatus']==0)
													等待审核
                                 	@elseif($list_item['disposeStatus']==1)
													通过
                                 	@endif
                                 	<p></p>
                                 </column>
                                 <actions width="10">
                                 	@if($list_item['disposeStatus']==-1)
                                     	<action label="重新编辑" type="edit" css="blu"></action>
                                    @else
                                     	<p>
					        							<action label="查看" css="blu">
					        								<attrs>
																<url>{{ u('RestaurantAudit/check',['id'=>$list_item['id']]) }}</url>
															</attrs>
					        							</action>
					        						</p>
                                     @endif
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
