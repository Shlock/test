@extends('seller._layouts.base')
@section('css')
<link rel="stylesheet" type="text/css" href="{{ asset('css/jquery.tagsinput.css') }}">
<style type="text/css">
	#cateSave{display: none;}
	.page_2,.page_3{display: none;}
	.m-spboxlst li{margin-bottom: 0px;}
	#tags_goods-form-item .f-boxr {width:550px;} 
	#cateSave{display: none;}
	.page_2,.page_3{display: none;}
	.m-spboxlst li{margin-bottom: 0px;}
	#tags_goods-form-item .f-boxr {width:550px;}
	.f-boxr .btn{background: #efefef; border-color: #dfdfdf; width: 80px; color: #555;}
	.x-gebox{border: 1px solid #ddd; padding: 5px 20px;}
	.x-gebox .u-ipttext{width: 100px; margin-right: 10px;}
	.closege{width: 20px; height: 20px; background: url({{ asset("wap/community/client/images/ico/close.png") }}); background-size: 100% 100%; display: inline-block; cursor: pointer; vertical-align: middle; margin-top: -2px;}
	#all-sellers, #selected-sellers {
		display: inline-block;
		width: 200px;
		height: 400px;
		vertical-align: middle;
	}
	.bat-post-buttons {
		display: inline-block;
		vertical-align: middle;

	}
	.bat-post-buttons button {
		display: block;
		width: 20px;
		height: 20px;
		border: 1px solid #000;
		margin-bottom: 20px;
	}
	.search-selle-wrap {
		margin-bottom: 20px;
	}
</style> 
@stop 
@section('content')
	<div>
		<div class="m-zjgltbg">					
			<div class="p10">
				<!-- 添加商品 -->
				<div class="g-fwgl">
					<p class="f-bhtt f14 clearfix">
						<span class="ml15 fl">添加商品</span>
					</p>
				</div> 
				<div class="m-tab m-smfw-ser pt20">
					@yizan_begin
	                    <yz:form id="yz_form" action="save">  
							<yz:fitem name="name" label="商品名称" required="true"></yz:fitem> 
							<yz:fitem label="商品分类">
								<yz:select name="cateId" options="$cate" textfield="name" valuefield="id" selected="$data['cate']['id']"></yz:select>
							</yz:fitem>
							<yz:fitem name="price" label="价格"  required="true"></yz:fitem>
							<yz:fitem name="weight" label="重量"  required="true"></yz:fitem> 
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
										重量：<input type="text" name="_weight[]" value="{{$item['weight']}}" class="u-ipttext" />
										库存：<input type="text" name="_stock[]" value="{{$item['stock']}}" class="u-ipttext" />
										<i class="closege"></i>
					                </div>
					            	@endforeach
					            </div>
					        </div>
							<div id="-form-item" class="u-fitem clearfix ">
							<yz:fitem label="商品图片">
								<yz:imageList name="images." images="$data['images']" required="true"></yz:imageList>
							</yz:fitem> 
							<yz:fitem name="buyLimit" label="每人限购"></yz:fitem>
                                <yz:fitem name="brief" label="商品描述"  required="true">
                                    <yz:Editor name="brief" value="{{ $data['brief'] }}" required="true"></yz:Editor>
                                </yz:fitem>

							</yz:fitem>    
							<yz:fitem label="商品状态"> 
								<php> $status = (int)$data['status'] </php>
								<yz:radio name="status" options="0,1" texts="下架,上架" checked="$status"></yz:radio>
							</yz:fitem> 
							<yz:fitem name="sort" label="排序"></yz:fitem>

							@if($checkShowBatPost)
							<div class="clearfix">
								<span class="f-tt">批量同步菜场:</span>

								<span class="f-boxr">
									<div class="search-selle-wrap">

										<input type="text" id="search-seller-input" class="u-ipttext">
										<button class="btn" type="button" id="search-seller-btn">搜索</button>
									</div>
									<select name="" id="all-sellers" multiple>
									</select>

									<div class="bat-post-buttons">
										<button type="button" class="l-to-r btn btn-gray" style="height:35px;width:40px;font-weight:bold;">></button>
										<button type="button" class="r-to-l btn btn-gray" style="height:35px;width:40px;font-weight:bold;"><</button>
									</div>
									<select name="bat-post-sellers[]" id="selected-sellers" multiple>

									</select>
								</span>
							</div>
							@endif
						</yz:form>
	                @yizan_end
				</div>
			</div>
		</div>
	</div>
@stop
@section('js')
@include('seller._layouts.alert')
<script src="{{ asset('js/jquery.tagsinput.min.js') }}"></script>  
<script type="text/tpl" id="normsrow"> 
	<div class="x-gebox" style="margin-top:3px;">
		型号：<input type="text" name="_name[]" class="u-ipttext" />
		价格：<input type="text" name="_price[]" class="u-ipttext" />
		重量：<input type="text" name="_weight[]" class="u-ipttext" />
		库存：<input type="text" name="_stock[]" class="u-ipttext" />
		<i class="closege"></i>
    </div>
</script> 
<script type="text/javascript">
	$(function () {
		$(".add_norms").click(function(){
			$(".norms_panel").append($("#normsrow").html());
			if($(".x-gebox").length > 0){
				$(".norms_panel").parent().show();
			}
		});
		$(document).on('click','.closege',function(){
			$(this).parent().remove();
			if($(".x-gebox").length <= 0){
				$(".norms_panel").parent().hide();
			}

		});
		$('.r-to-l').on('click', function () {
			var options = $('#selected-sellers').find(':checked');
			options.remove();

			$('#all-sellers').append(options);


		});
		$('.l-to-r').on('click', function () {
			var options = $('#all-sellers').find(':checked');
			options.remove();
			$('#selected-sellers').append(options);
		});
		var allSeller = {!! $allSeller  !!};
		renderSeller(allSeller);
		$('#search-seller-btn').on('click', function () {
			var newArr = [];
			var value = $('#search-seller-input').val();
			allSeller.forEach(function (item) {

				if (~item.name.indexOf(value)) {
					newArr.push(item);
				}
			});
			renderSeller(newArr);
		});
		$('#search-seller-input').keyup(function () {
			var newArr = [];
			var value = $('#search-seller-input').val();
			allSeller.forEach(function (item) {

				if (~item.name.indexOf(value)) {
					newArr.push(item);
				}
			});
			renderSeller(newArr);
		});
		function renderSeller (arr) {
			var div = $('<select>');
			arr.forEach(function (item) {
				var option = $('<option value="' + item.id +  '">' + item.name + '</option>');
				div.append(option);
			});
			$('#all-sellers').html(div.html());

		}


	})


</script>
@stop