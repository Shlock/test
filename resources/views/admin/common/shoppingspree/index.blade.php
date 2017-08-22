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
	<!-- <div class="y-qghd">
	       <h3 class="f14">抢购活动</h3>
	       <div class="y-start mt10">
	           <div class="y-qghdbtn">
	               <a @if($data['status'] == 1) class="on" @endif href="javascript:;" data-type="open">开启</a>
	               <a @if($data['status'] == 0) class="on" @endif href="javascript:;" data-type="close">关闭</a>
	           </div>
	           <div  class="f12 y-qghdmain">关闭抢购活动时，可添加显示抢购服务，当开启抢购活动时将不允许在添加任何服务</div>
	       </div>
	   </div>
	   <div @if($data['status'] == 0) style="display:none;"@endif> -->
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
			 $url = u('ShoppingSpree/addService',['id'=>$param['id']]);
			</php>
			<btns>
				<linkbtn type="add" label="添加服务项目" url="{{ $url }}"></linkbtn>
			</btns>
			<tabs>
				<navs>
				    <nav label="抢购设置">
						<attrs>
							<url>{{ u('ShoppingSpree/setting',['id'=>$param['id']]) }}</url>
							<css> @if(ACTION_NAME == 'setting') on @endif </css>
						</attrs>
					</nav>
					<nav label="抢购活动">
						<attrs>
							<url>{{ u('ShoppingSpree/index',['id'=>$param['id']]) }}</url>
							<css> @if(ACTION_NAME == 'index') on @endif </css>
						</attrs>
					</nav>
				</navs>
			</tabs>
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
						<p><a href="{{ u('ShoppingSpree/detail',['id'=>$list_item['id']]) }}" class=" blu" data-pk="1" target="_self">查看</a></p>
					</actions>
					<column label="抢购价格/元">
					   <input type="text" value="{{$list_item['activityGoods']['shoppingSpreePrice']}}" class="u-ipttext shopping_price" style="width:50%;" data-goodsid="{{ $list_item['id'] }}"/>
					</column>
				</columns>
			</table>
		</yz:list>
		<!-- </div> -->
		
		
	@yizan_end
@stop

@section('js')
<script type="text/javascript">
	$(function(){
		//设置抢购价格
		$(document).on('keypress','.shopping_price',function(e){
			var key = e.which;
            if (key == 13) {
                e.preventDefault();
                var id = $(this).data('goodsid');
                var activity = "{{$param['id']}}";
                var price = $(this).val();
                $.post('{{ u("ShoppingSpree/setPrice") }}',{'id':id,'activity_id':activity,'price':price},function(res){
					if(res.code == 0){
						window.location.reload();
					}else{
						$.ShowAlert(res.msg);
					}
                },"json");
            }
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
		
		$(document).on('click','.y-qghd a',function(){
			var type = $(this).data('type');
			var url = '{{ u("ShoppingSpree/setStaus") }}';
			$.post(url,{'type':type},function(res){
				if(res.code == 0){
					window.location.reload();
				}else{
					$.ShowAlert(res.msg);
				}
			},"json");
		});
	});
</script>
@stop
