@extends('seller._layouts.base')
@section('css')
<link rel="stylesheet" type="text/css" href="{{ asset('css/jquery.tagsinput.css') }}">
<style type="text/css">
	#cateSave{display: none;}
	.page_2,.page_3,.page_4{display: none;}
	.m-spboxlst li{margin-bottom: 0px;}
	#tags_goods-form-item .f-boxr {width:550px;}
</style>
@stop

@section('content')
	<?php $width=(100/4).'%';$_width=((100/4)-1).'%'; ?> 
	@yizan_begin
	<yz:form id="yz_form" action="save" nobtn="1">
		<!-- 第一页 -->
		<div class="pageBox page_1">
			<div class="m-zjgltbg">
				<div class="p10">						
					<p class="f-bhtt f14 clearfix">
						<span class="ml15 fl">编辑流程</span>
					</p>
					<div class="m-quyu1" style="padding-bottom:0;">
						<div class="m-porbar clearfix">
							<p class="f-bar"></p>
							<ul class="m-barlst clearfix">
								<li class="on" style="width:{{$width}};*width:{{$_width}}">
									<p class="f-lsbar"></p>
									<span class="num">1</span>
									<p class="tc mt5">1、编辑服务信息</p> 
								</li>
								<li style="width:{{$width}};*width:{{$_width}}">
									<p class="f-lsbar"></p>
									<span class="num">2</span>
									<p class="tc mt5">2、设置服务计费</p> 
								</li>
								<li style="width:{{$width}};*width:{{$_width}}">
									<p class="f-lsbar"></p>
									<span class="num">3</span>
									<p class="tc mt5">3、其他设置</p> 
								</li>
								<li style="width:{{$width}}%;">
									<p class="f-lsbar"></p>
									<span class="num">4</span>
									<p class="tc mt5">4、核实服务详情</p> 
								</li>
							</ul>
						</div>
					</div>
					<!-- 设置服务种类 -->
					<p class="lh45">
						设置服务种类
					</p>
					<div class="g-szzllst pt10">
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
						<div class="m-porbar clearfix">
							<p class="f-bar"></p>
							<ul class="m-barlst clearfix">
								<li class="on" style="width:{{$width}};*width:{{$_width}}">
									<p class="f-lsbar"></p>
									<span class="num">1</span>
									<p class="tc mt5">1、编辑服务信息</p> 
								</li>
								<li class="on"  style="width:{{$width}};*width:{{$_width}}">
									<p class="f-lsbar"></p>
									<span class="num">2</span>
									<p class="tc mt5">2、设置服务计费</p> 
								</li>
								<li style="width:{{$width}};*width:{{$_width}}">
									<p class="f-lsbar"></p>
									<span class="num">3</span>
									<p class="tc mt5">3、其他设置</p> 
								</li>
								<li style="width:{{$width}}%;">
									<p class="f-lsbar"></p>
									<span class="num">4</span>
									<p class="tc mt5">4、核实服务详情</p> 
								</li>
							</ul>
						</div>
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
						<div class="m-porbar clearfix">
							<p class="f-bar"></p>
							<ul class="m-barlst clearfix">
								<li class="on" style="width:{{$width}};*width:{{$_width}}">
									<p class="f-lsbar"></p>
									<span class="num">1</span>
									<p class="tc mt5">1、编辑服务信息</p> 
								</li>
								<li class="on"  style="width:{{$width}};*width:{{$_width}}">
									<p class="f-lsbar"></p>
									<span class="num">2</span>
									<p class="tc mt5">2、设置服务计费</p> 
								</li>
								<li class="on"  style="width:{{$width}};*width:{{$_width}}">
									<p class="f-lsbar"></p>
									<span class="num">3</span>
									<p class="tc mt5">3、其他设置</p> 
								</li>
								<li style="width:{{$width}}%;">
									<p class="f-lsbar"></p>
									<span class="num">4</span>
									<p class="tc mt5">4、核实服务详情</p> 
								</li>
							</ul>
						</div>
					</div>
					<!-- 设置服务种类 -->
					<p class="lh45 f14">
						其他设置
					</p>
					<div class="m-yhk m-ghkh" style="width:939px;">								
						
						<yz:fitem label="选择员工" pcss="send-user-type send-user-group hidden">
						    <div class="input-group">
						    	<table border="0">
					                 <tbody>
					                 	<tr>
						                    <td rowspan="2">
						                        <select id="user_1" name="staffIds" class="form-control" multiple="multiple" style="min-width:200px; *width:200px; height:260px;">
						                        @foreach($data['staffIds'] as $item)
												<option value="{{$item['id']}}" >{{$item['name']}}</option>
						                        @endforeach
						                        </select>
						                    </td>
						                    <td width="60" align="center" rowspan="2">
						                        <button type="button" class="btn btn-gray" onclick="$.optionMove('user_2', 'user_1', 1);">
						                            <span class="fa fa-2x fa-angle-double-left"> </span>
						                        </button>
						                        <br><br>
						                        <button type="button" class="btn btn-gray" onclick="$.optionMove('user_2', 'user_1');">
						                            <span class="fa fa-2x fa-angle-left"> </span>
						                        </button>
						                        <br><br>
						                        <button type="button" class="btn btn-gray" onclick="$.optionMove('user_1', 'user_2');">
						                            <span class="fa fa-2x fa-angle-right"> </span>
						                        </button>
						                        <br><br>
						                        <button type="button" class="btn btn-gray" onclick="$.optionMove('user_1', 'user_2', 1);">
						                            <span class="fa fa-2x fa-angle-double-right"> </span>
						                        </button>
						                        <input type="hidden" name="staffIds" id="users">
						                    </td>
						                    <td width="230" style="padding:0; height:35px;">
						                        <input type="text" class="u-ipttext" placeholder="搜索员工" id="fansName" style="width:140px;hieght:30px;">
						                        <a href="javascript:;" id="fansNameBtn" class="btn btn-gray btn-success input-image-select">
						                        <i class="fa fa-search"></i></a>
						                    </td>
						                </tr>
						                <tr>
						                    <td>
						                       <select id="user_2" class="form-control" multiple="multiple" style="min-width:200px; *width:200px; height:220px;"> 
				                            	</select>
						                    </td>
						                </tr>
					            	</tbody>
				            	</table>
				            	<div class="blank3"></div>
				            </div> 
						</yz:fitem> 
						<fitem type="script">
						<script type="text/javascript">
							jQuery(function($){ 

							    $("#yz_form").submit(function(){
							        var ids = new Array(); 
							        $("#user_1 option").each(function(){
							            ids.push(this.value);
							        })
							        $("#users").val(ids);
							    })
							    $.optionMove = function(from, to, isAll){
							        var from = $("#" + from);
							        var to = $("#" + to);
							        var list;
							        if(isAll){
							            list = $('option', from);
							        }else{
							            list = $('option:selected', from);
							        }
							        list.each(function(){
							            if($('option[value="' + this.value + '"]', to).length > 0){
							                $(this).remove();
							            } else {
							                $('option', to).attr('selected',false);
							                to.append(this);
							            }
							        });
							    }
						
								$('input[name="userType"]').change(function(){ 
									var type = $("input[name='userType']:checked").val(); 
									if(type==0){
										$('.send-user-group').addClass("hidden");
									}else{
										$('.send-user-group').removeClass("hidden");
									}
								});

					             $('#fansNameBtn').click(function(){
									var u_id = new Array(); 
					                $.post("{{ u('Seller/search') }}",{"name":$("#fansName").val()},function(result){  
					                    if(!result || result.length < 1){ 
					                        $('#user_2').html("<option value='0' disabled='true'>未搜索到员工</option>");
					                    } else {
					                        var html = '';
											$("#user_1 option").each(function(){
												u_id.push(this.value);
											})
					                        $.each(result, function(index,e){
												console.log(u_id.indexOf(result[index].id));
					                        	if (u_id.indexOf(result[index].id) == -1){
													html += " <option class='uid" + e.id + "' value=" + e.id + ">" + e.name + "</option>";													
												}
					                        });
					                        $('#user_2').html(html);
					                    }
					                },'json');
					            }); 
					            $('#fansNameBtn').click();

					            $("input[name=sendType]").change(function() {
					            	/* Act on the event */
					            	var type = $("input[name='sendType']:checked").val(); 
									if(type==1){
										$('#args-form-item').addClass("hidden");
									}else{
										$('#args-form-item').removeClass("hidden");
									}
					            });
					            $('#args-form-item').addClass("hidden");
							});
						</script>	
						</fitem> 	
						<yz:fitem label="提成方式">
							<div class="clearfix mb20"> 
								<p class="clearfix fl"> 
									<select name="deductType" id="deduct_type" class="sle  ">
										<option value="1" @if($data['deductType']==1) selected @endif>按单提成</option>
										<option value="2" @if($data['deductType']==2) selected @endif>按比例提成</option>
									</select>
								</p>	
							</div>	
						</yz:fitem> 
						
						<yz:fitem label="提成值">
							<div class="clearfix mb20"> 
								<p class="clearfix fl"> 
									<input name="deductValue" id="deduct_value" type="text" class="u-ipttext" style="width:80px;margin-top:0;" value="{{$data['deductValue']}}" onkeyup="if(isNaN(value))execCommand('undo')" onafterpaste="if(isNaN(value))execCommand('undo')">
									<label for="" class="fl"></label>
								</p>	
							</div>	
						</yz:fitem> 			
					</div>
					<p class="tc clearfix">
						<a href="javascript:;" class="u-addspbtn2 fl" style="margin-left:262px; margin-right:20px;" onclick="page(2)">上一步</a>
						<a href="javascript:;" class="u-addspbtn fl" onclick="page(4)">下一步</a>
					</p>	
				</div>
			</div>
		</div>
		<!-- 第四页 -->
		<div class="p20 pageBox page_4">
			<div class="m-zjgltbg">
				<div class="p10">						
					<p class="f-bhtt f14 clearfix">
						<span class="ml15 fl">编辑流程</span>
					</p>
					<div class="m-quyu1" style="padding-bottom:0;">
						<div class="m-porbar clearfix">
							<p class="f-bar"></p>
							<ul class="m-barlst clearfix">
								<li class="on" style="width:{{$width}};*width:{{$_width}}">
									<p class="f-lsbar"></p>
									<span class="num">1</span>
									<p class="tc mt5">1、编辑服务信息</p> 
								</li>
								<li class="on"  style="width:{{$width}};*width:{{$_width}}">
									<p class="f-lsbar"></p>
									<span class="num">2</span>
									<p class="tc mt5">2、设置服务计费</p> 
								</li>
								<li class="on"  style="width:{{$width}};*width:{{$_width}}">
									<p class="f-lsbar"></p>
									<span class="num">3</span>
									<p class="tc mt5">3、其他设置</p> 
								</li>
								<li class="on" style="width:{{$width}}%;">
									<p class="f-lsbar"></p>
									<span class="num">4</span>
									<p class="tc mt5">4、核实服务详情</p> 
								</li>
							</ul>
						</div>
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
									<span class="fl">提成方式</span>
									<p class="fl _deductType"><span class="allsp4"></span></p>
								</li>
								<li class="clearfix">
									<span class="fl">提成值</span>
									<p class="fl _deductValue"><span class="allsp5"></span></p>
								</li>
								<li class="clearfix">
									<span class="fl" style="height:90px;line-height:90px;">服务图片</span>
									<p class="fl clearfix u-ryszzjz u-fwtpx _images" style="height:90px;line-height:90px;">
									</p>
								</li>
								<li class="clearfix">
									<span class="fl">服务简介</span>
									<p class="fl clearfix">
										<span class="fl  _brief" >-</span>
									</p>
								</li>
								<li class="clearfix">										
									<p class="tc pb20">
										<a href="javascript:;" class="btn f-bluebtn mr15" onclick="page(3)">上一步</a>
										<input type="submit" class="btn f-80btn" style="width:120px;" value="@if(isset($systemGoods) && !empty($systemGoods))提交服务 @else 提交，等待审核@endif">
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
            if( !$(".per-time-cxt").hasClass("none") && formObj.find("#price1").val() == '' ) {
                alert('请填写服务价格');return false;
            }
            if( !$(".per-hour-cxt").hasClass("none") && formObj.find("#marketPrice2").val() == '' ) {
                alert('请填写门店价格');return false;
            }
            if( !$(".per-time-cxt").hasClass("none") && formObj.find("#marketPrice1").val() == '' ) {
                alert('请填写门店价格');return false;
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
		//判断第三页
		if(pn==4){
			if( formObj.find("#user_1 option").length == 0 ) {
				alert('请填写选择从事此服务的员工');return false;
			}

			if( formObj.find("#deduct_value").val() == '' ) {
				alert('请填写提成值');return false;
			}
 			
 			if($("#deduct_type").val()==1){
 				$(".allsp4").text("按单提成");
 			} else { 
 				$(".allsp4").text("按比例提成");
 			}

 			$(".allsp5").text(formObj.find("#deduct_value").val()); 

		}
		//切换
		$('.pageBox').hide();
		$('.page_'+pn).show();
	}

</script>
@stop
