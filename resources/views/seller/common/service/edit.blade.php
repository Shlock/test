@extends('admin._layouts.base')
@section('css')
<style type="text/css">
.m-spboxlst .f-boxr {
	width: 610px;
}
</style>
@stop
@section('right_content')
@yizan_begin
<yz:form id="yz_form" action="save">
	<dl class="m-ddl">
		<dt>服务人员信息</dt>
		<dd class="clearfix">
			<yz:fitem name="type" type="hidden" val="{{$data['type']}}"></yz:fitem>
			<yz:fitem name="mobile" label="手机号"></yz:fitem>
			@if($data['id'])
			<yz:fitem name="pwd"  label="密码" type="password" tip="不修改请保留为空"></yz:fitem>
			@else
			<yz:fitem name="pwd"  label="密码" type="password">
				<attrs>
					<btip><![CDATA[如果手机号未注册会员，必须设置密码；<br/>如果手机号已经注册过会员，设置密码将重置会员登录密码；]]></btip>
				</attrs>
			</yz:fitem>
			@endif
			<yz:fitem name="name" label="商家名称"></yz:fitem>
			<yz:fitem name="contacts" label="负责人"></yz:fitem>
			<yz:fitem label="分类">
                @if($cateIds)
                <yz:checkbox name="cateIds." options="$cateIds" textfield="name" valuefield="id" checked="$_cateIds"></yz:checkbox>
                @else
                <span style="color:#aaa">您还未设置分类，<a href="{{ u('SellerCate/index') }}">点击此处</a> 添加</span>
                @endif
            </yz:fitem>
			<yz:fitem name="logo" label="商家图片" type="image"></yz:fitem>
			<yz:fitem name="brief" label="简介" type="textarea"></yz:fitem>
		</dd>
		<yz:fitem name="provinceId" label="所在地区">
			<yz:region pname="provinceId" pval="$data['province']['id']" cname="cityId" cval="$data['city']['id']" aname="areaId" aval="$data['area']['id']"></yz:region>
		</yz:fitem>
		<yz:fitem name="mapPos" label="服务范围">
			<yz:mapArea name="mapPos" pointVal="$data['mapPoint']" addressVal="$data['address']" posVal="$data['mapPos']"></yz:mapArea>
		</yz:fitem>
	</dl>
	<dl class="m-ddl">
		<dt>认证信息</dt>
		<dd class="clearfix">
			<yz:fitem name="idcardSn" label="身份证编号"></yz:fitem>  
			<yz:fitem name="idcardPositiveImg" label="身份证正面" type="image"></yz:fitem>  
			<yz:fitem name="idcardNegativeImg" label="身份证背面" type="image"></yz:fitem> 	
			@if($data['type'] == 2)
			<yz:fitem name="businessLicenceImg" label="营业执照图片" type="image"></yz:fitem> 
			@endif
		</dd>
	</dl>
	<dl class="m-ddl">
		<dt>营业设置</dt>
		<dd class="clearfix" style="padding:15px;"> 
			@include('admin.common.service.showtime') 
            @include('admin.common.service.sztime') 
		</dd>
		<dd class="clearfix">
			<yz:fitem name="serviceFee" label="起送费"></yz:fitem>
			<yz:fitem name="deliveryFee" label="配送费"></yz:fitem>
		</dd>
	</dl>
	<dl class="m-ddl">
		<dt>银行卡</dt>
		<dd class="clearfix">
			<div class="list-btns">
              <a href="javascript:;" class="btn mr5 addbank" >
                添加银行卡
            </a>
        </div>
		<div class="m-tab">
		<table id="checkListTable" class="">
        <thead>
        	<tr><td class="" width="100" order="bank" code="bank"><span>开户行</span></td>
			<td class="" width="150" order="bankNo" code="bankNo"><span>卡号</span></td>
			<td class="" order="name" code="name" width="100"><span>开户名</span></td> 
			<td class="" width="100" order="mobile" code="mobile"><span>手机号</span></td>
			<td style="text-align:center;white-space:nowrap;" width="60"><span>操作</span></td>
            </tr>
        </thead>
        <tbody>
			@foreach($data['banks'] as $item)
            <tr class="tr-68 tr-even" ><td class="" code="bank">{{$item['bank']}}</td>
			<td class="" style="text-align:left;" code="bankNo">{{$item['bankNo']}}</td>
			<td class="" style="text-align:left;" code="name">{{$item['name']}}</td> 
			<td class="" code="mobile">{{$item['mobile']}}</td>
			<td class="">

				<p><a href="javascript:;" class=" red" onclick="$.RemoveItem(this, '{{ u('Service/delbank',['id'=>$item['id'],'sellerId'=>$data['id']])}}', '你确定要删除该数据吗？')" target="_self">删除</a></p>
			</td>
            </tr>
			@endforeach
        </tbody>
        </table>
        </div>
		</dd>
	</dl>
