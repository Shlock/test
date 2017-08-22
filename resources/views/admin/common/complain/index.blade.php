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
					<yz:select name="status" options="0,1,2" texts="全部,未处理,已处理" selected="$search_args['status']"></yz:select>
				</item>
			</row> 
			<row>
				<item name="disposeAdminId" label="处理人员"></item>
				<item name="beginTime" label="开始时间" type="date"></item>
				<item name="endTime" label="结束时间" type="date"></item>
				<item name="$nav" label="" type="hidden"></item>
				<btn type="search"></btn> 
			</row> 
		</search>  
		<tabs>
			@yizan_yield('complaincommon')
				<navs>
					<nav label="所有举报">
						<attrs>
							<url>{{ u('Complain/index',['nav'=>0]) }}</url>
							<css>@if( $nav == 0 ) on @endif</css>
						</attrs>
					</nav>
					<nav label="人员举报">
						<attrs>
							<url>{{ u('Complain/index',['status'=>'0','type'=>'1','nav'=>1]) }}</url>
							<css>@if( $nav == 1 ) on @endif</css>
						</attrs>
					</nav>
					<nav label="机构举报">
						<attrs>
							<url>{{ u('Complain/index',['status'=>'0','type'=>2,'nav'=>2]) }}</url>
							<css>@if( $nav == 2 ) on @endif</css>
						</attrs>
					</nav>
				</navs>
			@yizan_stop
			</tabs>
			<table>
			<rows>
				<headers>
					<header code="user" label="举报会员" width="120"></header>
					@if( $nav == 1 )
					<header code="seller" label="被举人员" ></header>
					@elseif( $nav == 2 )
					<header code="seller" label="被举机构" width="100"></header>
					@else
					<header code="seller" label="所被举者"></header>
					@endif
					<header code="createTime" label="举报时间" width="120"></header>
					<header code="dispose_admin" label="处理人员" width="90"></header>
					<header code="dispose_time" label="处理时间" width="90"></header>
				</headers>
				<row>
					<tr class="{{ $list_item_css }}">
						<td>{{ $list_item['user']['name'] }}</td>
						<td>{{ $list_item['seller']['name'] }}</td>
						<td>{{ yztime($list_item['createTime']) }}</td>
						<td>{{ $list_item['adminUser']['name'] }}</td>
						<td>{{ yztime($list_item['disposeTime']) }}</td>
						<td rowspan="2">
							<actions width="60">
								@if($list_item['status'] == 0)
								<action label="处理" click="WithdrawMoney({{ $list_item['id'] }})" css="blu"></action>
								@else
								已处理
								@endif
							</actions>
						</td>
					</tr>
					<tr class="{{ $list_item_css }}">
						<td colspan="3"  style="text-align:left;vertical-align:top;">
							<dl class="m-dl-inline">
								<dt>举报描述：<span></dt>
								<dd>
									{{ $list_item['content'] }}
									<p>
										@foreach($list_item['images'] as $image)
										<a href="{{ $image }}" target="_blank"><img src="{{ $image }}" alt="" height="40"></a>
										@endforeach
									</p>
								</dd>
							</dd>
						</td>
						<td colspan="2" style="text-align:left;vertical-align:top;word-wrap:break-word">
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
	            query.status = 1;  
	            if(query.content == ""){
	            	$.ShowAlert("请输入举报处理内容！");
	            }else{
		            dialog.setLoading();
		            	$.post("{{ u('Complain/dispose') }}",query,function(result){ 
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
	            $.zydialogs.close("SET_GROUP_WEEBOX");
	        }
    	});
	} 
</script>
@stop 