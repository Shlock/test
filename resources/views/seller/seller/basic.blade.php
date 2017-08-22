@extends('seller._layouts.base')
@section('css')
<link rel="stylesheet" type="text/css" href="{{ asset('css/jquery.tagsinput.css') }}">
<style type="text/css">
	.f-boxr .btn{background: #efefef; border-color: #dfdfdf; color: #555;}
	.x-gebox{border: 1px solid #ddd; padding: 5px 20px;}
	.x-addge{float: left;width: 500px;}
	.x-gebox .u-ipttext{width: 100px; margin-right: 10px;}
	.closege{width: 20px; height: 20px; display: inline-block; cursor: pointer; vertical-align: middle; margin-top: -2px; background: url({{ asset('wap/community/client/images/ico/close.png') }}); background-size: 100% 100%; }
</style>
@stop
@section('content')
	<div class="p20">
		<div class="m-zjgltbg">
			<div class="p10">						
				<p class="f-bhtt f14 clearfix">
					<span class="ml15 fl">个人基本信息设置</span>
					<a href="{{ u('Seller/index') }}" class="fr mr15 btn f-bluebtn" style="margin-top:8px;">返回</a>
				</p>
				<div class="m-quyu1">
					<div class="m-inforct" style="padding-top:78px;width:750px;">  
						@yizan_begin
						<yz:form id="yz_form" action="updatebasic">
							<yz:fitem name="name" label="名称"></yz:fitem>
							<yz:fitem name="logo" label="logo" type="image"></yz:fitem>
							<yz:fitem name="image" label="背景图" type="image"></yz:fitem>
							<div class="u-zciptct clearfix mb15">
								<span class="f-tt">
									 经营类型:
								</span> 
								<div class="fl" style="width:500px;">
								<div class="input-group">
			                        <table border="0">
			                            <tbody>
			                                <tr>
			                                    <td rowspan="2">
			                                        <select id="cate_1" name="cateIds" class="form-control" multiple="multiple" style="min-width:200px; *width:200px; height:260px;">
			                                        @if(count($data['sellerCate']) > 0)
			                                        @foreach($data['sellerCate'] as $item)
			                                        <option value="{{$item['cateId']}}" >{{$item['cates']['name']}}</option>
			                                        @endforeach
			                                        @endif
			                                        </select>
			                                    </td>
			                                    <td width="50" align="center" rowspan="2">
			                                        <button type="button" class="btn btn-gray" onclick="$.optionMove('cate_2', 'cate_1', 1);">
			                                            <span class="fa fa-2x fa-angle-double-left"> </span>
			                                        </button>
			                                        <br>
			                                        <button type="button" class="btn btn-gray" onclick="$.optionMove('cate_2', 'cate_1');">
			                                            <span class="fa fa-2x fa-angle-left"> </span>
			                                        </button>
			                                        <br><br>
			                                        <button type="button" class="btn btn-gray" onclick="$.optionMove('cate_1', 'cate_2');">
			                                            <span class="fa fa-2x fa-angle-right"> </span>
			                                        </button>
			                                        <br>
			                                        <button type="button" class="btn btn-gray" onclick="$.optionMove('cate_1', 'cate_2', 1);">
			                                            <span class="fa fa-2x fa-angle-double-right"> </span>
			                                        </button>
			                                        <input type="hidden" name="cateIds" id="cateIds">
			                                    </td>
			                                </tr>
			                                <tr>
			                                    <td>
			                                       <select id="cate_2" class="form-control" multiple="multiple" style="min-width:200px; *width:200px; height:260px;"> 
			                                       	@foreach($cateIds as $key => $val)
			                                       	@if($cateIds[$key]['childs'])
			                                       	<optgroup label="{{$val['name']}}">
			                                       		@foreach($cateIds[$key]['childs'] as $cs)
													    <option value="{{$cs['id']}}">{{$cs['name']}}</option>
													    @endforeach
													</optgroup> 
			                                        @else
													<option value="{{$val['id']}}" >{{$val['name']}}</option>
			                                        @endif
			                                        @endforeach
			                                        </select>
			                                    </td>
			                                </tr>
			                            </tbody>
			                        </table>
			                        <div class="blank3"></div>
			                    </div> 
								
								</div> 
								<script type="text/javascript">
				                    jQuery(function($){ 
				                        $("#yz_form").submit(function(){
				                            var ids = new Array(); 
				                            $("#cate_1 option").each(function(){
				                                ids.push(this.value);
				                            })
				                            $("#cateIds").val(ids);
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
				                
				                    });
				                </script>  
							</div>  
							@if($data['type'] == 2)
							<yz:fitem name="contacts" label="联系人"></yz:fitem>
							@endif
							<yz:fitem name="provinceId" label="所在地区">
								<yz:region pname="provinceId" pval="$data['province']['id']" cname="cityId" cval="$data['city']['id']" aname="areaId" aval="$data['area']['id']"></yz:region>
							</yz:fitem>
							<yz:fitem name="mapPos" label="服务范围">
								<yz:mapArea name="mapPos" pointVal="$data['mapPoint']" addressVal="$data['address']" posVal="$data['mapPos']"></yz:mapArea>
							</yz:fitem>
							<yz:fitem name="serviceTel" label="服务电话"></yz:fitem>
							<yz:fitem label="配送时段">
								<yz:fitem label="配送时间">
								<div id="delivery-form-item" class="u-fitem clearfix">
						            <div class="f-boxr">
						                <button type="button" class="btn addge add_delivery">添加时间</button>
						                <span>(最多可添加3个)</span>
						            </div>
						        </div>
						        <div id="delivery-form-item" class="u-fitem clearfix x-addge">
						            <span class="f-tt">&nbsp;</span>
						            <div class="f-boxr delivery_panel">
						           	 	@foreach($data['deliveryTimes'] as $item)
						                <div class="x-gebox">
											开始时间：<input type="text" name="_stime[]" value="{{$item['stime']}}" class="u-ipttext" />
											结束时间：<input type="text" name="_etime[]" value="{{$item['etime']}}" class="u-ipttext" />
											<i class="closege"></i>
						                </div>
						            	@endforeach
						            </div>
						        </div>
							</yz:fitem>
							<yz:fitem name="serviceFee" label="起送费"></yz:fitem>
							<yz:fitem name="deliveryFee" label="配送费"></yz:fitem>
							<!--<yz:fitem label="提成值">
								<div class="u-fitem clearfix ">
				                  <input type="text" name="deduct" class="u-ipttext" value="{{$data['deduct'] }}">
				                  <span style="color:#ccc;">%</span>
						        </div>
							</yz:fitem>-->

							<yz:fitem label="货到付款">
								<yz:checkbox name="isCashOnDelivery" options="1" texts="支持货到付款" checked="$data['isCashOnDelivery']"></yz:checkbox>
				  			</yz:fitem>
							<div class="u-zciptct clearfix mb15">
								<label class="f-tt fl">营业时间:</label>
								@include('seller.seller.showtime') 
					            @include('seller.seller.sztime') 
							</div>
							<yz:fitem name="status" type="hidden"></yz:fitem>
						</yz:form>				
						@yizan_end 
					</div>
				</div>
			</div>
		</div>
	</div>
@stop

@section('js') 
@include('seller._layouts.alert')
<script src="{{ asset('js/jquery.tagsinput.min.js') }}"></script>  
<script type="text/tpl" id="deliveryrow"> 
	<div class="x-gebox" style="margin-top:3px;">
		开始时间：<input type="text" name="_stime[]" class="u-ipttext" placeholder="00:00"/>
		结束时间：<input type="text" name="_etime[]" class="u-ipttext" placeholder="12:00"/>
		<i class="closege"></i>
    </div>
</script> 
<script type="text/javascript">
	$(".add_delivery").click(function(){  
		if ($(".x-gebox").length == 3) {
			$.ShowAlert('配送时间段最多添加3个'); 
			return false;
		};
		$(".delivery_panel").append($("#deliveryrow").html()); 
		if($(".x-gebox").length > 0){
			$(".delivery_panel").parent().show();
		}
	});
	$(document).on('click','.closege',function(){ 
		$(this).parent().remove();
		if($(".x-gebox").length <= 0){
			$(".delivery_panel").parent().hide();
		}
	});
	$("input[name='isCashPay']").click(function(){
		if(this.checked){
			$(this).val(1);
		} else {
			$(this).val(0);
		}
	});
</script>
@stop
