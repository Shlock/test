@extends('admin._layouts.base')
@section('css')
@stop
@section('right_content')
	@yizan_begin
	<yz:list>
		<search url="index">
			<row>
				<item label="状态">
					<yz:select name="status" options="0,1,2" texts="全部,关闭,开启" selected="$search_args['status']"></yz:select> 
				</item>
				<item name="name" label="商家名"></item>
				<btn type="search"></btn>
			</row>
		</search>
		<btns> 
		</btns>
		<table >
		<columns>
			<column code="id" label="编号" width="40"></column>
			<column code="name" label="商家名" align="left" width="150"></column>
			<column code="goods" label="商品管理" align="center">
				<p>
					<a href="{{ u('Goods/index',['sellerId'=>$list_item['id'],'type'=>1]) }}" style="color:grey;">商品({{$list_item['goodscount']}})</a>&nbsp;&nbsp;
					<a href="{{ u('SellerGoods/index',['sellerId'=>$list_item['id'],'type'=>2]) }}" style="color:grey;">服务({{$list_item['servicecount']}})</a>&nbsp;&nbsp;
					<a href="{{ u('GoodsCate/index',['sellerId'=>$list_item['id']]) }}" style="color:grey;">分类({{$list_item['catecount']}})</a>&nbsp;&nbsp;
					<a href="{{ u('SellerStaff/index',['sellerId'=>$list_item['id']]) }}" style="color:grey;">人员({{$list_item['staffcount']}})</a>
				</p>
			</column> 
			<column code="status" label="状态" width="60" type="status"></column>
			<actions width="60">
				<p><action type="edit" css="blu"></action></p>
				<p><action type="destroy" css="red"></action></p>
			</actions> 
		</columns>  
	</table>
	</yz:list>
	@yizan_end
@stop