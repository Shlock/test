@extends('admin._layouts.base')
@section('css')
<style type="text/css">
	#searchSeller{margin-left: 5px;}
	#mobile{width: 100px;}
	/*#setcity_price{margin-left: 110px;margin-top: -25px;}*/
	#setcity_price{width: 660px;}
	.setprice{width: 60px;margin: 0px 5px;}
	.allprice{margin-left: 20px; color: #999;}
	.ts,.ts3{color: #999;margin-left: 5px;vertical-align:middle;}
	.form-tip{background-color: #F9F9F9;padding: 10px 0px;margin-bottom: 10px;}
	.form-tip div.form-title{padding: 10px 0px 10px 43px;font-size: 18px; font-weight: bold;background-color: #eee; border: solid 1px #ccc;}
	.w80{width: 80px;}
	.w120{width: 120px;}
	.w150{width: 150px;}
	.cred{color: red}
	
	.cpsm-table textarea{height: 56px;}
	.fwbz-table textarea{height: 80px;}

	/*通用样式*/
	#yz_form table tr td{padding: 5px;}
	#yz_form table,#yz_form table tr th,#yz_form table tr td{border: 1px #ccc solid;text-align: center;}
	#processBody textarea{width: 120px;height: 80px;}

    .u-fitem{margin-bottom:0px; border: solid 1px #ccc; margin-top:-1px;}
    .m-spboxlst .f-boxr{padding: 10px; border-left: solid 1px #ccc;}
    .m-spboxlst .no-left-border  .f-boxr{border-left:none;}
    .m-spboxlst .f-tt{padding: 10px 5px 10px 0;}
</style>
@stop
@section('right_content')
<?php 
//预约方式
$makeType = $data['makeType'] == 3 ? [1,2] : $data['makeType'];
//服务人群
$servicePeople = $data['servicePeople'] == 2 ? [0,1] : $data['servicePeople'];
//服务承诺
foreach ($data['promise'] as $key => $value) {
	$promiseId[] = $value['promiseId'];
}
//等级列表处理
foreach ($data['pushMoneyWayList'] as $key => $value) {
	$pushMoneyWayList_new[$value['creditRankId']] = $value;
}
?>
	@yizan_begin
		<yz:form id="yz_form" action="#" nobtn = "false">
			<div class="form-tip">
			<div class="form-title form-title-1">基本信息</div>
				<yz:fitem label="服务分类">
					<yz:select name="goodsCate" options="$goodsCate" textfield="name" valuefield="id" selected="$data['catePid']"></yz:select>
					@if(isset($data) && $data['id'] > 0)
						<yz:select name="cateId" options="$data['cateSecondList']" textfield="name" valuefield="id" selected="$data['cateId']"></yz:select>
					@else
						<select name="cateId" id="cateId" class="sle">
							<option>选择二级分类</option>
						</select>
					@endif
				</yz:fitem>
				<yz:fitem label="预约方式">
					<yz:checkbox name="makeType." options="1,2" texts="上门服务,到店服务" checked="$makeType"></yz:checkbox>
				</yz:fitem>
				<yz:fitem name="name" label="服务名称"></yz:fitem>
				<yz:fitem name="brief" label="简介" type="textarea"></yz:fitem>
				<yz:fitem name="price" label="价格" append="1" css="price w80">
					<span class="ts">元</span>
				</yz:fitem>
				<yz:fitem name="marketPrice" label="市场价" append="1" css="price w80">
					<span class="ts">元</span>
				</yz:fitem>
				<yz:fitem name="subsidyPrice" label="平台补贴" append="1" css="price w80">
					<span class="ts">元</span>
				</yz:fitem>
				<yz:fitem label="服务图片">
					<yz:imageList name="images." images="$data['images']"></yz:imageList>
					<div><small class='cred pl10'>主图默认为第一张图</small></div>
				</yz:fitem>
				<yz:fitem name="keywords" label="自定义关键字" type="textarea"></yz:fitem>
			</div>

			<div class="form-tip">
			<div class="form-title">项目参数</div>
				<yz:fitem label="服务人群">
					<yz:checkbox name="servicePeople." options="1,0" texts="男,女" checked="$servicePeople"></yz:checkbox>
				</yz:fitem>
				<yz:fitem label="适用肤质">
					<yz:select name="fitSkinId" options="$fitSkin" textfield="name" valuefield="id" selected="$data['fitSkinId']"></yz:select>
				</yz:fitem>
				<yz:fitem name="functionExplain" label="功效说明" type="textarea"></yz:fitem>
				<yz:fitem name="notice" label="注意事项" type="textarea"></yz:fitem>
			</div>

			<div class="form-tip">
			<div class="form-title">服务流程</div>
				<yz:fitem label="服务步骤">
					@include('admin._layouts.goods_service_process_form')
				</yz:fitem>
				<yz:fitem name="serverAllTime" label="服务总时长" append="1" css="w80" attr="readonly">
					<span class="ts">分钟</span>
				</yz:fitem>
			</div>

			<div class="form-tip">
			<div class="form-title">配套产品(产品说明)</div>
				<yz:fitem pcss="no-left-border" notitle="1">
					@include('admin._layouts.goods_service_mating_form')
				</yz:fitem>
			</div>

			<div class="form-tip">
			<div class="form-title">其他信息</div>
				<yz:fitem label="服务承诺">
					<yz:checkbox name="promise." options="$promise" textfield="name" valuefield="id" checked="$promiseId"></yz:checkbox>
				</yz:fitem>
				<yz:fitem label="提成方式">
					<yz:select name="pushMoneyWay" options="1,2" texts="按金额提成,按比例提成" selected="$data['pushMoneyWay']"></yz:select>
					<div class="mt10">
						<table class="table tcfs-table">
							<tr>
								<th>技师等级</th><th>全职提成<span class='thname'>金额</span></th><th>兼职提成<span class='thname'>金额</span></th>
							</tr>
							@foreach($staffLevel as $key => $value)
								<tr>
									<td>
										<img src="{{ $value['icon'] }}" alt="">（{{$value['name']}}）
									</td>
									<input type="text" value="{{ $value['id'] }}" name="levelId[]" style="display:none">	
									<td>
										<input name="fullTime[]" type="text" class="u-ipttext w80 tichengjg price" 
										@if($data['pushMoneyWay'] == 1)
											value="{{ $pushMoneyWayList_new[$value['id']]['allTimeMoney'] }}"
										@elseif($data['pushMoneyWay'] == 2)
											value="{{ $pushMoneyWayList_new[$value['id']]['allTimeScale'] }}"
										@endif
										placeholder="编辑价格">
										<span class="ts ticheng">元</span>
									</td>
									<td>
										<input name="partTime[]" type="text" class="u-ipttext w80 tichengjg price"
										@if($data['pushMoneyWay'] == 1)
											value="{{ $pushMoneyWayList_new[$value['id']]['partTimeMoney'] }}"
										@elseif($data['pushMoneyWay'] == 2)
											value="{{ $pushMoneyWayList_new[$value['id']]['partTimeScale'] }}"
										@endif
										 placeholder="编辑价格">
										<span class="ts ticheng">元</span>
									</td>
								</tr>
							@endforeach
						</table>
					</div>
				</yz:fitem>
			</div>
		</yz:form>
	@yizan_end
@stop
@section('js')
<script type="text/javascript">
var pushMoneyWay = "{{$data['pushMoneyWay']}}";
	$(function(){
		//金额只能输入数字和小数点
		$(".price").keyup(function(){
			if(isNaN(this.value))$(this).val('')
		});
		$(".number").keyup(function(){
			$(this).val(this.value.replace(/\D/g,''))
		});

		//通过一级分类查找二级分类
		$("#goodsCate").change(function(){
			var pid = $(this).val();
			$("#cateId").html("<option>选择二级分类</option>");
			if(pid < 1){
				return false;
			}
			$.post("{{ u('Goods/selectSecond') }}",{'pid':pid,'status':1},function(res){
				if(res.length > 0){
					var html = "";
					$.each(res, function(k,v){
						html += "<option value='"+this.id+"'>"+this.name+"</option>";
					});
					$("#cateId").html(html);
				}
			},'json');
		});

		//按金额提成，按比例提成 进入
		if(pushMoneyWay == 1){
			$(".ticheng").text("元");
			$(".tichengjg").attr("placeholder","编辑价格");
			$(".thname").text("金额");
		}
		else if(pushMoneyWay == 2){
			$(".ticheng").text("%");
			$(".tichengjg").attr("placeholder","编辑比例");
			$(".thname").text("比例");
		}


		//按金额提成，按比例提成 切换
		$("#pushMoneyWay").change(function(){
			if($(this).val() == 1){
				$(".ticheng").text("元");
				$(".tichengjg").attr("placeholder","编辑价格");
				$(".thname").text("金额");
			}
			else if($(this).val()){
				$(".ticheng").text("%");
				$(".tichengjg").attr("placeholder","编辑比例");
				$(".thname").text("比例");
			}
		});

	});
</script>
@stop