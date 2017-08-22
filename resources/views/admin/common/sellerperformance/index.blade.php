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
			<linkbtn type="add">
				<attrs>
					<url>{{ u('Service/create',['type'=>$nav]) }}</url>
				</attrs>
			</linkbtn>
			<!-- <btn type="destroy" label="删除"></btn>  -->
		</btns>
		<table >
		<columns>
			<column code="id" label="编号"></column>
			<column code="name" label="商家名" align="left" width="150"></column>
			<column code="goods" label="商品管理" align="left">
				<p>所属服务站：</p>
				<p>常驻地址：</p>
				<p>营业时间：</p>
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