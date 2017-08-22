@extends('admin._layouts.base')
@section('css')
<style type="text/css">
	.ts{text-align: center;color: #999}
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
			<table>
				<columns>
					<column code="image" label="图片" type="image" width="80"></column>
					<column code="name" label="服务名称" align="left" width="150"></column>
					<column label="服务机构或人员" align="left" width="150">
						<p>编号：{{ $list_item['seller']['id'] }}</p>
						<p>名称：{{ $list_item['seller']['name'] }}</p>
						<p>电话：{{ $list_item['seller']['mobile'] }}</p>
					</column>
					<column label="分类">
						{{$list_item['cate']['name'] or '----'}}
					</column>
					<column label="服务时长" width="60">
						<!-- @if($list_item['priceType'] == 1) -->
							{{ sprintf("%.2f", $list_item['duration'] / 3600 ) }}小时
						<!-- @elseif($list_item['priceType'] == 2) -->
							按小时收费
						<!-- @endif -->
					</column>
					<column code="price" label="价格"></column>
					<column code="marketPrice" label="市场价"></column>
					<actions width="40">
						<action label="详情" css="blu">
							<attrs>
								<url>{{ u('GoodsAudit/detail',['id'=>$list_item['id']]) }}</url>
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
