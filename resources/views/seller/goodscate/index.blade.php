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
						<span class="ml15 fl">分类管理</span>
					</p>
				</div>
				<!-- 服务表格 -->
				<div class="m-tab m-smfw-ser">
					@yizan_begin
	                    <yz:list>
							<btns>
								<linkbtn label="添加分类" url="{{ u('GoodsCate/create') }}" css="btn-gray"></linkbtn>
								<!-- <linkbtn label="导出到Excel" type="export" url="{{ u('Goods/export?'.$excel) }}" css="btn-gray"></linkbtn> -->
								<!-- <linkbtn type="destroy" css="btn-gray"></linkbtn> -->
							</btns> 
							<table pager="no" css="goodstable" relmodule="GoodsSeller">
								<columns>
									<column code="id" label="分类编号" align="center"  ></column> 
									<column code="name" label="分类名称" align="center" css="name"></column> 
									<column code="tradeId" label="商家分类" css="sort" align="center">
										{{ $list_item['cates']['name'] }}
									</column>
									<!-- <column code="img" label="图标">
										<img src="{{$list_item['img']}}" style="max-width:32px;"/>
									</column>  -->
									<column code="sort" label="排序" css="sort"></column> 
									<column code="status" label="状态" type="status"></column>
									<actions> 
										<action type="edit" css="blu"></action>
										<!-- @if( !in_array($list_item['id'],$pids[0]) ) -->
										<action type="destroy" css="red"></action>
										<!-- @else -->
										<action type="destroy" click="javascript:;" style="color:#ccc;cursor:default"></action>
										<!-- @endif -->
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
 