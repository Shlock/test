@extends('admin._layouts.base')
@section('css')
<style type="text/css">
	._green{color: green;}
	._red{color: red;}
</style>
@stop
@section('right_content')
	@yizan_begin
		<yz:list>
			<search> 
				<row>
					<item name="name" label="服务名称"></item>
					<item name="sellerName" label="服务人员"></item>
					<item label="分类">
						<yz:select name="cateId" options="$cate" textfield="levelname" valuefield="id" attr="style='min-width:160px;width:auto'" selected="$search_args['cateId']">
						</yz:select>
					</item> 
					<btn type="search"></btn>
				</row>
			</search>
			<btns>
				<!-- <linkbtn label="导出到Excel" type="export" url="{{ u('SellerGoods/export?'.$excel) }}"></linkbtn> -->
			</btns>
			<table relmodule="goods">
				<columns>
					<column code="image" label="图片" type="image" width="60" iscut="1"></column>
					<column code="name" label="服务信息" align="left">
						<p>编号：{{ $list_item['id'] }}</p>
						<p>名称：{{ $list_item['name'] }}</p>
						<p>分类：{{ $list_item['cate']['id'] ? $cate[$list_item['cate']['id']]['levelrel'] : '----' }}</p>
					</column>
					@yizan_yield('sellergoodscommon')
					<column label="服务机构或人员" align="left" width="130">
					@yizan_stop
						<p>名称：{{ $list_item['seller']['name'] }}</p>
						<p>电话：{{ $list_item['seller']['mobile'] }}</p>
					</column>
					<column label="价格类型" width="70">
							{{ Lang::get('admin.PriceType.'.$list_item['priceType']) }}
					</column>
					<column code="price" label="价格/时长" align="left" width="100">
						<p>售　价：{{ $list_item['price'] }}</p>
						<p>市场价：{{ $list_item['marketPrice'] }}</p>
						<!-- @if($list_item['priceType']==1) -->
						<p>时　长：{{ sprintf("%.2f", $list_item['duration'] / 3600) }}小时</p>
						<!-- @else -->
						<p>时　长：----</p>
						<!-- @endif -->
					</column>
					<column label="上下架" width="40">
						<!-- @if( $list_item['saleStatus'] == 0 ) -->
							<i class="fa fa-arrow-down _red" title="下架服务"></i>
						<!-- @else if( $list_item['seleStatus'] == 1 ) -->
							<i class="fa fa-arrow-up _green" title="正常服务"></i>
						<!-- @endif -->
					</column>
					<column code="status" type="status" label="状态" width="40"></column>
					<actions width="90">
						<action css="blu" label="查看">
							<attrs>
								<url>{{ u('SellerGoods/lookat',['id'=>$list_item['id']]) }}</url>
							</attrs>
						</action>
						<!-- <action type="edit" css="blu"></action> -->
						<action type="destroy" css="red"></action>
					</actions>
				</columns>
			</table>
		</yz:list>
	@yizan_end
@stop

@section('js')
@stop
