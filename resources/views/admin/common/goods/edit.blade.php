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
</style>
@stop
@section('right_content')
	@yizan_begin
		<yz:form id="yz_form" action="save">
			<yz:fitem name="name" label="服务名称"></yz:fitem>
			<yz:fitem label="服务计费">
				<php> $priceType = $data['priceType'] ? $data['priceType'] : 1 ; </php>
				<yz:radio name="priceType" options="1,2" texts="按次计费,按时计费" checked="$priceType"></yz:radio>
			</yz:fitem>
			<yz:fitem label="按次计费" pstyle='display:none' pid="ci">
				<p>单次服务用时<input type="text" class="u-ipttext setprice" id="setprice_hour" placeholder="0"  onkeyup="if(isNaN(value))execCommand('undo')" onafterpaste="if(isNaN(value))execCommand('undo')" name="duration" value="{{$data['duration']/3600}}">小时</p>
				<p>
					单次服务价格<input type="text" class="u-ipttext setprice" id="setprice_money"  onkeyup="if(isNaN(value))execCommand('undo')" onafterpaste="if(isNaN(value))execCommand('undo')" name="price" value="{{$data['price'] or 0 }}">元
				</p>
			</yz:fitem>
			<yz:fitem label="按时计费" pstyle='display:none' pid="shi">
				<p>
					<input type="text" class="u-ipttext setprice" onkeyup="if(isNaN(value))execCommand('undo')" onafterpaste="if(isNaN(value))execCommand('undo')" name="_price" id="setprice_price" value="{{$data['price'] or 0}}">元/小时
				</p>
			</yz:fitem>
			<yz:fitem label="门店价格">
				<input type="text" class="u-ipttext" placeholder="0"  onkeyup="if(isNaN(value))execCommand('undo')" onafterpaste="if(isNaN(value))execCommand('undo')" name="marketPrice" id="marketPrice" value="{{$data['marketPrice']}}">
				<span class="ts">元</span>
			</yz:fitem>
			<yz:fitem label="分类">
				<yz:select name="cateId" options="$cate" textfield="levelname" valuefield="id" selected="$data['cate']['id']"></yz:select>
				<span class="ts ts1"></span>
			</yz:fitem>
			<yz:fitem name="sort" label="排序" val="100"></yz:fitem>
			<yz:fitem label="图片">
				<yz:imageList name="images." images="$data['images']"></yz:imageList>
			</yz:fitem>
			<yz:fitem label="简介" name="brief" type="textarea"></yz:fitem>
			<yz:fitem name="detail" label="详细"> 
				<yz:Editor name="detail" value="{{ $data['detail'] }}"></yz:Editor> 
			</yz:fitem>
			<!-- <yz:fitem label="城市价格">
				<ul id="setcity_price" class="clearfix">
					@foreach( $data['cityPrices'] as $key => $value)
					<li style="float:left; border:solid 1px #ccc; padding:5px; margin:0 10px 10px 0;" class="city_price_box">
						{{$value['city']['name']}} 
						<input type="text" name="cityPrices[{!! $value['city']['id'] !!}][price]" value="{{ $value['price'] or 0 }}" class="u-ipttext price" placeholder='服务价格' style="width:60px;">
						<span class="ts">元</span>
						<input type="text" name="cityPrices[{!! $value['city']['id'] !!}][marketPrice]" value="{{ $value['marketPrice'] or 0 }}" class="u-ipttext marketPrice" placeholder='门店价格' style="width:60px;">
						<span class="ts">元</span>
					</li>
					@endforeach
				</ul>
			</yz:fitem> -->
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

		if( editId > 0 ) {
			$("#sellerId").html("<option value='"+editId+"' selected>{{$data['seller']['name']}}</option>");
			$('.ts1').text( cate[cateId]['levelrel'] );
		}

		$('#cateId').change(function(){
			$('.ts1').text( cate[$(this).val()]['levelrel'] );
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
		$('#marketPrice').blur(function(){
			$('.city_price_box input.marketPrice').val( $(this).val() );
		});
	})

	function clearts() {
		$('.ts').text('');
	}

</script>
@stop
