@extends('seller._layouts.base')
@section('css')
<style type="text/css">
	.m-tab table tbody td{padding: 5px 0px;}
</style>
@stop
@section('content')
	<div>
		<div class="m-zjgltbg">					
			<div class="p10">
				<!-- 服务管理 -->
				<div class="g-fwgl">
					<p class="f-bhtt f14 clearfix">
						<span class="ml15 fl">服务管理</span>
					</p>
				</div>
				<!-- 服务表格 -->
				<div class="m-tab m-smfw-ser">
					@yizan_begin
	                    <yz:list>
							<btns>
								<linkbtn label="添加服务" url="{{ u('Goods/quickchoose') }}" css="btn-gray"></linkbtn>
								<!-- <linkbtn label="导出到Excel" type="export" url="{{ u('Goods/export?'.$excel) }}" css="btn-gray"></linkbtn> -->
								<!-- <linkbtn type="destroy" css="btn-gray"></linkbtn> -->
							</btns>
	                    	<search> 
								<row>
									<item name="name" label="服务名称"></item>
									<item label="分类">
										<yz:select name="cateId" options="$cate" textfield="levelname" valuefield="id" attr="style='min-width:160px;width:auto'" selected="$search_args['cateId']">
										</yz:select>
									</item> 
									<btn type="search" css="btn-gray"></btn>
								</row>
							</search>
	                        <table css="goodstable" relmodule="GoodsSeller">
	                            <columns>
	                                <column label="服务名称" align="left" width="200">
	                                	<a href="{{ $list_item['image'] }}" target="_blank" class="goodstable_img fl">
	                                		<img src="{{formatImage($list_item['image'],80,80)}} {{ $list_item['image'] }}" alt="">
	                                	</a>
	                                	<div class="goods_name">{{ $list_item['name'] }}</div>
	                                </column>
	                                <column label="服务分类" width="100">
	                                	{{ $list_item['cate']['id'] ? $cate[$list_item['cate']['id']]['levelrel'] : '--分类不存在--' }}
	                                </column>
	                                <column code="price" label="服务收费" width="100"></column>
	                                <column code="status" label="服务状态" width="80">
	                                	<!-- @if($list_item['status']==1) -->
										正常服务
	                                	<!-- @else -->
										<span style="color:red">禁用服务</span>
	                                	<!-- @endif -->
	                                </column>
	                                <column code="saleStatus" label="上架/下架" type="status" width="70"></column>
	                                <actions width="100">
	                                	@if($list_item['systemGoodsId'] > 0)
										<action label="编辑" click="javascript:;" style="color:#ccc;cursor:default"></action>
										<action label="查看" css="blu">
											<attrs>
												<url>{{ u('Goods/edit',['id'=>$list_item['id']]) }}</url>
												<attr>target="_blank"</attr>
											</attrs>
										</action>
										@else
										<action type="edit" css="blu" target="_blank"></action>
										<action label="查看" click="javascript:;" style="color:#ccc;cursor:default"></action>
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
