@extends('seller._layouts.base')
@stop
@section('content')
	<div class="p20">		
		<div class="m-zjgltbg">
			<p class="f-bhtt f14 clearfix">
				<span class="ml15 fl">人员设置</span>
			</p>
			<div class="p10">
				<p class="lh55 f-tt clearfix">
                    <span class="f16">账户设置</span>
                </p>
				<!-- 更换绑定银行卡号 -->
				<div class="clearfix mt10">
					<div class="m-yhk m-ghkh" style="width:939px;">
						<div class="m-ftabct mb15">
							<ul>
								<li class="clearfix">
									<span class="fl">手机号码</span>
									<p class="fl clearfix">
										<span class="fl">{{ substr_replace($list['mobile'],'****',3,4) }}</span>
										<a href="{{ u('Seller/changetel') }}" class="fr btn f-30btn" style="width:100px; margin-top:4px;">手机号码更换</a>
									</p>
								</li>
								<li class="clearfix even">
									<span class="fl">密码</span>
									<p class="fl  clearfix">
										<span class="fl">**********</span>
										<a href="{{ u('Seller/changepwd') }}" class="fr btn f-30btn" style="width:100px; margin-top:4px;">修改密码</a>
									</p>
								</li>
							</ul>
						</div>	
						<p class="lh55 f-tt clearfix ">
                            <span class="f16 fl">人员基本信息</span>
                            <a href="{{ u('Seller/basic') }}" class="fr btn f-30btn f-30btnshow " style="width:100px; margin-top:4px;">编辑</a>
                        </p> 
						@yizan_begin
						<yz:list> 
						<table pager="no">  
							<row> 
								<tr >
									<td width="100px;" style="text-align:center">LOGO</td>  
									<td><img src="{{$list['logo']}}" style="max-width:100px" alt=""></td>   
								</tr>
								<tr >
									<td width="100px;" style="text-align:center">@if($list['type']==2)商家@endif名称</td>  
									<td>{{$list['name']}}</td> 
								</tr>
								<tr >
									<td style="text-align:center">经营类型</td>  
									<td>
										@foreach($list['sellerCate'] as $item)
											{{$item['cates']['name']}} 
										@endforeach
									</td>   
								</tr> 
								@if($list['type'] == 2)
								<tr >
									<td style="text-align:center">法人/店主</td>
									<td>{{$list['contacts']}}</td>   
								</tr> 
								@endif
								<tr >
									<td style="text-align:center">所在地区</td>
									<td class="tdtr">
										<p class="noshow">
										{{ $list['province']['name'] }} - {{ $list['city']['name'] }} {{ $list['area'] != "" ? "-". $list['area']['name'] :"" }}
										</p>
										<p class="showok" style="display:none"><yz:Region pname="provinceId" cname="cityId" aname="areaId"></yz:Region>
										<a href="javascriput:;" class="btn f-btn f-btnonk" style="vertical-align: middle;">保存</a></p>
									</td>
								</tr>
								<tr >
									<php>
										$map = explode(',', $list['mapPointStr']);
										$point[0] = $map[1];
										$point[1] = $map[0];
										$mappoint = implode(',',$point);
									</php>
									<td style="text-align:center">服务范围</td>  
									<td> 
										<img src="http://st.map.qq.com/api?size=700*300&center={{$mappoint}}&zoom=16" /> 
									</td>
								</tr>  
								<tr >
									<td style="text-align:center;">服务电话</td>  
									<td>{{ $list['serviceTel']}}</td> 
								</tr> 
								<tr >
									<td style="text-align:center;">配送时段</td>  
									<td>{{ $list['deliveryTime']}}</td> 
								</tr> 
								<tr >
									<td style="text-align:center;">配送费</td>  
									<td>￥{{ $list['deliveryFee']}}</td> 
								</tr> 
								<tr >
									<td style="text-align:center;">起送价</td>  
									<td>￥{{ $list['serviceFee']}}</td> 
								</tr> 
								<tr >
									<td style="text-align:center;">佣金比例</td>  
									<td>{{ $list['deduct']}}%</td> 
								</tr> 
								<tr >
									<td style="text-align:center;">货到付款</td>  
									<td>@if($list['isCashOnDelivery']) 支持货到付款 @else 不支持货到付款 @endif</td> 
								</tr> 
							</row> 
						</table> 
						<p class="lh55 f-tt clearfix mt20">
                            <span class="f16 fl">资质认证信息</span>
                            <a href="{{ u('Seller/qualification') }}" class="fr btn f-30btn f-30btnshow " style="width:100px; margin-top:4px;">编辑</a>
                        </p>  
						<table pager="no">  
							<row> 
								<tr >
									<td style="text-align:center; width:10%;">认证状态</td>  
									<td>{{ $list['isAuthenticate'] == 1 ? "已认证" : "未认证"}}</td> 
								</tr>
                                @if($list['type'] == 1)
                                    <tr >
                                        <td style="text-align:center">真实姓名</td>
                                        <td>{{$list['contacts']}}</td>
                                    </tr>
                                @endif
								<tr >
									<td width="100px;" style="text-align:center">身份证号码</td>  
									<td>{{ $list['authenticate']['idcardSn'] }}</td> 
								</tr>
								<tr >
									<td style="text-align:center">身份证件照</td>  
									<td>
										<a href="{{ $list['authenticate']['idcardPositiveImg'] }}">
											<img src="{{ $list['authenticate']['idcardPositiveImg'] }}" style="max-width:200px"/>
										</a>
										<a href="{{ $list['authenticate']['idcardNegativeImg'] }}">
											<img src="{{ $list['authenticate']['idcardNegativeImg'] }}" style="max-width:200px"/>
										</a>
									</td> 
								</tr> 
								@if($list['type'] == 2)
								<tr >
									<td width="100px;" style="text-align:center">营业执照</td>  
									<td>
										<a href="{{ $list['authenticate']['businessLicenceImg'] }}">
										<img src="{{ $list['authenticate']['businessLicenceImg'] }}" style="max-width:200px"/>
										</a>
									</td> 
								</tr>
								@else
								<tr >
									<td width="100px;" style="text-align:center">资质证书</td>  
									<td>
										<a href="{{ $list['authenticate']['certificateImg'] }}">
										<img src="{{ $list['authenticate']['certificateImg'] }}" style="max-width:200px"/>
										</a>
									</td> 
								</tr>
								@endif
							</row> 
						</table>
						<p class="lh55 f-tt clearfix mt20">
                            <span class="f16 fl">其他设置</span>
                            <a href="{{ u('Seller/rest') }}" class="fr btn f-30btn f-30btnshow " style="width:100px; margin-top:4px;">编辑</a>
                        </p>  
						<table pager="no">  
							<row> 
								<tr >
									<td style="text-align:center; width:10%;">状态</td>  
									<td>{{ $list['status'] == 1 ? "正常" : "锁定"}}</td> 
								</tr> 
								<tr >
									<td style="text-align:center; width:10%;">商家简介</td>  
									<td>{{$list['brief']}}</td> 
								</tr> 
								<tr >
									<td style="text-align:center; width:10%;">排序</td>  
									<td>{{ $list['sort'] }}</td> 
								</tr> 
							</row> 
						</table>
						</yz:list>   
						@yizan_end  				
					</div>
				</div>
			</div>
		</div>
	</div>
