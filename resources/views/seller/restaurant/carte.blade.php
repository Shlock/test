@extends('seller._layouts.base')
@section('css')
<style type="text/css">
	.green{color: green;}
</style>
@stop
@section('content')
<?php 
$status = [
	['id'=>0, 'name'=>'全部状态'],
	['id'=>1, 'name'=>'下架'],
	['id'=>2, 'name'=>'上架']
];
 ?>
	<div>
		<div class="m-zjgltbg">					
			<div class="p10">
				<!-- 人员管理 -->
				<div class="g-fwgl">
					<p class="f-bhtt f14 clearfix">
						<span class="ml15 fl">餐厅管理</span>
						<span class="f20 fr mr15">餐厅名称：{{$restaurant['name']}}</span>
					</p>
				</div>
				<!-- 人员表格 -->
				<div class="m-tab m-smfw-ser">
					@yizan_begin
	                    <yz:list>
	                    	<search url="carte?restaurantId={{$restaurant['id']}}"> 
								<row>
									<item name="name" label="美食名称"></item>
									<item label="服务状态">
										<yz:select name="status" options="$status" textfield="name" valuefield="id" selected="$search_args['status']">
										</yz:select>
									</item> 
									<btn type="search" css="btn-gray" url="{{ u('Restaurant/carte') }}"></btn>
								</row>
							</search>
							<btns>

								<linkbtn label="添加美食" url="{{ u('Restaurant/createGoods') }}?restaurantId={{$restaurant['id']}}" css="btn-green"></linkbtn>
							</btns>
	                        <table>
                                <columns>
									<column code="name" label="美食名称" width="150"></column>
									<column code="image" label="美食图片" type="image" width="60"></column> 
									<column label="售价">
										<p>原价：{{$list_item['oldPrice']}}</p>
										<p>现价：{{$list_item['price']}}</p>
									</column> 
									<column code="type.name" label="分类"></column> 
									<column code="joinService" label="参与服务" width="80">
										<!-- @if($list_item['joinService'] == 1) -->
											<yz:checkbox name="joinService" options="1,2" texts="即时送餐,预约午餐" checked="1"></yz:checkbox>
										<!-- @elseif($list_item['joinService'] == 2) -->
											<yz:checkbox name="joinService" options="1,2" texts="即时送餐,预约午餐" checked="2"></yz:checkbox>
										<!-- @elseif($list_item['joinService'] == 3) -->
											<yz:checkbox name="joinService" options="1,2" texts="即时送餐,预约午餐" checked="1,2"></yz:checkbox>
										<!-- @else -->
											<yz:checkbox name="joinService" options="1,2" texts="即时送餐,预约午餐" checked=""></yz:checkbox>
										<!-- @endif -->
									</column> 
									<column label="状态" width="60">
										<!-- @if($list_item['disposeStatus'] == 1) -->
											<!-- @if($list_item['status'] == 0) -->
												<span class="red">下架</span>
											<!-- @elseif($list_item['status'] == 1) -->
												<span class="green">上架</span>
											<!-- @endif -->
										<!-- @elseif($list_item['disposeStatus'] == -1) -->
											审核未通过
										<!-- @elseif($list_item['disposeStatus'] == 0) -->
											等待审核
										<!-- @endif -->
									</column>
									<actions width="60">
										<!-- 审核通过 可上下架 -->
										<!-- @if($list_item['disposeStatus'] == 1) -->
											<p>
												<!-- @if($list_item['status'] == 0) -->
												<action label="上架" click="$.goodsUpDown({{$list_item['id']}}, 1)"></action>
												<!-- @elseif($list_item['status'] == 1) -->
												<action label="下架" click="$.goodsUpDown({{$list_item['id']}}, 0)"></action>
												<!-- @endif -->
											</p>
										<!-- @endif -->

										<!-- 审核通过、未通过 处于下架时才有编辑功能 -->
										<!-- @if($list_item['disposeStatus'] != 0 && $list_item['status'] == 0) -->
											<p>
												<php>
													$url = u('restaurant/editGoods')."?goodsId=".$list_item['id']."&restaurantId=".$restaurant['id'];
												</php>
												<action type="edit" css="blu" url="{{$url}}">
												</action>
											</p>
										<!-- @endif -->
										<p>
											<php>
												$destroyGoods = u('restaurant/destroyGoods')."?id=".$list_item['id'];
											</php>
											<action type="destroy" css="red" url="{{$destroyGoods}}"></action>
										</p>
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
<script type="text/javascript">
	$(function(){
		$("input[name='joinService']").click(function(){
			var joinService = 0;
			var id = $(this).parent().parent().parent().parent().parent().attr('key');
			$(".tr-"+id+" input[name='joinService']:checked").each(function(){
				joinService += this.value*1;
			});
			if(joinService < 1 || joinService > 3){
				joinService = 0;
			}
			$.post("{{u('Restaurant/joinService')}}",{'joinService':joinService,'id':id},function(res){
				$.ShowAlert(res.msg);
			});
		});

		//上下架
		$.goodsUpDown = function(id, status) {
			$.post("{{u('Restaurant/goodsUpDown')}}",{'id':id,'status':status},function(res){
				$.ShowAlert(res.msg);
			});
		}
		
	})
</script>
@stop