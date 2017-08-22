@extends('admin._layouts.base')
@section('css') 
@stop
@section('right_content')
	@yizan_begin 
		<yz:form id="yz_form" action="save">
			<yz:fitem name="mobile" label="帐号/登录手机号" attr="maxlength='11'"></yz:fitem> 
			<yz:fitem name="pwd" label="密码" attr="maxlength='20'"></yz:fitem> 
			<yz:fitem name="name" label="公司名称"></yz:fitem> 
			<yz:fitem name="provinceId" label="所在地区">
				<yz:region pname="provinceId" pval="$data['province']['id']" cname="cityId" cval="$data['city']['id']" aname="areaId" aval="$data['area']['id']"></yz:region>
			</yz:fitem>
			@if($data) 
			<div id="-form-item" class="u-fitem clearfix ">
	            <span class="f-tt">
	                 小区名称:
	            </span>
	            <div class="f-boxr">
				{{$data['district']['name']}}
	            </div>
	            <span class="" style="margin-left:10px">
	                <a class="btn" href="{{u('district/edit',['id'=>$data['district']['id']])}}">编辑小区</a>
	            </span>
	            <input type="hidden" name="districtId" value="{{$data['district']['id']}}" />
	        </div>	
			@else
			<div id="-form-item" class="u-fitem clearfix ">
	            <span class="f-tt">
	                 小区名称:
	            </span>
	            <div class="f-boxr">
	                <select id="districtId" name="districtId" class="sle">
	                  	<option value="0" >请选择小区</option>
	                </select>
	            </div>
	            <span class="" style="margin-left:10px">
	                <a class="btn" href="{{u('district/create')}}">添加小区</a>
	            </span>
	        </div>
			@endif
			<yz:fitem name="contacts" label="联系人" ></yz:fitem> 
			<yz:fitem name="serviceTel" label="联系电话"></yz:fitem> 
			<yz:fitem name="authenticate.idcardSn" label="身份号码"></yz:fitem> 
			<yz:fitem name="authenticate.idcardPositiveImg" label="身份证正面" type="image"></yz:fitem>
			<yz:fitem name="authenticate.idcardNegativeImg" label="身份证背面" type="image"></yz:fitem>
			<yz:fitem name="authenticate.businessLicenceImg" label="营业执照" type="image"></yz:fitem>
		</yz:form> 
	@yizan_end
	<script type="text/javascript">
		function getDistrict(provinceId, cityId, areaId){
			$.post("{{u('District/search')}}", {provinceId:provinceId,cityId:cityId,areaId:areaId}, function(res){
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
				getDistrict(provinceId, cityId, areaId);
			});
			$("#cityId").change(function(){
				provinceId = $("#provinceId").val() != null ? $("#provinceId").val() : 0;
				cityId = $(this).val();
				areaId = $("#areaId").val() != null ? $("#areaId").val() : 0;
				getDistrict(provinceId, cityId, areaId);
			});
			$("#areaId").change(function(){
				provinceId = $("#provinceId").val() != null ? $("#provinceId").val() : 0;
				cityId = $("#cityId").val() != null ? $("#cityId").val() : 0;
				areaId = $(this).val();
				getDistrict(provinceId, cityId, areaId);
			});
			$("#provinceId").trigger('change');
		})
	</script>
@stop