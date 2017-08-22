@extends('admin._layouts.base')
@section('css')
<style type="text/css">
	.ts{color: #999;margin-left: 5px;vertical-align:middle;}
</style>
@stop
@section('right_content')
<?php 
	$activitytype = [
		['id'=>1,'name'=>'买赠叠加']
	];
 ?>
	@yizan_begin
		<yz:form id="yz_form" action="saveActivity">
			<yz:fitem label="活动类型">
				<yz:select name="type" options="$activitytype" textfield="name" valuefield="id" selected="$data['activitytype']"></yz:select>
			</yz:fitem>
			<yz:fitem name="name" label="活动名称"></yz:fitem>
			<yz:fitem name="beginTime" label="开始时间" type="date"></yz:fitem>
			<yz:fitem name="endTime" label="结束时间" type="date"></yz:fitem>
			<yz:fitem label="选择出售洗车券">
				<yz:select name="sellTicketId" options="$ticket" textfield="ticketName" valuefield="id" selected="$data['sellTicketId']"></yz:select>
				<yz:btn label="添加洗车券" click="location.href='{{ u('Promotion/addCarTicket') }}'"></yz:btn>
			</yz:fitem>
			<yz:fitem name="sellTicketNum" label="出售洗车券数量" append="1">
				<span class="ts">张</span>
			</yz:fitem>
			<yz:fitem label="赠送洗车券">
				<yz:select name="giftTicketId" options="$ticketall" textfield="ticketName" valuefield="id" selected="$data['giftTicketId']"></yz:select>
			</yz:fitem>
			<yz:fitem name="giftTicketNum" label="另赠洗车券数量" append="1">
				<span class="ts">张</span>
			</yz:fitem>
			<yz:fitem name="price" label="促销价格" append="1">
				<span class="ts">元</span>
			</yz:fitem>
		</yz:form>
	@yizan_end
@stop
@section('js')
<script type="text/javascript">
</script>
@stop
