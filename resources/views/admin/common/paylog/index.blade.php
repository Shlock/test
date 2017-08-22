@extends('admin._layouts.base')
@section('css')
@stop
@section('right_content')
	@yizan_begin
	<yz:list>
        <search>
            <row>
                <item name="name" 	label="会员名称"></item>
                <item name="mobile" 	label="手机号"></item>
                <item name="orderSn" 	label="订单号"></item>
            </row>
            <row>
                <item name="paySn" label="流水号"></item>
                <item name="beginTime" label="开始时间" type="date"></item>
                <item name="endTime" label="结束时间" type="date"></item>
            </row>
            <row>
                <item label="支付方式">
                    <yz:select name="payment" options="$payments"  textfield="name" valuefield="code" first="全部" firstvalue="" selected="$search_args['payment']"></yz:select>
                </item>
                <item label="支付状态">
                    <yz:select name="payStatus" options="0,1,2" texts="全部,未支付,已支付" selected="$search_args['payStatus']"></yz:select>
                </item>
                <item label="类型">
                    <yz:select name="payType" options="0,1,2,3" texts="全部,买家消费,买家充值,退款" selected="$search_args['payType']"></yz:select>
                </item>
                <btn type="search"></btn>
            </row>
        </search>
        <btns>
            <linkbtn label="导出到EXCEL" type="export">
                <attrs>
                    <url>{{ u('PayLog/export', $search_args ) }}</url>
                </attrs>
            </linkbtn>
        </btns>
        <table>
			<columns>  
				<column label="用户信息" align="left">
					<div>昵称：{{ $list_item['user']['name'] }}</div>
					<div>手机：{{ $list_item['user']['mobile'] }}</div> 
				</column>
				<column label="订单信息" align="left">
					<div>订单 S N：{{ $list_item['order']['sn'] }}</div>
					<div>服务名称：{{ $list_item['content'] }}</div>
					<div>服务费用：{{ $list_item['money']  }}</div>
					<div>创建时间：{{ yzTime($list_item['createTime'])  }}</div>
				</column>
				<column label="支付信息" align="left">
					<div>流 水 号：{{ $list_item['sn']  }}</div>
                    <div>支付方式：{{ Lang::get('admin.payments.'.$list_item['paymentType'])  }}</div>
                    <div>类    型：{{ Lang::get('admin.userPayType.'.$list_item['payType'])  }}</div>
					<div>支付时间：{{  yzTime($list_item['payTime']) }}</div>
					<div>支付状态：{{ $list_item['order']['orderStatusStr'] ? $list_item['order']['orderStatusStr'] : Lang::get('admin.sellerPayType.'.$list_item['status']) }}</div> 
				</column>  
			</columns>
		</table>
	</yz:list>
	@yizan_end
@stop

@section('js')

@stop
