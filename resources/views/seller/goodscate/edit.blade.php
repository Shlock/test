@extends('seller._layouts.base')
@section('css')
<style type="text/css">
	.m-tab table tbody td{padding: 5px 0px;}
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
				<!-- 服务管理 -->
				<div class="g-fwgl">
					<p class="f-bhtt f14 clearfix">
						<span class="ml15 fl">分类管理</span>
					</p>
				</div>
				<!-- 服务表格 -->
				<div class="m-tab m-smfw-ser pt20">
					@yizan_begin
	                    <yz:form id="yz_form" action="save">
							<yz:fitem name="name" label="分类名称" attr="maxlength='20'"></yz:fitem>
							<div id="seller-cate-form-item" class="u-fitem clearfix ">
					            <span class="f-tt">
					                 所属行业分类:
					            </span>
					            <div class="f-boxr">
				                  	<select id="tradeId" name="tradeId" style="min-width:234px;width:auto" class="sle  ">
				                  		@foreach($cate as $item)
				                  		<optgroup label="{{$item['name']}}">
				                  			@if($item['childs'])
				                  			@foreach($item['childs'] as $child)
					                  		<option data-type={{$child['type']}} value="{{$child['id']}}" @if($data['tradeId'] == $child['id'])selected="selected"@endif>{{$child['name']}}</option>
				                  			@endforeach
				                  			@else
											<option data-type={{$item['type']}} value="{{$item['id']}}" @if($data['tradeId'] == $item['id'])selected="selected"@endif>{{$item['name']}}</option>
				                  			@endif
				                  		</optgroup>	                	
					                  	@endforeach
									</select>
									<span class="ts ts1"></span>
					            </div>
					        </div>
							<div id="type-form-item" class="u-fitem clearfix ">
					            <span class="f-tt">
					                 类型:
					            </span>
					            <div class="f-boxr">
					            	<label id="type_label" style="margin-left:10px;">商品</label>
					                <input type="hidden" name="type" id="type" class="u-ipttext" value="100">
					            </div>
					        </div>
							<yz:fitem name="img" label="图标" type="image"></yz:fitem>
							<yz:fitem name="sort" label="排序" val="100"></yz:fitem>
							<yz:fitem label="状态">
								<php> $status = isset($data['status']) ? $data['status'] : 1 </php>
								<yz:radio name="status" options="1,0" texts="开启,关闭" checked="$status"></yz:radio>
							</yz:fitem>
							@if($checkShowBatPost)
							<div class="clearfix">
								<span class="f-tt">批量同步菜场:</span>

								<span class="f-boxr">
									<div class="search-selle-wrap">

										<input type="text" id="search-seller-input" class="u-ipttext">
										<button class="btn" type="button" style="width:60px;" id="search-seller-btn">搜索</button>
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
	<script type="text/javascript">
	$(function(){
		$("#tradeId").change(function(){
			var type = $(this).find("option:selected").data('type');
			if(type == 1){
				$("#type_label").text('商品');
			} else {
				$("#type_label").text('服务'); 
			}
			$("#type").val(type);
		}).trigger('change');

		// 同步菜场功能

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

		$('#search-seller-input').keyup(function(){
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
	});
	</script>
@stop
 