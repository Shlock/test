@extends('seller._layouts.base')
@section('css')
@stop

@section('content')
	@yizan_begin
		<yz:form id="yz_form" action="saveGoods">
			<div class="pageBox page_1">
	            <div class="m-zjgltbg">
	                <div class="p10">
	                    <p class="f-bhtt f14 clearfix" style="border-bottom: none;">
	                        <span class="ml15 fl">{{$title}}</span>
	                        <a href="{{ u('Restaurant/carte')}}?restaurantId={{$args['restaurantId']}}" class="fr mr15 btn f-bluebtn" style="margin-top:8px;">返回</a>
	                    </p>
	                    <div class="g-szzllst pt10">
							<yz:fitem name="more.restaurant.name" label="餐厅名称" type="text"></yz:fitem>
							<yz:fitem name="name" label="菜品名称"></yz:fitem>
							<yz:fitem name="image" label="美食图片" type="image"></yz:fitem> 
							<yz:fitem name="oldPrice" label="原价"></yz:fitem> 
							<yz:fitem name="price" label="现价"></yz:fitem> 
							<yz:fitem label="分类">
								<yz:select name="typeId" options="$data['more']['goodstype']" textfield="name" valuefield="id" selected="$data['typeId']">
								</yz:select>
							</yz:fitem>
							<yz:fitem label="参与服务">
								<yz:select name="joinService" options="3,1,2" texts="同时参加,即时送餐,预约午餐 " selected="$data['joinService']">
								</yz:select>
							</yz:fitem>
							<yz:fitem name="sort" label="排序" val="100"></yz:fitem>
							<yz:fitem name="restaurantId" type="hidden" val="{{$args['restaurantId']}}" ></yz:fitem>
						</div>
	                </div>
	            </div>
	        </div>
		</yz:form>
	@yizan_end
@stop

@section('js')
@stop