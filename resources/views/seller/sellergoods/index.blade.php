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
				<!-- 商品管理 -->
				<div class="g-fwgl">
					<p class="f-bhtt f14 clearfix">
						<span class="ml15 fl">商品管理</span>
					</p>
				</div> 
				<div class="m-tab m-smfw-ser">
					@yizan_begin
	                    <yz:list>
							<btns>
								<linkbtn label="添加商品" url="{{ u('SellerGoods/create') }}" css="btn-gray"></linkbtn>
								<!-- <linkbtn label="导出到Excel" type="export" url="{{ u('Goods/export?'.$excel) }}" css="btn-gray"></linkbtn> -->
								<!-- <linkbtn type="destroy" css="btn-gray"></linkbtn> -->
							</btns>
	                    	<search> 
								<row>
									<item name="name" label="商品名称"></item>
									<item label="分类">
					                    <select name="cateId" class="sle">
					                        <option value="0">全部</option>
					                        @foreach($cate as $val)
					                            <option value="{{ $val['id'] }}"  @if($search_args['cateId'] == $val['id']) selected @endif>{{ $val['name'] }}</option>
					                        @endforeach
					                    </select>
					                </item>
									<btn type="search" css="btn-gray"></btn>
								</row>
							</search>
	                        <table css="goodstable" relmodule="">
	                            <columns>
	                                <column label="商品名称" align="left" width="200">
	                                	<a href="{{ $list_item['image'] }}" target="_blank" class="goodstable_img fl">
	                                		<img src="{{ $list_item['image'] }}" alt="" width="70">
	                                	</a>
	                                	<div class="goods_name">{{ $list_item['name'] }}</div>
	                                </column>
	                                <column label="商品分类" width="100">
	                                	{{ $list_item['cate']['name'] }}
	                                </column>
	                                <column code="price" label="价格" width="50"></column>  
	                                <column code="status" label="上架/下架" type="status" width="60"></column>
	                                <actions width="100"> 
										<action type="edit" css="blu"></action> 
										<action type="destroy" css="red" ></action> 
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
