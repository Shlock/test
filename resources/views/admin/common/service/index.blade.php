@extends('admin._layouts.base')
@section('css')
@stop
@section('right_content')
	@yizan_begin
	<yz:list>
		<search url="index">
			<row>

				<item name="name" label="商家名"></item>
                <item name="mobile" label="联系电话"></item>
                <item label="状态">
                    <yz:select name="status" options="0,1,2" texts="全部,关闭,开启" selected="$search_args['status']"></yz:select>
                </item>
                <item label="商家分类">
                    <select name="cateId" class="sle">
                        <option value="0">请选择</option>
                        @foreach($cateIds as $cate)
                            <option value="{{ $cate['id'] }}"  @if((int)Input::get('cateId') == $cate['id']) selected @endif>{{ $cate['name'] }}</option>
                        @endforeach
                    </select>
                </item>
            </row>
            <row>
				<yz:fitem name="provinceId" label="所在地区">
					<yz:region name="provinceId" pval="$search_args['provinceId']" cval="$search_args['cityId']" aval="$search_args['areaId']" showtip="1"></yz:region>
				</yz:fitem>
				<btn type="search"></btn>
			</row>
		</search>
		<btns> 
			<linkbtn type="add" url="create"></linkbtn>
			<linkbtn label="导出到EXCEL" type="export" url="{{ u('Service/export', $count) }}"></linkbtn>
		</btns>
		<table >
		<columns>
			<column code="id" label="编号" width="40"></column>
			<column code="name" label="商家名" align="left" width="150"></column>
			<column code="goods" label="商品管理" align="center">
				<p>
					<a href="{{ u('Service/goodslists',['sellerId'=>$list_item['id']]) }}" style="color:grey;">商品({{$list_item['goodscount']}})</a>&nbsp;&nbsp;
					<a href="{{ u('Service/servicelists',['sellerId'=>$list_item['id']]) }}" style="color:grey;">服务({{$list_item['servicecount']}})</a>&nbsp;&nbsp;
					<a href="{{ u('Staff/index',['sellerId'=>$list_item['id']]) }}" style="color:grey;">人员({{$list_item['staffcount']}})</a>
				</p>
				<p>
					<a href="{{ u('Service/catelists',['sellerId'=>$list_item['id'], 'type'=>1]) }}" style="color:grey;">商品分类({{$list_item['goodscatecount']}})</a>&nbsp;&nbsp;
					<a href="{{ u('Service/catelists',['sellerId'=>$list_item['id'], 'type'=>2]) }}" style="color:grey;">服务分类({{$list_item['servicecatecount']}})</a>&nbsp;&nbsp;
				</p>
			</column> 
			<column code="city" label="城市" width="120">
				<p>{{$list_item['province']['name']}}{{$list_item['city']['name']}}</p>
			</column>
			<column code="mobile" label="联系电话" width="80"></column>
			<!-- <column code="status" label="状态" width="40">
				@if($list_item['status'] == 1)
                    <i title="点击停用" class="fa fa-check text-success table-status table-status1" status="0" field="status"> </i>
                @else
                    <i title="点击启用" class="fa table-status fa-lock table-status0" status="1" field="status"> </i>
                @endif
			</column> -->
			<column code="status" label="状态" width="40" type="status"></column>
			<actions width="60">
				<p><action type="edit" css="blu"></action></p>
				<p><action type="destroy" css="red"></action></p>
			</actions> 
		</columns>  
	</table>
	</yz:list>
	@yizan_end
@stop