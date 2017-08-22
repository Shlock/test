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
<!-- 列表 -->
		<yz:list> 
			<table> 
				<rows>
				<headers>
					<header label="反馈信息"></header>  
					<header label="意见内容"></header> 
					<header label="状态"></header> 
				</headers>
				<row>
					<tr class="{{ $list_item_css }}">
						<td style="text-align:left;">
							<p>反馈人员：{{ $list_item['user']['name'] }}</p>
							<p>反馈时间：{{ yztime($list_item['createTime']) }}</p>
							<p>客户端类型：{{ $list_item['clientType'] }}</p>
						</td>   
						<td style="text-align:left">
							<p>处理人员：{{ $list_item['disposeAdmin'] }} </p> 
							<p>处理备注：{{ $list_item['disposeResult'] }} </p> 
							<p>处理时间：{{ yzday($list_item['disposeTime']) }} </p>  
						</td> 
						<td> 
							<p>{{ $list_item['status'] == '1' ? '管理员已处理' : '未处理'}}</p>
						</td> 
						<td rowspan="3">
							@if($list_item['status'] == 0)
								<action label="未处理" click="WithdrawMoney({{ $list_item['id'] }})" css="gray"></action>
							@else 
								已处理
							@endif
							<actions> 
								<action type="destroy" css="red"></action>
							</actions>
						</td>
					</tr>
					<tr class="{{ $list_item_css }}" style="text-align:left;">	 
						<td colspan="3" style="text-align:left;">			
							<p style='text-align:left;display:block;word-break: break-all;word-wrap: break-word;'><b>设备信息：</b>{{ yzHtmlSpecialcharsDecode( $list_item['clientInfo'] ) }}</p> 
							<p style='text-align:left;display:block;word-break: break-all;word-wrap: break-word;'><b>反馈信息：</b>{{ $list_item['content'] }}</p> 
						</td>  
					</tr>
					<tr class="{{ $list_item_css }}" style="text-align:left;">	
					@if($list_item['userType'] == 1)
						<td colspan="3" style="text-align:left;">					
							<p style='text-align:left;display:block;word-break: break-all;word-wrap: break-word;'> 
								<b>推送手机</b>：{{$list_item['users']}}
							</p> 
						</td> 
					@endif
					</tr>
				</row>
			</rows>
			</table>
		</yz:list> 
	@yizan_end
@stop
@section('js')
<script type="text/tpl" id="WithdrawMoney"> 
	<div style="width:500px;text-align:center;margin:15px 0"> 
		<textarea name='disposeRemark' id='disposeRemark' placeholder='请务必填写反馈信息操作的备注。' style="width:400px;height:100px;border:1px solid #EEE"></textarea> 
	</div>
</script>  
<script type="text/javascript"> 
	function WithdrawMoney(id) {    
		var url = "{{ u($type.'AppFeedback/edit') }}";
		var dialog = $.zydialogs.open($("#WithdrawMoney").html(), {
	        boxid:'SET_GROUP_WEEBOX',
	        width:300,
	        title:'反馈信息处理',
	        showClose:true,
	        showButton:true,
	        showOk:true,
	        showCancel:true,
	        okBtnName: '处理',
			cancelBtnName: '不处理',
	        contentType:'content',
	        onOk: function(){
	            var query = new Object();
	            query.id = id;
	            query.content = $("#disposeRemark").val(); 
	            query.status = 1;  
	            if(query.content == ""){
	            	$.ShowAlert("确定反馈信息操作的备注不能为空");
	            }else{
		            dialog.setLoading(); 
		            	$.post(url,query,function(result){ 
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