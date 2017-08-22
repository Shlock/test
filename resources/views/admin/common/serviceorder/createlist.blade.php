@extends('admin._layouts.base')
@section('css')
<style type="text/css">
	select#staffId{width: auto;}
	#pagebtn{margin-right: 10px;margin-top: 2px;cursor: pointer;}
	#userInfo{min-width: 130px;width: auto}
	#showpromotionSn{min-width: 100px;width: auto;}
	.pointer{cursor: pointer;}
	.ts{color: red}
	.clear{clear: both;}
	.gray-ts{margin-left: 5px;color: gray;}
</style>
<script type="text/javascript">
	var sellerId = 0;
	var checkDay = '';
</script>
@stop
@section('right_content')
<?php 
	for ($i=1; $i<=24; $i++ ) {
		$duration[$i]['name'] = $i;
		$duration[$i]['val'] = $i;
	}
 ?>
	@yizan_begin
		<yz:form id="yz_form" action="save">
			<yz:fitem name="showgoodsname" label="选择服务">
				<yz:btn label="选择服务" id="searchgoods"></yz:btn>
				<span class="ts ts1"></span>
			</yz:fitem>
			<yz:fitem name="goodsName" label="服务名称" attr="readonly='true'"></yz:fitem>
			<yz:fitem name="sellerName" label="机构名称" attr="readonly='true'"></yz:fitem>
			<yz:fitem name="duration1" label="服务时长" attr="readonly='true'" append="1" pstyle="display:none" pid="priceType1">
				<span class="ml5">小时 <span class="gray-ts">（按次计费）</span></span>
			</yz:fitem>
			<yz:fitem label="服务时长" pstyle="display:none" pid="priceType2">
				<yz:select name="duration2" options="$duration" textfield="name" valuefield="val"></yz:select>
				<span class="ml5">小时 <span class="gray-ts">（按小时计费）</span></span>
			</yz:fitem>
			<yz:fitem label="服务员工" pstyle="display:none" pid="staff_box">
				<yz:select name="staffId" options="$staff" textfield="name" valuefield="id"></yz:select>
			</yz:fitem>
			<yz:fitem name="showusername"  label="选择会员"  append="1">
				<yz:select name="userInfo" options="0" texts="请输入会员昵称"></yz:select>
				<yz:btn label="搜索会员" id="searchuser"></yz:btn>
				<span class="ts ts2"></span>
			</yz:fitem>
			<yz:fitem name="userName" label="联系人"></yz:fitem>
			<yz:fitem name="mobile" label="联系电话" attr="maxlength='11'"></yz:fitem>
			<yz:fitem label="联系地址">
				<yz:map name="address"></yz:map>
			</yz:fitem>
			<yz:fitem label="预约时间管理">
				<div class="clearfix">	            
		            <div class="f-boxr"> 
		           		@include('admin.common.order.time')
		            </div>
				</div>
			</yz:fitem>
			<yz:fitem type="textarea" label="备注" name="remark"></yz:fitem>
			<yz:fitem label="服务价格" attr="readonly='true'" val="0" name="price" append="1">
				<span class="ml5">元</span>
				<span class="ml5" id="marketPrice"></span>
			</yz:fitem>
			<yz:fitem label="服务费用" val="0" name="serviceFee" append="1">
				<span class="ml5">元</span>
			</yz:fitem>
			<yz:fitem label="优惠券" name="promotionSn" append="1" attr="readonly='true'">
				<yz:select name="showpromotionSn" options="0" texts="不使用" attr="onchange='promotion(this.value)'"></yz:select>
				<yz:btn label="查询优惠券" id="searpromotionSn"></yz:btn>
				<span class="ts ts3"></span>
			</yz:fitem>
			<yz:fitem label="优惠价格" attr="readonly='true'" val="0" name="youhuiprice" append="1">
				<span class="ml5">元</span>
			</yz:fitem>

			<yz:fitem name="userId" type="hidden"></yz:fitem>
			<yz:fitem name="sellerId" type="hidden"></yz:fitem>
			<yz:fitem name="goodsId" type="hidden"></yz:fitem>
			<yz:fitem name="appointTime2" type="hidden"></yz:fitem>
			<yz:fitem name="priceType" type="hidden"></yz:fitem>
		</yz:form>
	@yizan_end
@stop

