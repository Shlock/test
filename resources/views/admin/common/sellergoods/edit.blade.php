@extends('admin._layouts.base')
@section('css')
<style type="text/css">
	#searchSeller{margin-left: 5px;}
	#mobile{width: 100px;}
	.setprice{width: 60px;margin: 0px 5px;}
	.allprice{margin-left: 20px; color: #999;}
	.ts,.ts3{color: #999;margin-left: 5px;vertical-align:middle;}
</style>
@stop
@section('right_content')
	@yizan_begin
		<yz:form id="yz_form" action="save">
			<yz:fitem name="name" label="服务名称"></yz:fitem>
			<yz:fitem name="mobile" label="服务人员" append="1" val="{{$data['seller']['mobile']}}" attr="maxlength='11'">
				<yz:select name="sellerId" options="0" texts="请输入手机号或昵称" style="180px;"></yz:select>
				<yz:btn label="搜索" id="searchSeller"></yz:btn>
				<span class="ts ts2"></span>
			</yz:fitem>
			<yz:fitem label="服务计费">
				<php> $priceType = $data['priceType'] ? $data['priceType'] : 1 ; </php>
				<yz:radio name="priceType" options="1,2" texts="按次计费,按时计费" checked="$priceType"></yz:radio>
			</yz:fitem>
			<yz:fitem label="按次计费" pstyle='display:none' pid="ci">
				<p>每次<input type="text" class="u-ipttext setprice" id="setprice_hour" placeholder="0"  onkeyup="if(isNaN(value))execCommand('undo')" onafterpaste="if(isNaN(value))execCommand('undo')" name="duration" value="{{$data['duration']}}">小时</p>
				<p>
					每次<input type="text" class="u-ipttext setprice" id="setprice_money" placeholder="0"  onkeyup="if(isNaN(value))execCommand('undo')" onafterpaste="if(isNaN(value))execCommand('undo')" name="price" value="{{$data['price']}}">元
					<span class="allprice">总计：<span class="ts4">{{ $data['duration'] or 0 }}</span>小时<span class="ts5">{{ $data['price'] or 0 }}</span>元 / 次</span>
				</p>
			</yz:fitem>
			<yz:fitem label="按时计费" pstyle='display:none' pid="shi">
				<p>
					<input type="text" class="u-ipttext setprice" placeholder="0"  onkeyup="if(isNaN(value))execCommand('undo')" onafterpaste="if(isNaN(value))execCommand('undo')" name="_price" id="setprice_price" value="{{$data['price']}}">元/小时
				</p>
			</yz:fitem>
			<yz:fitem name="marketPrice" label="门店价格"></yz:fitem>
			<yz:fitem label="分类">
				<yz:select name="cateId" options="$cate" textfield="levelname" valuefield="id" selected="$data['cate']['id']"></yz:select>
				<span class="ts ts1"></span>
			</yz:fitem>
			<yz:fitem label="简介" name="brief" type="textarea"></yz:fitem>
			<yz:fitem label="图片">
				<yz:imageList name="images." images="$data['images']"></yz:imageList>
			</yz:fitem>
			<yz:fitem name="sort" label="排序" val="100"></yz:fitem>
		</yz:form>
	@yizan_end
@stop
@section('js')
<script type="text/javascript">
var cate = eval( <?php echo json_encode($cate); ?> );
var editId = "<?php if(isset($data['seller']['id'])){ echo $data['seller']['id'];} ?>";
var cateId = "<?php if(isset($data['cate']['id'])){ echo $data['cate']['id'];} ?>";
var priceType = "{{ $priceType }}";
	$(function(){
		if( priceType == 1 ) {
			$("#ci").show();
		}
		else if(  priceType == 2 ) {
			$("#shi").show();
		}

		$("input[name='priceType']").change(function(){
			//按次计费
			if( $(this).val() == 1 ){
				$("#shi").hide();
				$("#ci").show();
			}
			//按时计费
			else{
				$("#ci").hide();
				$("#shi").show();
			}
		});

		$('#setprice_hour').blur(function(){
			var hour = $(this).val();
			if( hour > 0 ) {
				$('.ts4').text( hour );
			}else{
				$('.ts4').text( 0 );
			}
		});

		$('#setprice_money').blur(function(){
			var money = $(this).val();
			if( money > 0 ) {
				$('.ts5').text( money );
			}else{
				$('.ts5').text( 0 );
			}
			$('.city_price_box input.price').val(money);
		});
		
		$('#setprice_price').blur(function(){
			$('.city_price_box input.price').val( $(this).val() );
		});


		if( editId > 0 ) {
			$("#sellerId").html("<option value='"+editId+"' selected>{{$data['seller']['name']}}</option>");
			$('.ts1').text( cate[cateId]['levelrel'] );
		}

		$('#cateId').change(function(){
			$('.ts1').text( cate[$(this).val()]['levelrel'] );
		});

			$('#searchSeller').click(function(){
				clearts();
				var mobileName = $('#mobile').val();
				$.post("{{u('Order/getSellerInfo')}}",{"mobileName":mobileName},function(res){
					res = eval(res); 
					if(res.length>0){
						var html = "";
						$.each(res,function(n,value) {
							if(n<1){
								$('#mobile').val(value.mobile);
							}
							html += "<option value='"+value.id+"' data-mobile='"+value.mobile+"'>"+value.name+"</option>";  
						});
						$("#sellerId").html(html);
					}else{
						$("#sellerId").html("<option value='0'>请输入手机号或昵称</option>");
						$(".ts2").text('未查询到相关服务人员');
					}
					

				});
			});

			$("#sellerId").change(function(){
				$('#mobile').val( $("#sellerId option:checked").data('mobile') );
			});
	})

	function clearts() {
		$('.ts').text('');
	}
</script>
@stop
