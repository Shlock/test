@extends('seller._layouts.base')
@section('css')
<link rel="stylesheet" type="text/css" href="{{ asset('css/jquery.tagsinput.css') }}">
<style type="text/css">
	#cateSave{display: none;}
	.page_2,.page_3{display: none;}
	.m-spboxlst li{margin-bottom: 0px;}
	#tags_goods-form-item .f-boxr {width:550px;}
</style>
@stop 
@section('content')
	@yizan_begin
	<yz:form id="yz_form" action="save" nobtn="1">
		<!-- 个人版本 -->
		<!-- 第一页 -->
		<div class="pageBox page_1">
			<div class="m-zjgltbg">
				<div class="p10">						
					<p class="f-bhtt f14 clearfix">
						<span class="ml15 fl">编辑流程</span>
					</p>
					<div class="m-quyu1" style="padding-bottom:0;">
						<p class="tc"><img src="{{ u('images/fw1.png') }}" alt=""></p>
					</div>
					<!-- 设置服务种类 -->
					<p class="lh45">
						设置服务种类
					</p>
					<div class="g-szzllst pt10">
						<!-- 如果是从通用服务库获取的信息则只是显示不提交 -->
						@if(isset($systemGoods) && !empty($systemGoods))
						<input type="hidden" name="systemGoodsId" value="{{$systemGoods['id']}}" >
						<yz:fitem name="name" label="服务标题" attr="disabled" val="{{$systemGoods['name']}}"></yz:fitem>

						<yz:fitem label="服务标签" id="tagsgoods" name="goodsTags" attr="disabled" type="textarea" val="{{$systemGoods['goodsTags']}}" ></yz:fitem>

						<yz:fitem label="服务分类">
							<yz:select name="cateId" options="$cate" attr="disabled" textfield="levelname" valuefield="id" selected="$systemGoods['cate']['id']"></yz:select>
						</yz:fitem>
						<yz:fitem label="服务图片">
							<ul id="image-list-1" >
								<li class="image-box">
									<img height="140" width="140" src="{{$systemGoods['image']}}" />
								</li>
							</ul>
						</yz:fitem>
						<yz:fitem label="服务简介" name="brief" type="textarea" attr="disabled" val="{{$systemGoods['brief']}}"></yz:fitem>
						<yz:fitem label="服务详细" name="detail" type="textarea" attr="disabled" val="">
							<p style="width:522px;border:1px #ccc solid;padding:10px;">{!! $systemGoods['detail'] !!}</p>
						</yz:fitem>
						@else 
						<yz:fitem name="name" label="服务标题"></yz:fitem>

						<yz:fitem label="服务标签" id="tags_goods" name="goodsTags" type="textarea" val="{{$data['goodsTags']}}"></yz:fitem>

						<yz:fitem label="服务分类">
							<yz:select name="cateId" options="$cate" textfield="levelname" valuefield="id" selected="$data['cate']['id']"></yz:select>
						</yz:fitem>
						<yz:fitem label="服务图片">
							<yz:imageList name="images." images="$data['images']"></yz:imageList>
						</yz:fitem>
						<yz:fitem label="服务简介" name="brief" type="textarea"></yz:fitem>
						<yz:fitem name="detail" label="服务详细"> 
							<yz:Editor name="detail" value="{{ $data['detail'] }}"></yz:Editor> 
						</yz:fitem>
						@endif
						<p class="tc clearfix">
							@if(!isset($data['id']) || $data['id'] < 0) 
							<a href="javascript:window.history.go(-1);" class="u-addspbtn2 fl " style="margin-left:262px; margin-right:20px;">返回</a>
							<a href="javascript:;" class="u-addspbtn fl" onclick="page(2)">下一步</a>
							@else
							<a href="javascript:;" class="u-addspbtn fl" onclick="page(2)" style="margin-left:262px; margin-right:20px;">下一步</a>
							@endif
						</p>
					</div>
				</div>
			</div>
		</div>
		<!-- 第二页 -->
		<div class="p20 pageBox page_2">
			<div class="m-zjgltbg">
				<div class="p10">						
					<p class="f-bhtt f14 clearfix">
						<span class="ml15 fl">编辑流程</span>
					</p>
					<div class="m-quyu1" style="padding-bottom:0;">
						<p class="tc"><img src="{{ u('images/fw2.png') }}" alt=""></p>
					</div>
					<!-- 设置服务种类 -->
					<p class="lh45">
						核实服务详情
					</p>
					<div class="g-szzllst">
						<div class="m-jfct clearfix">
							@if(isset($systemGoods) && !empty($systemGoods))
							@if($systemGoods['priceType']==1)
							<div class="clearfix mb20">
								<a href="javascript:;" class="tjggbtn per-time1 fl">按次计费</a>
								<div class="u-jsbg per-time-cxt fl "> 
									<div class="clearfix mb20">
										<i class="fa fa-sort-up f20 fl mr10 ml10 mt10"></i>	
										<p class="clearfix fl">
											<label for="" class="fl">单次服务用时</label>
											<input name="duration" disabled="disabled" type="text" class="u-ipttext fl mr5 ml5" style="width:80px;margin-top:0;" value="{{ $systemGoods['duration'] / 3600 }}" onkeyup="if(isNaN(value))execCommand('undo')" onafterpaste="if(isNaN(value))execCommand('undo')">
											<label for="" class="fl">小时</label>
										</p>	
									</div>
									<div class="clearfix mb20">
										<i class="fa fa-sort-up f20 fl mr10 ml10 mt10"></i>	
										<p class="clearfix fl">
											<label for="" class="fl">单次服务价格</label>
											<input name="price" disabled="disabled" type="text" class="u-ipttext fl mr5 ml5" style="width:80px;margin-top:0;" value="{{ $systemGoods['price'] }}" onkeyup="if(isNaN(value))execCommand('undo')" onafterpaste="if(isNaN(value))execCommand('undo')">
											<label for="" class="fl">元</label>
										</p> 
									</div>
									<div class="clearfix">
										<i class="fa fa-sort-up f20 fl mr10 ml10 mt10"></i>	
										<p class="clearfix fl">
											<label for="" class="fl">门店价格</label>
											<input name="marketPrice" disabled="disabled" type="text" class="u-ipttext fl mr5 ml5" style="width:80px;margin-top:0;" value="{{ $systemGoods['marketPrice'] }}" onkeyup="if(isNaN(value))execCommand('undo')" onafterpaste="if(isNaN(value))execCommand('undo')">
											<label for="" class="fl">元</label>
										</p> 
									</div>
								</div>
							</div>
							@else
							<div class="clearfix mb20">
								<a href="javascript:;" class="tjggbtn per-hour1 fl">按时计费</a>
								<div class="u-jsbg per-hour-cxt fl "> 
									<div class="clearfix mb20">
										<i class="fa fa-sort-up f20 fl mr10 ml10 mt10"></i>	
										<p class="clearfix fl">
											<label for="" class="fl">每小时</label>
											<input name="price" disabled="disabled" id="price2" type="text" class="u-ipttext fl mr5 ml5" style="width:80px;margin-top:0;" value="{{ $systemGoods['price'] }}" onkeyup="if(isNaN(value))execCommand('undo')" onafterpaste="if(isNaN(value))execCommand('undo')">
											<label for="" class="fl">元</label>
										</p>
									</div>
									<div class="clearfix">
										<i class="fa fa-sort-up f20 fl mr10 ml10 mt10"></i>	
										<p class="clearfix fl">
											<label for="" class="fl">门店价格</label>
											<input name="marketPrice" disabled="disabled" id="marketPrice2" type="text" class="u-ipttext fl mr5 ml5" style="width:80px;margin-top:0;" value="{{ $systemGoods['marketPrice'] }}" onkeyup="if(isNaN(value))execCommand('undo')" onafterpaste="if(isNaN(value))execCommand('undo')">
											<label for="" class="fl">元</label>
										</p>
									</div>
								</div>
							</div> 
							@endif
							@else
							<div class="clearfix mb20">
								<a href="javascript:;" class="tjggbtn per-time fl">按次计费</a>
								<div class="u-jsbg per-time-cxt fl none">
									<input type="hidden" value="1" name="priceType"/>
									<div class="clearfix mb20">
										<i class="fa fa-sort-up f20 fl mr10 ml10 mt10"></i>	
										<p class="clearfix fl">
											<label for="" class="fl">单次服务用时</label>
											<input name="duration" id="duration" type="text" class="u-ipttext fl mr5 ml5" style="width:80px;margin-top:0;" value="{{ $data['duration'] / 3600 }}" onkeyup="if(isNaN(value))execCommand('undo')" onafterpaste="if(isNaN(value))execCommand('undo')">
											<label for="" class="fl">小时</label>
										</p>	
									</div>
									<div class="clearfix mb20">
										<i class="fa fa-sort-up f20 fl mr10 ml10 mt10"></i>	
										<p class="clearfix fl">
											<label for="" class="fl">单次服务价格</label>
											<input name="price" id="price1" type="text" class="u-ipttext fl mr5 ml5" style="width:80px;margin-top:0;" value="{{ $data['price'] }}" onkeyup="if(isNaN(value))execCommand('undo')" onafterpaste="if(isNaN(value))execCommand('undo')">
											<label for="" class="fl">元</label>
										</p> 
									</div>
									<div class="clearfix">
										<i class="fa fa-sort-up f20 fl mr10 ml10 mt10"></i>	
										<p class="clearfix fl">
											<label for="" class="fl">门店价格</label>
											<input name="marketPrice" id="marketPrice1" type="text" class="u-ipttext fl mr5 ml5" style="width:80px;margin-top:0;" value="{{ $data['marketPrice'] }}" onkeyup="if(isNaN(value))execCommand('undo')" onafterpaste="if(isNaN(value))execCommand('undo')">
											<label for="" class="fl">元</label>
										</p> 
									</div>
								</div>
							</div>
							<div class="clearfix mb20">
								<a href="javascript:;" class="tjggbtn per-hour fl">按时计费</a>
								<div class="u-jsbg per-hour-cxt fl none">
									<input type="hidden" value="2" name="priceType"/>
									<div class="clearfix mb20">
										<i class="fa fa-sort-up f20 fl mr10 ml10 mt10"></i>	
										<p class="clearfix fl">
											<label for="" class="fl">每小时</label>
											<input name="price" id="price2" type="text" class="u-ipttext fl mr5 ml5" style="width:80px;margin-top:0;" value="{{ $data['price'] }}" onkeyup="if(isNaN(value))execCommand('undo')" onafterpaste="if(isNaN(value))execCommand('undo')">
											<label for="" class="fl">元</label>
										</p>
									</div>
									<div class="clearfix">
										<i class="fa fa-sort-up f20 fl mr10 ml10 mt10"></i>	
										<p class="clearfix fl">
											<label for="" class="fl">门店价格</label>
											<input name="marketPrice" id="marketPrice2" type="text" class="u-ipttext fl mr5 ml5" style="width:80px;margin-top:0;" value="{{ $data['marketPrice'] }}" onkeyup="if(isNaN(value))execCommand('undo')" onafterpaste="if(isNaN(value))execCommand('undo')">
											<label for="" class="fl">元</label>
										</p>
									</div>
								</div>
							</div> 
							@endif
						</div>
						<p class="tc clearfix">
							<a href="javascript:;" class="u-addspbtn2 fl" style="margin-left:262px; margin-right:20px;" onclick="page(1)">上一步</a>
							<a href="javascript:;" class="u-addspbtn fl" onclick="page(3)">下一步</a>
						</p>
					</div>	
				</div>
			</div>
		</div>
		<!-- 第三页 -->
		<div class="p20 pageBox page_3">
			<div class="m-zjgltbg">
				<div class="p10">						
					<p class="f-bhtt f14 clearfix">
						<span class="ml15 fl">编辑流程</span>
					</p>
					<div class="m-quyu1" style="padding-bottom:0;">
						<p class="tc"><img src="{{ u('images/fw3.png') }}" alt=""></p>
					</div>
					<!-- 设置服务种类 -->
					<p class="lh45 f14">
						<b>核实服务详情</b>
					</p>
					<div class="m-yhk m-ghkh" style="width:939px;">								
						<div class="m-ftabct">
							<ul>
								<li class="clearfix">
									<span class="fl">服务标题</span>
									<p class="fl _name">-</p>
								</li>
								<li class="clearfix even">
									<span class="fl">服务分类</span>
									<p class="fl _cateId">-</p>
								</li>
								<li class="clearfix">
									<span class="fl">服务类型</span>
									<p class="fl"><span class="allsp1"></span></p>
								</li>
								<li class="clearfix">
									<span class="fl">服务价格</span>
									<p class="fl _price"><span class="allsp2">{{$systemGoods['price']}}</span>元</p>
								</li>
								<li class="clearfix">
									<span class="fl">市场价格</span>
									<p class="fl _marketPrice"><span class="allsp3">{{$systemGoods['marketPrice']}}</span>元</p>
								</li>
								<li class="clearfix">
									<span class="fl" style="height:90px;line-height:90px;">服务图片</span>
									<p class="fl clearfix u-ryszzjz u-fwtpx _images" style="height:90px;line-height:90px;">
									</p>
								</li>
								<li class="clearfix">
									<span class="fl">服务简介</span>
									<p class="fl clearfix">
										<span class="fl _brief" >-</span>
									</p>
								</li>
								<li class="clearfix">										
									<p class="tc pb10 pt10">
										<a href="javascript:;" class="btn f-bluebtn mr15" onclick="page(2)">上一步</a>
										@if(!isset($data['systemGoodsId']) || (int)$data['systemGoodsId'] == 0 )
                                        <input type="submit" class="btn f-80btn" style="width:120px;" value="@if(isset($systemGoods) && !empty($systemGoods))提交服务 @else 提交，等待审核@endif">
                                        @endif
									</p>
								</li>
							</ul>
						</div>							
					</div>
				</div>
			</div>
		</div>
	</yz:form>
	<!-- 分类存储 -->
		<div id="cateSave">
			<ul>
				@foreach( $cate as $key => $value )
				<li class="cateId_{{$key}}">{{$value['levelrel']}}</li>
				@endforeach
			</ul>
		</div>
	@yizan_end
