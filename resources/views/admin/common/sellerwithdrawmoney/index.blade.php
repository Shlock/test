@extends('admin._layouts.base')
@section('css') 
@stop 
@section('right_content')
@yizan_begin 
		<yz:list>
		<search> 
			<row>
				<item name="sellerName" 	label="服务人员"></item>  
				<item name="sellerMobile" label="服务手机"></item>
			</row>
			<row> 
				<item name="beginTime" label="开始时间" type="date"></item>
				<item name="endTime" label="结束时间" type="date"></item>
				<btn type="search"></btn>
			</row>
		</search>
		<yz:tabs>  
			<navs>
				<nav label="所有">
					<attrs>
						<url>{{ u('SellerWithdrawMoney/index',['status'=>'-1','nav'=>1]) }}</url>
						<css>@if( $nav == 1 ) on @endif</css>
					</attrs>
				</nav>
				<nav label="未处理">
					<attrs>
						<url>{{ u('SellerWithdrawMoney/index',['status'=>'1','nav'=>2]) }}</url>
						<css>@if( $nav == 2 ) on @endif</css>
					</attrs>
				</nav>
				<nav label="未通过">
					<attrs>
						<url>{{ u('SellerWithdrawMoney/index',['status'=>'2','nav'=>3]) }}</url>
						<css>@if( $nav == 3 ) on @endif</css>
					</attrs>
				</nav>
				<nav label="已取消">
					<attrs>
						<url>{{ u('SellerWithdrawMoney/index',['status'=>'3','nav'=>4]) }}</url>
						<css>@if( $nav == 4 ) on @endif</css>
					</attrs>
				</nav>
				<nav label="处理中">
					<attrs>
						<url>{{ u('SellerWithdrawMoney/index',['status'=>'4','nav'=>5]) }}</url>
						<css>@if( $nav == 5 ) on @endif</css>
					</attrs>
				</nav>
				<nav label="成功">
					<attrs>
						<url>{{ u('SellerWithdrawMoney/index',['status'=>'5','nav'=>6]) }}</url>
						<css>@if( $nav == 6 ) on @endif</css>
					</attrs>
				</nav>
			</navs> 
		</yz:tabs>
			<btns> 
				<linkbtn label="导出到EXCEL" type="export"></linkbtn>
			</btns>
			<table>
				<columns>				 
					<column code="money" label="提现资料" align="left" width="140">     
						<p data-sn="{{ $list_item['sn'] }}">SN码：{{ $list_item['sn'] }}</p> 
						<p>服 务：{{ $list_item['seller']['name'] }}</p> 
					</column>  
					<column code="bank" label="提现信息" align="left" width="160">   
						<p>金额：{{ $list_item['money'] }}</p> 
						<p>银行：{{ $list_item['bank'] }}</p> 
						<p>卡号：{{ $list_item['bankNo'] }}</p> 
					</column> 
					<column label="提现说明" align="left">
						<p>说明：{{ $list_item['content'] }}</p>  
						<p>时间：{{  yzday($list_item['createTime']) }}</p> 
					</column> 
					<column label="处理信息" align="left">
						<p>处理人员：{{ $list_item['disposeAdmin'] }} </p> 
						<p>处理备注：{{ $list_item['disposeRemark'] }} </p> 
						<p>处理时间：{{ yzday($list_item['disposeTime']) }} </p>  
					</column> 
					<column code="stat" label="状态"></column>
					<actions width="80"> 
						@if($list_item['status'] == 3 )
						<!-- <action label="处理中" click="WithdrawMoney( {{ $list_item['id'] }} , '{{ $list_item['disposeRemark'] }}' )"></action> -->
						交易正在处理
						@elseif($list_item['status'] == 2 )
						<action label="再次处理" click="WithdrawMoneys( {{ $list_item['id'] }} )" css="red"></action>
						@elseif($list_item['status'] == 1 )
							未通过
						@elseif($list_item['status'] == 0 )
							<action label="提现" click="WithdrawMoneys( {{ $list_item['id'] }} )" css="blu"></action>
						@else
							交易成功
						@endif  
					</actions>
				</columns>  
			</table>
		</yz:list> 
	@yizan_end
@stop
@section('js')
<script type="text/tpl" id="WithdrawMoneys">
<div style="width:500px;text-align:center;margin:15px 0"> 
	<span class="msgs"style="display: block;text-align:left;margin:5px"> </span>
	<textarea name='disposeRemark' id='disposeRemark' placeholder='请务必填写提现操作的备注。' style="width:400px;height:100px;border:1px solid #EEE"></textarea> 
</div>
</script>  
<script type="text/javascript"> 
//提现处理
function WithdrawMoneys(id) {  
    var dialog = $.zydialogs.open($("#WithdrawMoneys").html(), {
        boxid:'SET_GROUP_WEEBOX',
        width:300,
        title:'退款处理',
        showClose:true,
        showButton:true,
        showOk:true,
        showCancel:true,
        okBtnName: '确定提现',
		cancelBtnName: '拒绝提现',
        contentType:'content',
        onOk: function(){
            var query = new Object();
            query.id = id;
            query.content = $("#disposeRemark").val(); 
            query.status = 4;  
            if(query.content == ""){
            	$.ShowAlert("确定退款内容详细不能为空");
            }else{
	            dialog.setLoading();
	            	 $.post("{{ u('SellerWithdrawMoney/edit')  }}",query,function(result){  
	                	dialog.setLoading(false);  
	                	if(result.status == true){ 
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
            $("#disposeRemarkj").show();
            $(".ssshow").hide();
            query.content = $("#disposeRemark").val(); 
            if(query.content == ""){
            	$.ShowAlert("拒绝退款内容详细不能为空");
            }else{
            	dialog.setLoading();
            	query.status = 1; 
	        	$.post("{{ u('SellerWithdrawMoney/edit')  }}",query,function(result){ 
	                dialog.setLoading(false);  
	                if(result.status == true){
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
//提现处理中
function WithdrawMoney(id,remark) {  
    var dialog = $.zydialogs.open($("#WithdrawMoneys").html(), { 
        boxid:'SET_GROUP_WEEBOX',
        width:300,
        title:'退款处理中',
        showClose:true,
        showButton:true,
        showOk:true,
        showCancel:true,
        okBtnName: '转入',
		cancelBtnName: '取消提现',
        contentType:'content',
        onOk: function(){
            var query = new Object();
            query.id = id;
            query.content = $("#disposeRemark").val(); 
            query.status = 4;  
            if(query.content == ""){
            	$.ShowAlert("确定退款内容详细不能为空");
            }else{
	            dialog.setLoading();
	            	 $.post("{{ u('SellerWithdrawMoney/edit')  }}",query,function(result){  
	                	dialog.setLoading(false);  
	                	if(result.status == true){ 
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
            $("#disposeRemarkj").show();
            $(".ssshow").hide();
            query.content = $("#disposeRemark").val(); 
            if(query.content == ""){
            	$.ShowAlert("拒绝退款内容详细不能为空");
            }else{
            	dialog.setLoading();
            	query.status = 2; 
	        	$.post("{{ u('SellerWithdrawMoney/edit')  }}",query,function(result){ 
	                dialog.setLoading(false);  
	                if(result.status == true){
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
	$(".msgs").text("正在退款的备注：");
	$("#disposeRemark").val(remark);
}  
</script>  
@stop 