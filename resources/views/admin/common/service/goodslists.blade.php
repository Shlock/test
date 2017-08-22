@extends('admin._layouts.base')
@section('css')
@stop
@section('right_content')
	@yizan_begin
		<yz:list>
			@yizan_yield("search")
			<search> 
				<row>
					<input type="hidden" name="sellerId" value="{{$args['sellerId']}}" />
					<item name="name" label="商品名称"></item>
					<item label="分类">
						<select name="cateId" class="sle">
	                        <option value="0">请选择</option>
	                        @foreach($cate as $cate)
	                            <option value="{{ $cate['id'] }}"  @if((int)Input::get('cateId') == $cate['id']) selected @endif>{{ $cate['name'] }}</option>
	                        @endforeach
	                    </select>
					</item> 
					<btn type="search"></btn>
				</row>
			</search>
			@yizan_stop
			@yizan_yield("btn")
			<btns>
				<!--linkbtn label="添加商品" url="{{ u('Goods/create') }}" css="btn-green"></linkbtn -->
				<!-- <linkbtn label="导出到Excel" type="export" url="{{ u('Goods/gexport?'.$excel) }}"></linkbtn> -->
			</btns>
			@yizan_stop
			<table relmodule="SystemGoods">
				<columns>
					<column code="id" label="编号" width="40">
						<p>{{ $list_item['id'] }}</p>
					</column>
					<column code="seller" label="所属商家" >
						<p>{{ $list_item['seller']['name'] }}</p>
					</column>
					<column code="image" label="图片" type="image" width="60" iscut="1"></column>
					<column code="name" label="商品信息" align="left">
						<p>名称：{{ $list_item['name'] }}</p> 
					</column>
					<column code="cate" label="分类">
						{{ $list_item['cate']['name']}}
					</column>
					<column code="price" label="价格" align="left" width="100">
						<p>售　价：{{ $list_item['price'] }}</p>  
					</column>
					<column code="status" type="status" label="状态" width="40"></column>
					<actions width="60">
						<action css="blu" label="编辑">
							<attrs>
								<url>{{ u('Service/goodsEdit',['id'=>$list_item['id'], 'sellerId'=>$list_item['sellerId']]) }}</url>
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
