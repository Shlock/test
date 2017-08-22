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
	.hoverred{color: #000}
	.hoverred:hover{color: red}
</style>
@stop
@section('right_content')
	@yizan_begin 
		<yz:list>
		<search>  
			<row>
				<item name="status" label="处理状态">
					<yz:select name="status" options="0,1,2" texts="全部,未处理,已处理" selected="$search_args['status']"></yz:select>
				</item>
			</row> 
			<row>
				<item name="disposeAdminId" label="处理人员"></item>
				<item name="beginTime" label="开始时间" type="date"></item>
				<item name="endTime" label="结束时间" type="date"></item>
				<btn type="search"></btn> 
			</row> 
		</search>  
			<table>
				<rows>
					<headers>
						<header code="user" label="举报会员" width="90"></header>
						<header code="goods" label="被举报服务"></header>
						@yizan_yield("seller_header")
						<header code="seller" label="被举报机构"></header>
						@yizan_stop
						@yizan_yield("staff_header")
						<header code="staff" label="被举报员工"></header>
						@yizan_stop
						<header code="createTime" label="举报时间" width="90"></header>
						<header code="dispose_admin" label="处理人员" width="90"></header>
						<header code="dispose_time" label="处理时间" width="90"></header>
					</headers>
					<row>
						<tr class="{{ $list_item_css }}">
							<td>{{ $list_item['user']['name'] }}</td>
							<td>
								<a href="{{ u('SellerGoods/lookat',['id'=> $list_item['goods']['id']]) }}" target="_new" class="hoverred">
									{{ $list_item['goods']['name'] }}
								</a>
							</td>
							@yizan_yield("seller_td")
							<td>{{ $list_item['seller']['name'] }}</td>
							@yizan_stop
							@yizan_yield("staff_td")
							<td>{{ $list_item['staff']['name'] }}</td>
							@yizan_stop
							<td>{{ yztime($list_item['createTime']) }}</td>
							<td>{{ $list_item['adminUser']['name'] }}</td>
							<td>{{ yztime($list_item['disposeTime']) }}</td>
							<td rowspan="2">
								<actions width="60">
									<!-- @if($list_item['status'] == 0) -->
									<action label="处理" click="WithdrawMoney({{ $list_item['id'] }})" css="blu"></action>
									<!-- @else -->
									已处理
									<!-- @endif -->
								</actions>
							</td>
						</tr>
						<tr class="{{ $list_item_css }}">
							@yizan_yield("colspan")
							<td colspan="4"  style="text-align:left;vertical-align:top;">
							@yizan_stop
								<dl class="m-dl-inline">
									<dt>举报信息：<span></dt>
									<dd>
										{{ $list_item['content'] }}
										<p>
											<!-- @foreach($list_item['images'] as $image) -->
											<a href="{{ $image }}" target="_blank"><img src="{{ $image }}" alt="" height="40"></a>
											<!-- @endforeach -->
										</p>
									</dd>
								</dd>
							</td>
							<td colspan="3" style="text-align:left;vertical-align:top;word-wrap:break-word">
								<dl class="m-dl-inline">
									<dt>处理结果：</dt>
									<dd>
										{{ $list_item['disposeResult'] }}
									</dd>
								</dd>
							</td>
						</tr>
					</row>
				</rows>
			</table>
		</yz:list> 
	@yizan_end
@stop
@section('js') 
<script type="text/tpl" id="WithdrawMoney"> 
	<div style="width:500px;text-align:center;padding:10px;">
		<textarea name='disposeRemark' id='disposeRemark' placeholder='请务必填写举报操作的备注。' style="width:480px;height:100px;border:1px solid #EEE"></textarea> 
	</div>
</script> 
<script type="text/javascript"> 
	function WithdrawMoney(id) {   
		var dialog = $.zydialogs.open($("#WithdrawMoney").html(), {
	        boxid:'SET_GROUP_WEEBOX',
	        width:300,
	        title:'举报处理',
	        showClose:true,
	        showButton:true,
	        showOk:true,
	        showCancel:true,
	        okBtnName: '确定处理',
			cancelBtnName: '不处理',
	        contentType:'content',
	        onOk: function(){
	            var query = new Object();
	            query.id = id;
	            query.content = $("#disposeRemark").val();
	            if(query.content == ""){
	            	$.ShowAlert("确定举报内容详细不能为空");
	            }else{
		            dialog.setLoading();
		            	$.post("{{ u('GoodsAccusation/save') }}",query,function(result){ 
		                	dialog.setLoading(false);  
		                	if(result.status == true){
		                    	$.ShowAlert(result.msg);
		                   	 	window.location.reload();
		                	}else{
		                    	$.ShowAlert(result.msg);
		                    $.zydialogs.close("SET_GROUP_WEEBOX");
		                }
		            },'json');
	            }
	        },
	        onCancel:function(){ 
	            $.zydialogs.close("SET_GROUP_WEEBOX");
	        }
    	});
	} 
</script>
@stop 