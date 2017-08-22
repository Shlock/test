@extends('admin._layouts.base')
@section('css')
<style type="text/css">
	.shuoming{
		overflow: hidden;
		text-overflow: ellipsis;
		display: -webkit-box;
		-webkit-line-clamp: 1;
		-webkit-box-orient: vertical;
		word-break:break-all;
	}
.y-qghd{border:1px solid #ededed;background:#f9f9f9;padding:10px 1em;margin-bottom:10px;}
.y-start{position: relative;border:1px solid #ededed;background:#fff;padding:10px 10px;}
.y-qghdbtn{position: absolute;top:9px;left:1em;border-radius:3px;overflow: hidden;}
.y-qghdbtn a{float:left;border:1px solid #ededed;border-width:1px 1px 1px 0;width:35px;line-height:20px;font-size:12px;display:inline-block;text-align: center;}
.y-qghdbtn a:first-child{border-width:1px 0 1px 1px;}
.y-qghdbtn a:hover{color:#313233;}
.y-qghdbtn a.on{border:1px solid #00BB00;background:#00BB00;color:#fff;}
.y-start .y-qghdmain{color:#979797;margin-left:90px;line-height:20px;}
</style>
@stop
@section('right_content')
<?php 
$makeType = [
	['id'=>0,'name'=>'全部'],
	['id'=>1,'name'=>'上门'],
	['id'=>2,'name'=>'到店'],
	['id'=>3,'name'=>'上门+到店'],
];

 ?>
	@yizan_begin
		<yz:list>
			<search> 
				<row>
				    <yz:fitem label="服务分类">
						<yz:select name="catePid" options="$catePid" textfield="name" valuefield="id" selected="$search_args['catePid']"></yz:select>
						@if(!empty($cateId))
							<yz:select name="cateId" options="$cateId" textfield="name" valuefield="id" selected="$search_args['cateId']"></yz:select>
						@else
							<select name="cateId" id="cateId" class="sle">
								<option value>全部</option>
							</select>
						@endif
					</yz:fitem>
					
					<item name="name" label="服务名称"></item>
					<item label="预约方式">
						<yz:select name="makeType" options="$makeType" textfield="name" valuefield="id" selected="$search_args['makeType']">
						</yz:select>
					</item> 
				</row>
				<row>
					<item name="price" label="服务单价"></item>
					<item name="minTime" label="服务时长"></item>
					<item name="maxTime" label="至"></item>
					<btn type="search"></btn>
				</row>
			</search>
			<php>
			 $url = u('ShoppingSpree/index',['id'=>$param['id']]);
			</php>
			<btns>
				<linkbtn label="确认" url="{{$url}}"></linkbtn>
				<!-- <linkbtn label="返回" url="{{u('ShoppingSpree/index',['id'=>$param['id']])}}"></linkbtn> -->
			</btns>
			
			<table relmodule="SystemGoods">
				<columns>
					<column code="image" label="图片" type="image" width="60" iscut="1"></column>
					<column code="name" label="服务信息" align="left">
						<p>名称：{{ $list_item['name'] }}</p>
						<p>时长：{{ $list_item['serverAllTime'] }}分钟</p>
						<p class="shuoming">
							@foreach($list_item['explain'] as $key => $value)
								{{ $value['name'] }}&nbsp;
							@endforeach
						</p>
					</column>
					<column label="服务类别">
						{{ $list_item['cateFirst']['name'] }}|{{ $list_item['cateSecond']['name'] }}
					</column>
					<column code="price" label="服务收费/元"></column>
					<column label="预约方式">
						@if($list_item['makeType'] == 1)
							上门
						@elseif($list_item['makeType'] == 2)
							到店
						@elseif($list_item['makeType'] == 3)
							上门+到店
						@endif
					</column>
					<!-- <column code="status" type="status" label="状态" width="40"></column> -->
					<actions width="60">
					    @if(empty($list_item['activityGoods']) || $list_item['activityGoods']['activityId'] != $param['id'])
						<p><a href="javascript:;" class="serviceChoose blu" data-pk="1" data-goods="{{$list_item['id']}}" data-type='add'>选择</a></p>
						@else
						<p><a href="javascript:;" class="serviceChoose blu" data-pk="1" data-goods="{{$list_item['id']}}" data-type='del'>已选择</a></p>
						@endif
					</actions>
				</columns>
			</table>
		</yz:list>
	@yizan_end
@stop

@section('js')
<script type="text/javascript">
	$(function(){
		//选择服务
		var activity_id = "{{$param['id']}}";
		$(document).on('click',".serviceChoose",function(){
			var type = $(this).data('type');
			var goodsId = $(this).data('goods');
			var param = {
					'goods_id' : goodsId,
					'activity_id' : activity_id,
					'type' : type
					};
			$.post('{{ u("ShoppingSpree/doAddService") }}',param,function(res){
				if(res.code == 0){
					location.reload();
				}else{
					alert(res.msg);
				}
			},"json");
		});
		
		//通过一级分类查找二级分类
		$("#catePid").change(function(){
			var pid = $(this).val();
			$("#cateId").html("<option value>全部</option>");
			if(pid < 1){
				return false;
			}
			$.post("{{ u('Goods/selectSecond') }}",{'pid':pid,'status':1},function(res){
				if(res.length > 0){
					var html = "<option value>全部</option>";
					$.each(res, function(k,v){
						html += "<option value='"+this.id+"'>"+this.name+"</option>";
					});
					$("#cateId").html(html);
				}
			},'json');
		});
	});
</script>
@stop
