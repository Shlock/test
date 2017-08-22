@extends('admin._layouts.base')
@section('css') 
<style type="text/css">
	.ssshow{
		height: 50px;
		line-height: 50px;
		width: 100%;
		text-align: center;
		color: #bbb; 
		font-size: 12px;
	}
</style>
@stop
@section('right_content')
	@yizan_begin 
		<yz:list>
		<search>  
			<row>
				<item name="status" label="处理状态">
					<yz:select name="status" options="0,1,2,3" texts="全部,未处理,已处理,已驳回" selected="$search_args['status']"></yz:select>
				</item>
			</row> 
			<row>
				<item name="sn" label="订单流水号"></item>
				<item name="beginTime" label="开始时间" type="date"></item>
				<item name="endTime" label="结束时间" type="date"></item>
				<item name="$nav" label="" type="hidden"></item>
				<btn type="search"></btn> 
			</row> 
		</search>  
		<table>
			<rows>
				<headers>
					<header code="user" label="举报会员" width="120"></header> 
					<header code="order" label="被举报订单" ></header> 
					<header code="createTime" label="举报时间" width="120"></header>
					<header code="dispose_admin" label="处理人员" width="90"></header>
					<header code="dispose_time" label="处理时间" width="90"></header>
				</headers>
				<row>
					<tr class="{{ $list_item_css }}">
						<td>{{ $list_item['user']['name'] }}</td>
						<td>{{ $list_item['order']['sn'] }}</td>
						<td>{{ yztime($list_item['createTime']) }}</td>
						<td>{{ $list_item['adminUser']['name'] }}</td>
						<td>{{ yztime($list_item['disposeTime']) }}</td>
						<td rowspan="2">
							<actions width="60">
								@if($list_item['status'] == 3)
								已驳回
								@elseif($list_item['status'] == 2)
								已处理
								@else
								<action label="处理" click="orderComplain({{ $list_item['id'] }})" css="blu"></action>
								@endif
							</actions>
						</td>
					</tr>
					<tr class="{{ $list_item_css }}">
						<td colspan="3"  style="text-align:left;vertical-align:top;line-height:20px;">
							<dl class="m-dl-inline">
								<dt><span>举报描述：</span></dt>
								<dd>
									{{ $list_item['content'] }}
								</dd>
							</dl>
							@if($list_item['images'])
							<dl class="m-dl-inline">
								<dd> 
									@foreach($list_item['images'] as $image)
									<a href="{{ $image }}" target="_blank"><img src="{{ $image }}" alt="" height="80"></a>
									@endforeach
								</dd>
							</dl>
							@endif
						</td>
						<td colspan="2" style="text-align:left;vertical-align:top;word-wrap:break-word">
							<dl class="m-dl-inline">
								<dt>处理结果：</dt>
								<dd>
									{{ $list_item['disposeResult'] }}
								</dd>
							</dl>
						</td>
					</tr>
				</row>
			</rows>
			</table>
		</yz:list> 
	@yizan_end
@stop
@section('js') 
<script type="text/tpl" id="orderComplain"> 
	<div style="width:500px; text-align:center;margin:10px 0 ">
		<textarea name='disposeRemark' id='disposeRemark' placeholder='请务必填写举报操作的备注。' style="width:400px;height:100px;border:1px solid #EEE"></textarea> 
	</div>
</script> 
<script type="text/javascript"> 
	$(function(){
		$('#yzForm').submit(function(){
			var beginTime = $("#beginTime").val();
			var endTime = $("#endTime").val();
			if(beginTime!='' || endTime!='') {
				if(beginTime==''){
					alert("开始时间不能为空");return false;
				}
				else if(endTime==''){
					alert("结束时间不能为空");return false;
				}
				else if(endTime < beginTime){
					alert("结束时间不能大于开始时间");return false;
				}
			}
		});
	});
	
	function orderComplain(id) {   
		var dialog = $.zydialogs.open($("#orderComplain").html(), {
	        boxid:'SET_GROUP_WEEBOX',
	        width:300,
	        title:'举报处理',
	        showClose:true,
	        showButton:true,
	        showOk:true,
	        showCancel:true,
	        okBtnName: '通过举报',
			cancelBtnName: '驳回举报',
	        contentType:'content',
	        onOk: function(){
	            var query = new Object();
	            query.id = id;
	            query.content = $("#disposeRemark").val(); 
	            query.status = 2;  
	            if(query.content == ""){
	            	$.ShowAlert("请输入举报处理内容！");
	            }else{
		            dialog.setLoading();
		            	$.post("{{ u('OrderComplain/dispose') }}",query,function(result){ 
		                	dialog.setLoading(false);  
		                	if(result.code == 0){
		                   	 	window.location.reload();
		                	}else{
		                    	$.ShowAlert(result.msg);
		                    $.zydialogs.close("SET_GROUP_WEEBOX");
		                }
		            },'json');
	            }
	        },
	        onCancel:function(){ 
	        	var query = new Object();
	            query.id = id;
	            query.status = 3;  
	            query.content = $("#disposeRemark").val(); 
	            if(query.content == ""){
	            	$.ShowAlert("请输入举报处理内容！");
	            }else{
		            dialog.setLoading();
		            	$.post("{{ u('OrderComplain/dispose') }}",query,function(result){ 
		                	dialog.setLoading(false);  
		                	if(result.code == 0){
		                   	 	window.location.reload();
		                	}else{
		                    	$.ShowAlert(result.msg);
		                    $.zydialogs.close("SET_GROUP_WEEBOX");
		                }
		            },'json');
	            }
	            $.zydialogs.close("SET_GROUP_WEEBOX");
	        }
    	});
	} 
</script>
@stop 