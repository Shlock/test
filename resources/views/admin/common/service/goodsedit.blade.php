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
		<yz:form id="yz_form" action="serviceSave">  
			<input type="hidden" name="type" value="{{$data['type']}}" />
			<input type="hidden" name="sellerId" value="{{$data['sellerId']}}" />
			<yz:fitem name="name" label="商品标题"></yz:fitem>
			<yz:fitem label="商品分类">
				<yz:select name="cateId" options="$cate" textfield="name" valuefield="id" selected="$data['cate']['id']"></yz:select>
			</yz:fitem>
			<yz:fitem name="price" label="价格"></yz:fitem>
			<yz:fitem name="stock" label="库存" val="0"></yz:fitem>
			<!--yz:fitem name="totalStock" attr="readonly" label="总库存" val="0"></yz:fitem-->
			<div id="norms-form-item" class="u-fitem clearfix">
	            <span class="f-tt">
	                 规格型号:
	            </span>
	            <div class="f-boxr">
	                <button type="button" class="btn addge add_norms">添加规格</button>
	            </div>
	        </div>
	        <div id="norms-form-item" class="u-fitem clearfix x-addge">
	            <span class="f-tt">&nbsp;</span>
	            <div class="f-boxr norms_panel">
	           	 	@foreach($data['norms'] as $item)
	                <div class="x-gebox">
	                	<input type="hidden" name="_id[]" value="{{$item['id']}}" >
						型号：<input type="text" name="_name[]" value="{{$item['name']}}" class="u-ipttext" />
						价格：<input type="text" name="_price[]" value="{{$item['price']}}" class="u-ipttext" />
						库存：<input type="text" name="_stock[]" value="{{$item['stock']}}" class="u-ipttext" />
						<i class="closege"></i>
	                </div>
	            	@endforeach
	            </div>
	        </div>
			<div id="-form-item" class="u-fitem clearfix ">
			<yz:fitem label="商品图片">
				<yz:imageList name="images." images="$data['images']"></yz:imageList>
			</yz:fitem>
			<yz:fitem name="buyLimit" label="每人限购"></yz:fitem>
			<yz:fitem name="brief" label="商品描述">
				<yz:Editor name="brief" value="{{ $data['brief'] }}"></yz:Editor>
			</yz:fitem>
			<yz:fitem label="商品状态">
				<php> $status = (int)$data['status'] </php>
				<yz:radio name="status" options="0,1" texts="下架,上架" checked="$status"></yz:radio>
			</yz:fitem>
			<yz:fitem name="sort" label="排序"></yz:fitem>
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