@stop

@section('js')
<script type="text/javascript">  
    // var times = "{{yzday(UTC_TIME)}}";
    // onblurs(times,1);
    // function onblurs(time,type) {  
    // 	$(".m-yytimelstct  li").each(function(){
    //         sta = $(this).attr("data-strtotime"); 
    //         if( sta == type ){               
    //             $(this).css("color","#555");
    //             $(this).css("background","#fff");
    //         }else{
    //         	$(this).css("color","#fff");
    //             $(this).css("background","#d4d4d4");
    //         }
    //     }); 

    // 	$(".times").text(time);
    //     $(".uli").html(""); 
    //     var html = "";  
    //     var sta = "" ;   
    //     $.post("{{ u('Seller/schedule')}}",{date:time},function(result){ 
    //         res = eval(result.data);
    //         $.each(res.hours,function(key,value){  
    //             if(value.status == "-1"){      
    //                 html +="<li data-time="+key+"><span>"+value.hour+"</span><div>拒绝接单</div></li> ";               
    //             }else{
    //                 html +="<li data-time="+key+"><span>"+value.hour+"</span><div>可选接单</div></li> ";                    
    //             }
    //         }); 
    //         $(".uli").append(html);
    //         $(".yuetime  li").each(function(){
    //             sta = $(this).children("div").text();
    //             if( sta == "拒绝接单"){               
    //                 $(this).css("color","#fff");
    //                 $(this).css("background","#b50000");
    //             }else{
    //                 $(this).css("color","#000"); 
    //                 $(this).css("background","#fff");
    //             }
    //         });
    //     },'json');  
    // }; 
</script>
@stop
