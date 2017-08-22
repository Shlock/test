@extends('admin._layouts.base')
@section('css')
@stop
@section('right_content')
	@yizan_begin
		<yz:list>
			@yizan_yield("search")
			<search> 
				<row>
					<item name="name" label="服务名称"></item>
					<item label="分类">
						<yz:select name="cateId" options="$cate" textfield="levelname" valuefield="id" attr="style='min-width:160px;width:auto'" selected="$search_args['cateId']">
						</yz:select>
					</item> 
					<btn type="search"></btn>
				</row>
			</search>
			@yizan_stop
			@yizan_yield("btn")
			<btns>
				<linkbtn label="添加服务" url="{{ u('Goods/create') }}" css="btn-green"></linkbtn>
				<linkbtn label="导出到Excel" type="export" url="{{ u('Goods/export?'.$excel) }}"></linkbtn>
			</btns>
			@yizan_stop
			<table relmodule="SystemGoods">
				<columns>
					<column code="image" label="图片" type="image" width="60" iscut="1"></column>
					<column code="name" label="服务信息" align="left">
						<p>编号：{{ $list_item['id'] }}</p>
						<p>名称：{{ $list_item['name'] }}</p>
						<p>分类：{{ $list_item['cate']['id'] ? $cate[$list_item['cate']['id']]['levelrel'] : '----'}}</p>
					</column>
					<column code="priceType" label="价格类型">
						{{ Lang::get('admin.PriceType.'.$list_item['priceType']) }}
					</column>
					<column code="price" label="价格/时长" align="left" width="100">
						<p>售　价：{{ $list_item['price'] }}</p>
						<p>市场价：{{ $list_item['marketPrice'] }}</p>
						<!-- @if($list_item['priceType']==1) -->
						<p>时　长：{{ sprintf("%.2f", $list_item['duration'] / 3600 ) }}小时</p>
						<!-- @else -->
						<p>时　长：----</p>
						<!-- @endif -->
					</column>
					<column code="status" type="status" label="状态" width="40"></column>
					<actions width="60">
						<action type="edit" css="blu"></action>
						<action type="destroy" css="red"></action>
						<!-- <action label="预览" target="_blank">
							<attrs>
								<url>http://wap.vso2o.jikesoft.com/Goods/detail?goodsId={{ $list_item['id'] }}</url>
							</attrs>
						</action> -->
					</actions>
				</columns>
			</table>
		</yz:list>
	@yizan_end
@stop

@section('js')
@stop
