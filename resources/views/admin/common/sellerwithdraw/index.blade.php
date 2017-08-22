@extends('admin._layouts.base')
@section('css') 
@stop 
@section('right_content')
@yizan_begin 
		<yz:list>
    		<search url="{{ $url }}">
    			<row>
    				<item name="name" 	label="商户"></item>
                    <item name="beginTime" label="开始时间" type="date"></item>
                    <item name="endTime" label="结束时间" type="date"></item>
    				<btn type="search"></btn>
    			</row>
    		</search>
            <yz:tabs>
                <navs>
                    <nav name="status" label="待处理">
                        <attrs>
                            <url>{{ u('SellerWithdraw/index',['status'=>'0','nav'=>1]) }}</url>
                            <css>@if( $nav == 1) on @endif</css>
                        </attrs>
                    </nav>
                    <nav name="status" label="已通过">
                        <attrs>
                            <url>{{ u('SellerWithdraw/index',['status'=>'1','nav'=>2]) }}</url>
                            <css>@if( $nav == 2 ) on @endif</css>
                        </attrs>
                    </nav>
                    <nav name="status" label="已拒绝">
                        <attrs>
                            <url>{{ u('SellerWithdraw/index',['status'=>'2','nav'=>3]) }}</url>
                            <css>@if( $nav == 3 ) on @endif</css>
                        </attrs>
                    </nav>
                </navs>
            </yz:tabs>
			<table>
				<columns>				 
				<column label="提现流水" code="sn" width="150"></column>
				<column label="提现金额" code="money" width="60">
				    ￥{{ $list_item['money'] }}
				</column>
				<column label="提现信息" code="seller" align="left" width="180">
                    <p>商家：<a href="{{ u('Service/edit',['id'=>$list_item['seller']['id']]) }}">{{ $list_item['seller']['name'] }}</a></p>
				    <p>商家余额：￥{{ $list_item['extend']['money'] }}</p>
                    <p>开户行：{{ $list_item['bank'] }}</p>
				    <p>持有人：{{ $list_item['name'] }}</p>
				    <p>银行卡：{{ $list_item['bankNo'] }}</p>
				    <p>申请时间：{{ yztime($list_item['createTime']) }}</p>
				</column>
				<column label="处理信息" code="seller" align="left" width="150">
				    @if( !empty($list_item['disposeTime']) )
				    <p>处理人：{{ $list_item['admin']['name'] }}</p>
				    <p>处理备注：{{ $list_item['disposeRemark'] }}</p>
				    <p>处理时间：{{ yztime($list_item['disposeTime']) }}</p>
				    @else
				    <span style="text-align:center;display: block;color:#CCC">等待管理员处理</span>
				    @endif
				</column>
				<column label="状态" code="status" width="40">
				        {{ Lang::get('admin.status_txian.'.$list_item['status']) }}
				</column>
					<actions width="80">	
                        <action label="商品订单列表" >
                            <attrs>
                                <url>{{ u('Order/index',['sellerName'=>$list_item['seller']['name']])}}</url>
                            </attrs>
                        </action>  
                        <action label="服务订单列表" >
                            <attrs>
                                <url>{{ u('ServiceOrder/index',['sellerName'=>$list_item['seller']['name']])}}</url>
                            </attrs>
                        </action> 
                        <br>
                        <action label="提现记录" >
                            <attrs>
                                <url>{{ u('SellerWithdraw/index',['status'=>'1','nav'=>2,'name'=>$list_item['seller']['name']])}}</url>
                            </attrs>
                        </action>   
                        <br>  				 
						@if($list_item['status'] == STATUS_WITHDRAW_STAY )
						   <action label="确定" click="WithdrawMoneys('1','{{ $list_item['id'] }}' )" css="blu"></action>
					       <action label="拒绝" click="WithdrawMoneys('-1', '{{ $list_item['id'] }}' )" css="red mt10"></action>
						@endif  
					</actions>
				</columns>  
			</table>
		</yz:list> 
	@yizan_end
@stop
@section('js')
<script type="text/tpl" id="WithdrawMoneys">
<div style="width:400px;text-align:center;margin:15px 0"> 
	<span class="msgs"style="display: block;text-align:center;margin:5px"></span>
	<textarea name='disposeRemark' id='disposeRemark' placeholder='请填写提现的备注。' style="width:380px;height:100px;border:1px solid #EEE"></textarea> 
</div>
</script> 
<script type="text/javascript"> 
//提现处理
function WithdrawMoneys(type,id) {
	if(type == 1){
		var name = "确认";
	}else{
		var name = "拒绝";
	}
    var dialog = $.zydialogs.open($("#WithdrawMoneys").html(), {
        boxid:'SET_GROUP_WEEBOX',
        width:300,
        title:name+'提现',
        showClose:true,
        showButton:true,
        showOk:true,
        showCancel:true,
        okBtnName: name+"提现",
		cancelBtnName: '取消',
        contentType:'content',
        onOk: function(){
     		dialog.setLoading();
            var query = new Object();
            query.id = id;
        	query.content = $("#disposeRemark").val(); 
       	  //  if(query.content == ""){
           //   	$.ShowAlert(name+"提现内容详细不能为空"); 
                //return false;
           //   	dialog.setLoading(false);
           // } else {
                if(type == "-1"){ //拒绝
                    query.status = "{{ STATUS_WITHDRAW_REFUSE }}";
                	$.post("{{ u('SellerWithdraw/dispose')  }}",query,function(result){  
                    	dialog.setLoading(false);
                    	if(result.status == true){ 
                       	 	window.location.reload();
                    	}else{
                        	$.ShowAlert(result.msg);
                        	$.zydialogs.close("SET_GROUP_WEEBOX");
    	                }
    	            },'json');
            	}else{
                 query.status = "{{ STATUS_WITHDRAW_PASS }}";
            	 $.post("{{ u('SellerWithdraw/dispose')  }}",query,function(result){  
                        dialog.setLoading(false);  
                    	if(result.status == true){ 
                       	 	window.location.reload();
                    	}else{
                        	$.ShowAlert(result.msg);
                        	$.zydialogs.close("SET_GROUP_WEEBOX");
                        }
                    },'json');
            	}        
          //  }    
        }, 
		onCancel:function(){
            $.zydialogs.close("SET_GROUP_WEEBOX");
        }  
	});	
}
</script>  
@stop