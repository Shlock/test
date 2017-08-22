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
    .notice{
        width:100%;
        height:40px;
        background:#FFFD86;
        line-height: 40px;
        margin-bottom: 5px;
        text-indent: 1em;
    }
    .notice b{
        color:red;
    }
</style>
@stop
@section('right_content')
	@yizan_begin 
<!-- 列表 -->
		<yz:list>
<!-- 搜索 -->
		<search url="{{ $url }}">
			<row>
				<item name="orderSn" label="订单号"></item>
                <item name="mobile" label="会员手机号"></item>
				<item name="beginTime" label="开始时间" type="date"></item>
				<item name="endTime" label="结束时间" type="date"></item>
				<btn type="search"></btn>
			</row>
		</search>
<!-- 导航 -->
            @if($nav == 1)
            <div class="notice">待退款订单数：<b>{{ $data['totalCount'] }}</b> 条，金额总计：<b>{{ $data['totalMoney'] }}</b> 元</div>
		    @endif
            <yz:tabs>
			<navs> 
				<nav name="status" label="待处理">
					<attrs>
						<url>{{ u('UserRefund/index',['status'=>'0','nav'=>1]) }}</url>
						<css>@if( $nav == 1) on @endif</css>
					</attrs>
				</nav>
				<nav name="status" label="已处理">
					<attrs>
						<url>{{ u('UserRefund/index',['status'=>'1','nav'=>2]) }}</url>
						<css>@if( $nav == 2 ) on @endif</css>
					</attrs>
				</nav>
			</navs>  
		</yz:tabs>
			<table>
				<columns>
					<column code="sn" label="订单号" width="150">
                        @if($list_item['orderType'] == 1)
                            <a href="{{ u('Order/detail',['id'=>$list_item['orderId']]) }}">{{ $list_item['sn'] }}</a>
                        @else
                            <a href="{{ u('ServiceOrder/detail',['id'=>$list_item['orderId']]) }}">{{ $list_item['sn'] }}</a>
                        @endif
                    </column>
                    <column code="mobile" label="退款会员" width="100"></column>
                    <column code="money" label="退款金额" width="60"></column>
                    <column code="tradeNo" label="原支付单号" width="200"></column>
                    <column code="status" label="状态">
                        @if($list_item['status'] == 0)
                            待退款
                         @elseif($list_item['status'] == 1)
                            退款成功
                            @else
                            退款失败
                        @endif
                    </column>
					<column code="content" label="退款说明"></column>
                    <actions width="40">
	                    @if( $nav == 1) 
		                    <a href="{{u('UserRefund/dispose', ['id'=>$list_item['id']])}}"  target="_blank" onclick="return window.confirm('将原路退款给客户，请确认')">退款</a>
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
</script>
@stop 