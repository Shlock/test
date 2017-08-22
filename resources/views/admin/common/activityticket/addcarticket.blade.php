@extends('admin._layouts.base')
@section('css')
<style type="text/css">
	.ts{color: #999;margin-left: 5px;vertical-align:middle;}
</style>
@stop
@section('right_content')
<?php 
	$conditionType = [
		['type'=>'all','name'=>'全车洗'],
		['type'=>'body','name'=>'洗车身']
	];

	$cleaningtype = [
		['type'=>'offset','name'=>'服务抵用券'],
		['type'=>'money','name'=>'服务优惠券']
	];

	$timetype = [
		['id'=>1,'name'=>'使用有效期'],
		['id'=>2,'name'=>'固定有效期']
	];
 ?>
	@yizan_begin
		<yz:form id="yz_form" action="saveCarTicket">
			<yz:fitem name="name" label="洗车券名称"></yz:fitem>
			<yz:fitem label="适用范围">
				<yz:select name="conditionType" options="$conditionType" textfield="name" valuefield="type" selected="$data['conditionType']"></yz:select>
			</yz:fitem>
			<yz:fitem label="洗车券类型">
				<yz:radio name="type" options="$cleaningtype" valuefield="type" css="send-type" textfield="name" checked="offset"></yz:radio>
			</yz:fitem>
			<yz:fitem name="data" label="洗车券面值" append="1">
				<span class="ts">元</span>
			</yz:fitem>
			<yz:fitem name="sellPrice" label="洗车券出售金额" append="1">
				<span class="ts">元</span>
			</yz:fitem>
			<yz:fitem label="有效时间设定">
				<yz:radio name="timetype" options="$timetype" valuefield="id" css="send-type" textfield="name" checked="1"></yz:radio>
			</yz:fitem>
			<yz:fitem pid="timetype1" label="使用有效期" css="hidden">
				<div>
					购买之日起<input type="text" name="expireDay" class="u-ipttext ml5 mr5" style="width:100px;">日有效
				</div>
			</yz:fitem>
			<yz:fitem pid="timetype2" pstyle="display:none" name="beginTime" label="固定有效期开始" type="date"></yz:fitem>
			<yz:fitem pid="timetype3" pstyle="display:none" name="endTime" label="结束" type="date"></yz:fitem>
			<yz:fitem label="状态">
				<yz:radio name="status" options="1,0" texts="启用,禁用" checked="1"></yz:radio>
			</yz:fitem>
			<yz:fitem name="sort" label="排序" val="100"></yz:fitem>
		</yz:form>
	@yizan_end
@stop
@section('js')
<script type="text/javascript">
	$(function(){
		$("input:radio[name='timetype']").change(function(){
				if( $(this).val() == 1 ) {
					$('#timetype2').hide();
					$('#timetype3').hide();
					$('#timetype1').show();
				}else{
					$('#timetype1').hide();
					$('#timetype2').show();
					$('#timetype3').show();
				}
			});
	})
</script>
@stop
