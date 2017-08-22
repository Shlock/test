@extends('seller._layouts.sign_base')
@section('css')
<style type="text/css">
.u-zciptct .f-tt {
  width: 90px;
}
.m-tpyllst li {float: left;width: 129px;margin: 5px 5px;position: relative;height: 84px;border: solid 1px #ccc;margin-left: 110px;margin-top: -22px;}
.g-crtserct{margin: 100px auto;}
#map-search-1{width: 95px;height: 48px;} 
#map-address-1{	line-height: 15px;height: 15px;padding: 15px 15px 15px 8px; border: 1px solid #ced6dc;border-radius: 2px; width: 238px;}
#map-refresh-pos-1{width: 105px;height: 48px;} 

#businessLicenceImg-box{padding: 15px 15px 15px 8px;}
input[type="text"]{box-shadow: 0px 0px 0px 0px rgba(223, 223, 233, 0.4) inset;}
select{box-shadow: 0px 0px 0px 0px rgba(223, 223, 233, 0.4) inset;width: 80%;border: 0px;}
.shez{padding-left:5px;}
.ui-datepicker select.ui-datepicker-month, .ui-datepicker select.ui-datepicker-year {width: 35%;border: 1px solid #ced6dc;margin: 0 4px;} 
</style>
@stop
@section('content') 
	<div  id="register">	
		<div class="m-bztt mt10"><img src="{{ asset('images/zc1.png') }}" alt=""></div>
		<div class="mt10 m-xyct">
			<p class="lh85 f20">签署服务机构协议</p>
			<div class="u-xyct clearfix">
				<ul class="fl m-xylst">
					@foreach($config as $key => $val)
					<li @if($key == '0') class="on" @endif>
						{{$val['name']}}<i></i>
					</li>
					@endforeach
				</ul>
				<div class="m-xyshow fl">
					@foreach($config as $key => $val)
					<div @if($key == '0') class="xylst" @else class="xylst none" @endif >
						 {{$val['val']}}
					</div> 
					@endforeach
				</div>
			</div>
			<p class="mt20 tc">
				<a href="javascript:;" id="register1" class="btn f-back">同意签署服务机构协议，下一步</a>
			</p> 
		</div>
	</div>

	<div  id="register2" style="display:none">	
		<div class="m-bztt mt10"><img src="{{ asset('images/zc2.png') }}" alt=""></div>
		<div class="mt10 m-xyct">
		<div class="m-inforct">
			<p class="f20 lh85">注册基本信息</p> 
			<fieldset>
				<legend>注册表单</legend>
				@if(OPERATION_VERSION == 'personal')
				<input type="hidden" value="1" name="seltype" >
				@else
				<div class="u-zciptct clearfix mb15">
					<label class="f-tt fl">加盟类型:</label>
					<p class="u-iptdw fl">
						<i class="fa fa-link"></i>
						<select id="seltype" class="">
							<option value="0">请选择加盟类型</option>
							<option value="1">个人</option>
							<option value="2">商家</option> 
							<option value="3">物业公司</option> 
						</select>
					</p>								
				</div>
				@endif
				<div class="u-zciptct clearfix mb15">
					<label class="f-tt fl">手机号码:</label>
					<p class="u-iptdw fl">
						<i class="fa fa-user"></i>
						<input type="text" maxlength="11" onkeyup="this.value=this.value.replace(/\D/g,'')" onafterpaste="this.value=this.value.replace(/\D/g,'')"  id="mobile" name="mobile" placeholder="请输入你的手机号码">
					</p>								
				</div>
				<div class="u-zciptct clearfix mb15">
					<label class="f-tt fl">验证码:</label>
					<p class="u-iptdw fl" style='width:184px;'>
						<i class="fa fa-tablet"></i>
						<input type="text" maxlength="6" onkeyup="this.value=this.value.replace(/\D/g,'')" onafterpaste="this.value=this.value.replace(/\D/g,'')"  name="verify" placeholder="请输入验证码"style='width:125px;'>
					</p>
					<form onsubmit="return false;">
						<button id="verify" class=" verify fl btn f-yzmbtn ml15 imgverify">获取验证码</button>							
					</form>							
				</div>
				<div class="u-zciptct clearfix mb15">
					<label class="f-tt fl">密码:</label>
					<p class="u-iptdw fl">
						<i class="fa fa-unlock-alt"></i>
						<input type="password" name="pwd" placeholder="密码应由6-12个字符组成">
					</p>								
				</div>
				<div class="u-zciptct clearfix mb15">
					<label class="f-tt fl">确认密码:</label>
					<p class="u-iptdw fl">
						<i class="fa fa-unlock-alt"></i>
						<input type="password" name="pwdtwo" placeholder="请再次确认填写密码">
					</p>								
				</div>
				<p class="tc">
					<a href="javascript:;" id="registerrk" class="btn f-170btn">上一步</a>
					<a href="javascript:;" id="registernet" class="btn f-170btn ml20">下一步</a>
				</p>
			</fieldset> 
		</div>
		</div>
	</div>
<!-- 图形验证码框 -->
	<div class="g-tkbg verifynotice none" style="left:0px">
		<div class="g-serct">
			<p class="f-tt" style="text-align:left"><span class="ml15"> 操作提醒</span></p>
			<p class="tc mt20 mb20">
				请输入图片验证码，正确无误后发送短信验证码
			</p>
			<p class="lh25 f18 tc">
				<input type="text" name="imgverify" style='width:125px;line-height: 45px;height: 45px;border: 1px solid #ced6dc;margin-left:200px;' class="fl">
				<img src="{{ u('Public/imgverify')}}" id="imgverify">
				<span id="changeimg" style="cursor:pointer;margin-right:150px;"> 换一张</span>
			</p> 
			<p class="mt20 pb20 tc">
				<a href="javascript:;" class="btn f-back mb20 canver" >返回</a>
				<a href="javascript:;" class="btn f-back mb20 checkverify" >确定</a>
			</p>
		</div>
	</div>
	<div  id="register3" style="display:none">	
		<div class="m-bztt mt10">
			<img src="{{ asset('images/zc3.png') }}" alt="">
		</div>
		<div class="mt10 m-xyct">
			<div class="m-inforct" >
			<p class="f20 lh85">编辑<span class="type"></span>资料</p>
			<form action="">		
			@yizan_begin		
				<fieldset>
					<legend>编辑<span class="type"></span>资料</legend>
					<div class="u-zciptct clearfix mb15">
						<div class="u-zciptct clearfix mb15 seller_info"> 
						<div class="fl">
							<php>
								$headimg = asset('images/default_headimg.jpg'); 
							</php> 
							<yz:fitem name="avatar" label="请上传LOGO" type="image"></yz:fitem>
						</div>	
					</div>
					<div class="u-zciptct clearfix mb15">
						<label class="f-tt fl"><span class="type"></span>名称:</label>
						<p class="u-iptdw fl" style='width:184px;'>
							<input type="text" name="name" maxlength="20" placeholder="请输入您的名称" style='width:170px;' class="ml10">
						</p>
					</div> 
					<div class="typeyye">
					<div class="u-zciptct clearfix mb15">
						<label class="f-tt fl"><span class="type"></span>联系人:</label>
						<p class="u-iptdw fl" style='width:184px;'>
							<input type="text" name="contacts" style='width:170px;' class="ml10">
						</p>
					</div> 
					</div> 
					<div class="u-zciptct clearfix mb15 property">
						<label class="f-tt fl">联系电话:</label>
						<p class="u-iptdw fl" style='width:184px;'>
							<input type="text" name="serviceTel" style='width:170px;' class="ml10">
						</p>
					</div> 
					<div class="u-zciptct clearfix mb15 seller_info">
						<label class="f-tt fl"><span class="type"></span>经营类型:</label>
						<div class="fr" style="width:470px;"> 
						<div class="input-group">
	                        <table border="0">
	                             <tbody>
	                                <tr>
	                                    <td rowspan="2">
	                                        <select id="cate_1" name="cateIds" class="form-control" multiple="multiple" style="min-width:200px; *width:200px; height:260px;border:1px solid #ddd;"></select>
	                                    </td>
	                                    <td width="50" align="center" rowspan="2" style="max-height:250px;">
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
	                                       <select id="cate_2" class="form-control" multiple="multiple" style="min-width:200px; *width:200px; height:260px;border:1px solid #ddd;"> 
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
					</div>  
					<div class="u-zciptct u-zciptct2 clearfix mb15">
						<label class="f-tt fl"><span class="type"></span>所在地区:</label>
						<div class="fl" style="margin-left: -5px;"> 
						<yz:region pname="provinceId" cname="cityId" aname="areaId" ></yz:region> 
						</div> 
					</div> 
					<div class="u-zciptct u-zciptct2 clearfix mb15 seller_info">
						<label class="f-tt fl"><span class="type"></span>服务范围:</label> 
						<div class="fr" style="margin-top: -46px; margin-left:105px">   
							<yz:mapArea css="btn-gray ml5 shez" placeholder="　请输入你的详细地址并设置机构服务范围" width="480px" name="mapPos" pointVal="$data['mapPoint']" addressVal="$data['address']" posVal="$data['mapPos']"></yz:mapArea>  
						</div>	 
					</div> 
					<div class="u-zciptct u-zciptct2 clearfix mb15 property" id="-form-item">
						<label class="f-tt fl">服务小区:</label>
			            <div class="f-boxr">
			                <select id="districtId" name="districtId" class="sle fl" style='width:184px;'>
			                  	<option value="0" >请选择小区</option>
			                </select>
			                <p class="u-iptdw fl" style='width:155px;'>
								<input type="text" name="districtName" style='width:135px;' class="ml10" id="districtName">
							</p>
			                <span class="fr" style="margin-left:10px; margin-bottom: 8px; color:#ddd;">
				                <a class="btn dis_search" href="javascript:;">搜索</a>
				            </span>
			            </div>
					</div>
					<div class="u-zciptct u-zciptct2 clearfix mb15 property">
						<label>小区不在列表中？请联系客服：{{$site_config['seller_service_tel']}}</label>
					</div> 
					<p class="tc">
						<a href="javascript:;" id="registe2rrk" class="btn f-170btn">上一步</a>
						<a href="javascript:;" id="registernet4" class="btn f-170btn ml20">下一步</a>
					</p>
				</fieldset>
			</form>
			</div>
			
		</div>
	</div> 

	<div  id="register4" style="display:none">	
		<div class="m-bztt mt10">
			<img src="{{ asset('images/zc4.png') }}" alt="">
		</div>
		<div class="mt10 m-xyct">
			<div class="m-inforct">
			<p class="f20 lh85"><span class="type"></span>认证信息</p>
			<form action="">
				<fieldset>
					<legend><span class="type"></span>实名认证信息</legend> 
					<div class="typeyye">
						<div class="u-zciptct clearfix mb15"> 
							<div class="fl"> 	
								<yz:fitem name="businessLicenceImg" label="营业执照" type="image"></yz:fitem>   
							</div> 					
						</div>
					</div>
					<div class="pertype">
						<div class="u-zciptct clearfix mb15"> 
							<div class="fl"> 	
								<yz:fitem name="certificateImg" label="资质证书" type="image"></yz:fitem>   
							</div> 					
						</div>
					</div>
					<div class="u-zciptct clearfix mb15">
						<label class="f-tt fl">证件号码:</label>
						<p class="u-iptdw fl" style='width:350px;'>
							<input type="text" name="idcardSn" maxlength="18" placeholder="请输入您正确的身份证号码"style='width:320px;' class="ml10">
						</p> 					
					</div> 
					<div class="u-zciptct clearfix mb15"> 
						<div class="fl"> 	
							<yz:fitem name="idcardPositiveImg" label="身份证正面" type="image"></yz:fitem>  
							<yz:fitem name="idcardNegativeImg" label="身份证背面" type="image"></yz:fitem>  
						</div>
													
					</div>
					<p class="tc">
						<a href="javascript:;" id="registe4rrk" class="btn f-170btn">上一步</a>
						<span class="sbtn"></span>
					</p>
				</fieldset>
			</form>
			</div>
		</div>
	</div> 
	@yizan_end  
@stop

@section('js')
<script> 
 $('.date').datepicker({
	changeYear:true,
	changeMonth:true,
}); 
var obj = new Object(); 
	obj.sellerType	 =  "";
	obj.mobile   =  "";
	obj.pwd		 =  ""; 
	obj.name     =  ""; 
	obj.sex      =  "1"; 
	obj.cstime   =  "123456"; 
	obj.contacts =  "";
	obj.cityId   =  "";
	obj.areaId   =  "";
	obj.idcardSn =  "";
	obj.avatar   =  "";
	obj.mapPoint =  "";
	obj.address  =  "";
	obj.mapPos   =  ""; 
	obj.provinceId 	 =  "";
	obj.companyName  =  "";
	obj.verifyCode   =  "";
	obj.idcardPositiveImg  =    "";
	obj.idcardNegativeImg  =    "";
	obj.businessLicenceSn  =    "";
	obj.businessLicenceImg =    "";
	obj.certificateImg = ""; 
	obj.cateIds = ""; 
	obj.districtId = ""; 
	obj.serviceTel = ""; 
	var verifynum =  0;
	var click =  0;
	$("#register1").click(function(){ 
		click ++ ;
		$('#register2').show(); 
		$('#register').hide(); 
	});

	$("#registerrk").click(function(){ 
		click -- ;
		$('#register').show(); 
		$('#register2').hide(); 
	});

	$("#registernet").click(function(){ 
		click ++ ;		  
		obj.sellerType	= @if(OPERATION_VERSION == 'personal') 1; @else $('select option:selected') .val(); @endif//选中的值
		if(obj.sellerType == 1){
			$(".sllertype-person").removeClass("none").removeAttr("disabled");
		} 
		obj.mobile = $("input[name=mobile]").val();
		obj.verifyCode    = $("input[name=verify]").val();
		obj.pwd    = $("input[name=pwd]").val(); 
		var imgverify = $("input[name=imgverify]").val();		
		if(obj.sellerType == 1){
			$(".type").text("");
			$(".typeyye").hide();
			$(".property").hide();
		}else if(obj.sellerType == 2){
			$(".type").text("商家");
			$(".pertype").hide();
			$(".property").hide();
			$("#avatar-form-item span").text("请上传logo");
		} else {
			$(".type").text("公司");
			$(".seller_info").hide();
			$(".pertype").hide();
		}
		if(obj.sellerType != 0){
			var reg = /^1[\d+]{10}$/;
			if(!reg.test(obj.mobile)){
				alert('请输入正确的手机号码');  
				return false; 
			} 
			if(obj.verifyCode == ""){
				alert("验证码不能为空");
				return false;
			} 
			if(obj.pwd==""){
				alert("密码不能为空");
				return false;
			}else{
				if(obj.pwd.length < 6 || obj.pwd.length > 12  ){
					alert("密码长度不合法");
					return false;
				}else if(obj.pwd != $("input[name=pwdtwo]").val()){
					alert("两次密码不正确");
					return false;
				} 
			} 
			$.post("{{ u('public/verify') }}",{mobile:obj.mobile,vertype:'checktelcode',verifyCode:obj.verifyCode,imgverify:imgverify},function(result){ 
				if(result['code']  == '10104'){
					alert(result.msg);
					return false;
				} else if(result['code']  == '10118'){
					alert(result.msg,5);
					return false;
				}else if(result['code']  == '0'){					
					$('#register3').show(); 
				    $('#register2').hide(); 
				    $(".sbtn").html("");
				}
			},'json');
		}else{
			alert("请选择加盟类型");
			return false;			
		}
	});

	$("#registe2rrk").click(function(){  
		click -- ;
		$('#register2').show(); 
		$('#register3').hide(); 		
		$(".sbtn").html("");		 
	});

	$("#registernet4").click(function(){ 
		click ++ ;
		obj.avatar    = $("input[name=avatar]").val();
		obj.name = $("input[name=name]").val();
		//obj.sex    = $("input[name=sex]").val();
		//obj.cstime    = $("input[name=cstime]").val();
 		obj.contacts	= $("input[name=contacts]").val();
		obj.provinceId    = $("select[name=provinceId]").val(); 
		obj.cityId    = $("select[name=cityId]").val(); 
		obj.areaId    = $("select[name=areaId]").val();   
		obj.mapPoint   = $("input[name=mapPoint]").val();
		obj.address   = $("input[name=address]").val(); 
		var cateIds = new Array();
		$('select[name=cateIds] option').each(function(){ 
			cateIds.push($(this).val()); 
		});
		obj.cateIds = cateIds;
		obj.districtId  = $("select[name=districtId]").val(); 
		obj.serviceTel  = $("input[name=serviceTel]").val(); 
		//alert(obj.cateIds);
		if(obj.mobile != ""){
			var reg = /^1[\d+]{10}$/;
			if(!reg.test(obj.mobile)){
				alert('请输入正确的手机号码'); 
				return false;
			}
		}else{
			alert("手机号码不能为空");
			return false;
		}
		if (obj.sellerType != 3) {
			if(obj.avatar ==""){
				alert("还是为自己选个头像吧！");
				return false;
			}
			if(obj.address==""){
				alert("服务地址不能为空");
				return false;
			} 
		} else {
			if (obj.serviceTel == '') {
				alert("请填写联系电话");
				return false;
			};
			if (obj.districtId < 1) {
				alert("请选择小区");
				return false;
			};
		}
		if(obj.verify == ""){
			alert("验证码不能为空");
			return false;
		}
		if(obj.pwd==""){
			alert("密码不能为空");
			return false;
		}
		if(obj.name==""){
			alert("名称不能为空");
			return false;
		}
		if(obj.provinceId==""){
			alert("选择城市");
			return false;
		}
		
		var maplatLngs = new Array();
        qqPolygon1.getPath().forEach(function(element, index){
            maplatLngs.push(element.getLat() + "," + element.getLng());
            $("#map-pos-1").val(maplatLngs.join("|"));
        });         
		obj.mapPos   =	$("input[name=mapPos]").val(); 
		$('#register3').hide(); 
		$('#register4').show();  		
		$(".sbtn").append('<a href="javascript:;" id="bntreg" class="btn f-170btn ml20">提交</a>'); 	 
	});	

	$("#registe4rrk").click(function(){ 
		click -- ;
		$('#register3').show(); 
		$('#register4').hide(); 
		$(".sbtn").html("");	 
	});

	$(".sbtn").click(function(){ 
		obj.idcardSn 		      = $("input[name=idcardSn]").val();
		obj.certificateImg		  = $("input[name=certificateImg]").val();
		obj.idcardPositiveImg     = $("input[name=idcardPositiveImg]").val();
		obj.idcardNegativeImg     = $("input[name=idcardNegativeImg]").val();
		//obj.businessLicenceSn     = $("input[name=businessLicenceSn]").val();
		obj.businessLicenceImg    = $("input[name=businessLicenceImg]").val();
		obj.idcardNegativeImgName = $("input[name=idcardNegativeImgName]").val(); 
		if(obj.sellerType != 1){
			if(obj.businessLicenceImg == ""){
				alert("营业执照图片不能为空");
				return false;
			}  
		}
		if(obj.sellerType == 1){
			if(obj.certificateImg == ""){
				alert("资质证书图片不能为空");
				return false;
			}  
		}
		var regs = /(^\d{15}$)|(^\d{18}$)|(^\d{17}(\d|X|x)$)/;  
	   	if(regs.test(obj.idcardSn) === false)  
	   	{  
	       alert("证件号码输入不合法");  
	       return  false;  
	   	}   
		if(obj.idcardPositiveImg == ""){
			alert("请上传证件号码证件正面");
			return false;
		} 
		if(obj.idcardNegativeImg == ""){
			alert("请上传证件号码证件背面");
			return false;
		}
		if(obj.sellerType != 0){			 
			$.post("{{ u('public/doregister') }}",obj,function(result){  
				if(result['code'] == 0 ){
					alert('入驻申请已提交成功！后台审核成功后我们将尽快通知您！请耐心等待！',2);
				} else {
					if(result.msg == "10118" ){
						alert(result.msg,5);
					}else{
						alert(result.msg);							
					}
				}
			},'json'); 
		}else{
			alert("加盟类型未选择");
			return false;
		}
	});	
	
	// $(".verify").click(function(){
	// 	obj.sellerType	= $('select option:selected') .val();//选中的值 
	// 	if(obj.sellerType != 0){
	// 		obj.mobile = $("input[name=mobile]").val();
	// 		if(obj.mobile != ""){ 
	// 			if(!/^1[\d+]{10}$/i.test(obj.mobile)){ 
	// 				alert('请输入正确的手机号码'); 
	// 				return false;
	// 			}
	// 		}else{
	// 			alert("手机号码不能为空");
	// 			return false;
	// 		}  
	// 		$.post("{{ u('public/verify') }}",{mobile:obj.mobile,type:'reg',vertype:"mobileverify"},function(result){
	// 			if(result.code == "10118" ){
	// 				alert(result.msg,5); 
	// 			}else if(result.code == "9999" ){
	// 				alert(result.msg); 
	// 			}else{
	// 				verifynum ++;
	// 				time();					
	// 			}
	// 		},'json');
	// 	}else{
	// 		alert("加盟类型未选择");
	// 		return false;
	// 	} 
	// });
	var path = "{!! u('Public/imgverify')!!}?random=" + Math.random();
	$(".imgverify").click(function(){
		$(".verifynotice").removeClass('none').show();
	});	
	$("#changeimg").click(function(){
		$("#imgverify").attr('src',path);
	});	
	$(".canver").click(function(){
        $('.verifynotice').addClass('none').hide();
        $("input[name=imgverify]").empty();
        $("#imgverify").attr('src',path);
    });
	$(".checkverify").click(function(){
		obj.sellerType	= $('select option:selected') .val();//选中的值 
		if(obj.sellerType != 0){
			var imgverify = $("input[name=imgverify]").val();
			obj.mobile = $("input[name=mobile]").val();
			if(obj.mobile != ""){ 
				if(!/^(13[0-9]|14[0-9]|15[0-9]|18[0-9])\d{8}$/i.test(obj.mobile)){ 
					alert('请输入正确的手机号码'); 
					return false;
				}
			}else{
				alert("手机号码不能为空");
				return false;
			}  
			$.post("{{ u('Public/verify') }}",{mobile:obj.mobile,type:'reg',vertype:"mobileverify",imgverify:imgverify},function(result){
				if(result.code == 0 ){
					verifynum ++;
					time();		
					$("input[name=imgverify]").empty();
					$('.verifynotice').addClass('none').hide();		
				}else{
					$("#imgverify").attr('src',path);
					alert(result.msg); 
				}
			},'json');
		}else{
			alert("加盟类型未选择");
			return false;
		} 
	});


var wait = 120; 
function time() {    
    if (wait == 0) { 
		$(".verify").removeAttr("disabled") ;
    	$("#seltype").removeAttr("disabled");
        $(".verify").text("免费获取验证码");  
		$("#mobile").removeAttr("readonly");
        wait = 120; 
    } else { 
    	$("#seltype").attr("disabled","true");
        $(".verify").attr('disabled',"true");
        $(".verify").text(wait + "秒后获取验证码"); 
		$("#mobile").attr("readonly","readonly");
        wait--; 
        setTimeout(function () {
            time();
        },
        1000)
    }
}

$('#register2').hide(); 
$('#register3').hide();
$('.appointdate').datepicker({});  
function getDistrict(name, provinceId, cityId, areaId){
	$.post("{{u('public/search')}}", {name:name, provinceId:provinceId,cityId:cityId,areaId:areaId}, function(res){
		if(res.data.length == 0){
			$("#districtId").html('<option value="0" >所选区域内未找到小区</option>');
		} else {
			var html = "<option value='0' >请选择小区</option>";
			for (var i = res.data.length - 1; i >= 0; i--) {
				var data = res.data[i];
				html += "<option value='"+data.id+"' >"+data.name+"</option>";
			};
			$("#districtId").html(html);
		} 
	}, 'json');
} 
$(function(){
	$("#provinceId").change(function(){
		provinceId = $(this).val();
		cityId = $("#cityId").val() != null ? $("#cityId").val() : 0;
		areaId = $("#areaId").val() != null ? $("#areaId").val() : 0;
		getDistrict('',provinceId, cityId, areaId);
	});
	$("#cityId").change(function(){
		provinceId = $("#provinceId").val() != null ? $("#provinceId").val() : 0;
		cityId = $(this).val();
		areaId = $("#areaId").val() != null ? $("#areaId").val() : 0;
		getDistrict('',provinceId, cityId, areaId);
	});
	$("#areaId").change(function(){
		provinceId = $("#provinceId").val() != null ? $("#provinceId").val() : 0;
		cityId = $("#cityId").val() != null ? $("#cityId").val() : 0;
		areaId = $(this).val();
		getDistrict('',provinceId, cityId, areaId);
	});
	$("#provinceId").trigger('change');

	$(".dis_search").click(function() {
		var districtName = $("#districtName").val();
		provinceId = $("#provinceId").val() != null ? $("#provinceId").val() : 0;
		cityId = $("#cityId").val() != null ? $("#cityId").val() : 0;
		areaId = $("#areaId").val() != null ? $("#areaId").val() : 0;
		if (districtName != '') {
			getDistrict(districtName,provinceId, cityId, areaId);
		};
	})
})
</script>
@stop