</yz:form>

<div class="u-frct fl" style="width:600px; display:none;border:1px solid #ececec;background-color:#ececec;position: fixed; top: 50%; left: 50%; margin-left: -200px; margin-top: -200px; z-index: 99;" id="bankinfo">
	<div class="m-spboxlst " style="">
		<form class="validate-form ajax-form" method="post" enctype="application/x-www-form-urlencoded" target="_self" novalidate="novalidate">
		<div class="pageBox page_1">
            <div class="m-zjgltbg">
                <div class="p10">
                	<p class="f-bhtt f14 clearfix" style="border-bottom: none;">
                        <a href="javascript:$('#bankinfo').addClass('none').hide();" class="mr15 btn f-bluebtn" style="margin-top:8px;color:#ffffff;">取消</a>
                    </p>
                <div class="g-szzllst pt10">
				<div class="u-fitem clearfix ">
		            <span class="f-tt">
		                 卡号:
		            </span>
		            <div class="f-boxr">
		                  <input type="text" name="bankNo" id="bankNo" class="u-ipttext" value="">
		            </div>
		        </div>
				<div class="u-fitem clearfix ">
		            <span class="f-tt">
		                 开户行:
		            </span>
		            <div class="f-boxr">
		                  <input type="text" name="bank" id="bank" class="u-ipttext" value="">
		            </div>
		        </div>
				<div class="u-fitem clearfix ">
		            <span class="f-tt">
		                 户主:
		            </span>
		            <div class="f-boxr">
		                  <input type="text" name="bank_name" id="bank_name" class="u-ipttext" value="">
		            </div>
		        </div>
				<div class="u-fitem clearfix ">
		            <span class="f-tt">
		                手机号码:
		            </span>
		            <div class="f-boxr">
		                  <input type="text" name="bank_mobile" id="bank_mobile" class="u-ipttext" value="" maxlength="11"> 
		            </div>
		        </div>
				</div>
                </div>
            </div>
        </div>		
        <div class="u-fitem clearfix">
                <span class="f-tt">&nbsp;</span>
                <div class="f-boxr">
                      <button type="button" class="u-addspbtn" id="bank_submit">提 交</button>
                </div>
            </div>		
		</form>
	</div>

</div>
@yizan_end
@stop

@section('js')
<script type="text/javascript">
	$(function(){
		$(".addbank").click(function() {
			$("#bankinfo").removeClass("none").show();
		})
		
		$("#bank_submit").click(function() {
			var bank = $("#bank").val();
			var bankNo = $("#bankNo").val();
			var mobile = $("#bank_mobile").val();
			var name = $("#bank_name").val();
			var id = "{{$data['id']}}";
			$.post("{{ u('Service/banksave') }}", {'bank':bank,'bankNo':bankNo,'mobile':mobile,'id':id,'name':name} ,function(res){
		      if(res.code == 0) {
		        $.showSuccess(res.msg, "u('Service/edit',['id'=>$data['id']])");
		      }else{
		        $.showError(res.msg);
		      }
		    },"json");

		})


	})

</script>
@stop