@stop
@section('js')
<script src="{{ asset('js/jquery.tagsinput.min.js') }}"></script> 
<script type="text/javascript">
	$(function(){
		$('#tags_goods').tagsInput({width:'auto'});

		$(".per-time").click(function(){
			$(".u-jsbg").addClass('none');
			$(this).parent().find('div').removeClass('none');
			$(".per-time-cxt input").removeAttr("disabled");
			$(".per-hour-cxt input").attr("disabled","disabled");
			$(".allsp1").text("按次计费");
			$(".allsp2").text($("#price1").val());
			$(".allsp3").text($("#marketPrice1").val());
		})
		$(".per-hour").click(function(){
			$(".u-jsbg").addClass('none');
			$(this).parent().find('div').removeClass('none');
			$(".per-hour-cxt input").removeAttr("disabled");
			$(".per-time-cxt input").attr("disabled","disabled");
			$(".allsp1").text("按时计费");
			$(".allsp2").text($("#price2").val());
			$(".allsp3").text($("#marketPrice2").val());
		})
		
		@if(isset($data['priceType']) && $data['priceType'] == 1) 
		$(".per-time").trigger('click');
		@elseif (isset($data['priceType']) && $data['priceType'] == 2) 
		$(".per-hour").trigger('click');
		@endif

	});

	function page (pn) {
		var formObj = $("#yz_form");
		//判断第一页
		if(pn==2){
			if( formObj.find("#name").val() == '' ) {
				alert('请填写服务标题');return false;
			}
			if( formObj.find("#cateId").val() == '' ) {
				alert('请选择服务分类');return false;
			}
			if( !$("#image-list-1").find(".image-box img").attr("src") ) {
				alert('请至少上传一张图片');return false;
			} 
			if(  formObj.find("#brief").val() == '' ) {
				alert('请填写服务简介');return false;
			}
		}
		//判断第二页
		if(pn==3){
			if($(".per-hour-cxt").hasClass("none") && $(".per-time-cxt").hasClass("none")){
				alert('请选择计费方式');return false;
			}
			if( $(".per-time-cxt").hasClass("none") && formObj.find("#duration").val() == '' ) {
				alert('请填写服务时长');return false;
			}
			if( !$(".per-time-cxt").hasClass("none") && formObj.find("#duration").val() <= 0 ) {
				alert('服务时长必须大于0');return false;
			}
			if( !$(".per-hour-cxt").hasClass("none") && formObj.find("#price2").val() == '' ) {
				alert('请填写服务价格');return false;
			}
			if( !$(".per-hour-cxt").hasClass("none") && formObj.find("#price2").val() <= 0 ) {
				//alert('服务价格必须大于0');return false;
			} 
			if( !$(".per-time-cxt").hasClass("none") && formObj.find("#price1").val() == '' ) {
				alert('请填写服务价格');return false;
			} 
			if( !$(".per-time-cxt").hasClass("none") && formObj.find("#price1").val() <= 0 ) {
				//alert('服务价格必须大于0');return false;
			} 
			if( !$(".per-hour-cxt").hasClass("none") && formObj.find("#marketPrice2").val() == '' ) {
				alert('请填写门店价格');return false;
			} 
			if( !$(".per-hour-cxt").hasClass("none") && formObj.find("#marketPrice2").val() <= 0 ) {
				//alert('门店价格必须大于0');return false;
			} 
			if( !$(".per-time-cxt").hasClass("none") && formObj.find("#marketPrice1").val() == '' ) {
				alert('请填写门店价格');return false;
			}
			if( !$(".per-time-cxt").hasClass("none") && formObj.find("#marketPrice1").val() <= 0 ) {
				//alert('门店价格必须大于0');return false;
			} 

			if( !$(".per-hour-cxt").hasClass("none") && formObj.find("#price2").val() != '' ) {
				$(".allsp2").text(formObj.find("#price2").val());
			} else {
				$(".allsp2").text(formObj.find("#price1").val());
			}

			if( !$(".per-hour-cxt").hasClass("none") && formObj.find("#marketPrice2").val() != '' ) {
				$(".allsp3").text(formObj.find("#marketPrice2").val()); 
			} else { 
				$(".allsp3").text(formObj.find("#marketPrice1").val());
			} 

			if($(".per-hour-cxt").length == 1 && !$(".per-hour-cxt").hasClass('none')){
				$(".allsp1").text("按时计费");
			}
			
			if($(".per-time-cxt").length == 1 && !$(".per-time-cxt").hasClass('none')){
				$(".allsp1").text("按次计费");
			}

			//获取信息
			$('._name').text( formObj.find("#name").val() );
			$('._cateId').text( $("#cateSave").find("ul li.cateId_"+formObj.find("#cateId").val()).text() );
			$('._brief').text( formObj.find("#brief").val() );
			$("._images").html("");
			var images = $("#image-list-1").find(".image-box img");
			$.each(images,function(key,value){
				var pic = value.cloneNode(true);
				$("._images").append(pic);
			});
		}
		//切换
		$('.pageBox').hide();
		$('.page_'+pn).show();
	}

</script>
@stop
@include('seller._layouts.alert')