@section('js')
<script type="text/javascript">
	$(function(){
		//搜索服务
		$('#searchgoods').click(function(){
			clearts();
			$.zydialogs.open("{{ u('Goods/search') }}", {
	            boxid:'REPLY_RELATE_WEEBOX',
	            width:750,
	            title:'选择服务',
	            contentType:'ajax',
	            showLoading:true
	        });
		});

		//搜索会员
		$('#searchuser').click(function(){
			clearts();
			var mobileName = $('#showusername').val();
			$.post("{{u('Order/getUserInfo')}}",{"mobileName":mobileName},function(res){
				res = eval(res);
				if(res.length>0){
					var html = "";
					$.each(res,function(n,value) {
						if(n<1){
							$("#userName").val(value.name);
							$("#mobile").val(value.mobile);
							$("#showusername").val(value.mobile);
							$("#map-address-1").val(value.address);
							$("#userId").val(value.id);
						}
						if(value.address){
							var add = value.address.address;
						}
						html += "<option value='"+value.id+"' data-mobile='"+ value.mobile +"' data-address='"+ add +"'>"+value.name+"</option>";  
					});
					$("#userInfo").html(html);
				}else{
					$("#userInfo").html("<option value='0'>请输入会员昵称</option>");
					$(".ts2").text('未查询到相关会员');
				}
			});
		});

		//联系人
		$("#userInfo").change(function(){
			$("#userName").val($(this).find("option:selected").text());
			$("#mobile").val($(this).find("option:selected").data('mobile'));
			$("#showusername").val($(this).find("option:selected").data('mobile'));
			$("#map-address-1").val($(this).find("option:selected").data('address'));
			$("#userId").val($(this).val());
		});

		//搜索优惠券
		$('#searpromotionSn').click(function(){
			clearts();
			var userId = $('#userId').val();
			var goodsId = $('#goodsId').val();
			if(goodsId==''){
				$('.ts3').text("请选择服务"); return false;
			}
			else if(userId==''){
				$('.ts3').text("请选择会员"); return false;
			}
			else{
				var html = "<option value='0'>不使用</option>";
				$.post("{{u('Order/getUserPromotion')}}",{"userId":userId,"goodsId":goodsId},function(res){
					res = eval(res);
					if(res.length>0){
						$.each(res,function(n,value) {
							var promotion = value.promotion;
							html += "<option value='"+value.id+"' data-price='"+ promotion.data +"' data-sn='"+value.sn+"'>"+promotion.name+"</option>";  
						});
						$("#showpromotionSn").html(html);
					}
				});
			}
			
		});

		//联系人
		$("#showpromotionSn").change(function(){
			$("#promotionSn").val($(this).find("option:selected").data('sn'));
			$("#youhuiprice").val($(this).find("option:selected").data('price'));
		});

	});

	function clearts() {
		$('.ts').text('');
	}

	function yuyuetime(id) {
		var obj = $("#time-"+id);
		$(".yuetime  li").each(function(){
            if($(this).data("status") == 0){
                $(this).css({"color":'',"background":'#fff'}).removeClass('checked');
            }
        });
		obj.css({'background':'#ff5887','color':'#fff'}).addClass('checked');
		$('#appointTime2').val(obj.data('time'));
	}

	$.selectedGoods = function(id) {
		var goods = JSON.parse($("#goods-json-"+id).val());
		sellerId = goods.seller.id; // 提供给time.blade.php

		if (goods.priceType==1 || goods.priceType==2) {
			$("#priceType1").hide();
			$("#priceType2").hide();
			$("#priceType"+goods.priceType).show();
		}
		else {
			return false;
		}
		$('#goodsId').val(goods.id);
		$('#sellerId').val(goods.seller.id);
		$('#goodsName').val(goods.name);
		$('#price').val(goods.price);
		$('#marketPrice').text("(市场价："+goods.marketPrice+"元)");
		$('#sellerName').val(goods.seller.name);
		$('#duration1').val(goods.duration/3600);
		$("#priceType").val(goods.priceType);

		//获取该服务的服务人员
		if( goods.id > 0) {
			$.post("{{ u('Order/getStaffInfo') }}",{"goodsId":goods.id},function(res){
				if(res.data.length>0){
					var html = '';
					$.each(res.data,function(key,value){
						html += "<option value='"+value.id+"'>"+value.name+"</option>";
					});
					$("#staffId").html(html);
				}else{
					$("#staffId").html("<option value='0'>暂未查询到相关服务人员</option>");
				}
			},'json');
		}else{
			alert('服务不存在');
			return false;
		}
		
		$("#staff_box").show();
		$('.zydialog_close').click();
	}
</script>
@stop