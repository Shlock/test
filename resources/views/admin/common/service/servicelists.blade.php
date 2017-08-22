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
						<yz:select name="cateId" options="$cate" textfield="name" valuefield="id" attr="style='min-width:160px;width:auto'" selected="$search_args['cateId']">
						</yz:select>
					</item> 
					<btn type="search"></btn>
				</row>
			</search>
			<btns>
				<!--linkbtn label="添加服务" url="{{ u('Goods/create') }}" css="btn-green"></linkbtn 
				<linkbtn label="导出到Excel" type="export" url="{{ u('SellerGoods/export?'.$excel) }}"></linkbtn>-->
			</btns>
			<table relmodule="goods">
				<columns>
					<column code="id" label="编号" width="40">
						<p>{{ $list_item['id'] }}</p>
					</column>
					<column code="seller" label="所属商家" width="100">
						<p>{{ $list_item['seller']['name'] }}</p>
					</column>
					<column code="image" label="图片" type="image" width="60" iscut="1"></column>
					<column code="name" label="服务信息" align="left">
						<p>名称：{{ $list_item['name'] }}</p> 
					</column>
					<column code="cate" label="分类">
						{{ $list_item['cate']['name']}}
					</column> 
					<column code="price" label="价格/时长" align="left" width="100">
						<p>售　价：{{ $list_item['price'] }}</p>
						<p>时　长：{{ (int)$list_item['duration'] }} @if((int)$list_item['unit'] == 0) 分钟 @else 小时 @endif</p> 
					</column>
					<column label="上下架" width="60">
						<!-- @if( $list_item['saleStatus'] == 0 ) -->
							<i class="fa fa-arrow-down _red" title="下架服务"></i>
						<!-- @else if( $list_item['seleStatus'] == 1 ) -->
							<i class="fa fa-arrow-up _green" title="正常服务"></i>
						<!-- @endif -->
					</column>
					<column code="status" type="status" label="状态" width="40"></column>
					<actions width="90">
						<action css="blu" label="编辑">
							<attrs>
								<url>{{ u('Service/serviceEdit',['id'=>$list_item['id'], 'sellerId'=>$list_item['sellerId']]) }}</url>
							</attrs>
						</action>
						<!-- <action type="edit" css="blu"></action> -->
						<action type="destroy" css="red">
							<attrs>
								<url>{{ u('Service/destroy',['id'=>$list_item['id'], 'sellerId'=>$list_item['sellerId'], 'type'=>$list_item['type']]) }}</url>
							</attrs>
						</action>
					</actions>
				</columns>
			</table>
		</yz:list>
	@yizan_end
@stop

@section('js')
@stop
