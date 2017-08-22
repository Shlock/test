@extends('admin._layouts.base')
@section('css') 
@stop
@section('right_content')
	@yizan_begin 
		<yz:form id="yz_form" action="dispose">
			<input type="hidden" name="id" value="{{$data['id']}}" >
			<div id="mobile-form-item" class="u-fitem clearfix ">
	            <span class="f-tt">
	                 帐号/登录手机号:
	            </span>
	            <div class="f-boxr">
	                {{$data['mobile']}}
	            </div>
	        </div>
	        <div id="name-form-item" class="u-fitem clearfix ">
	            <span class="f-tt">
	                 公司名称:
	            </span>
	            <div class="f-boxr">
	                {{$data['name']}}
	            </div>
	        </div>  
			<div id="-form-item" class="u-fitem clearfix ">
	            <span class="f-tt">
	                 小区名称:
	            </span>
	            <div class="f-boxr">
	                {{$data['district']['name']}}
	            </div> 
	        </div>
			<div id="-form-item" class="u-fitem clearfix ">
	            <span class="f-tt">
	                 联系人:
	            </span>
	            <div class="f-boxr">
	                {{$data['contacts']}}
	            </div> 
	        </div>
			<div id="-form-item" class="u-fitem clearfix ">
	            <span class="f-tt">
	                 联系电话:
	            </span>
	            <div class="f-boxr">
	                {{$data['serviceTel']}}
	            </div> 
	        </div>
			<div id="-form-item" class="u-fitem clearfix ">
	            <span class="f-tt">
	                 身份号码:
	            </span>
	            <div class="f-boxr">
	                {{$data['authenticate']['idcardSn']}}
	            </div> 
	        </div> 
			<div id="idcardPositiveImg-form-item" class="u-fitem clearfix ">
	            <span class="f-tt">
	                 身份证正面:
	            </span>
	            <div class="f-boxr">
	                  <ul class="m-tpyllst clearfix">
            				<li id="idcardPositiveImg-box" class="">
                                <a id="img-preview-idcardPositiveImg" class="m-tpyllst-img" href="javascript:;" target="_self">
                                	<img src="{{$data['authenticate']['idcardPositiveImg']}}" alt=""  >
                                </a>
            				</li>
						</ul>
	            </div>
	        </div>
			<div id="idcardNegativeImg-form-item" class="u-fitem clearfix ">
	            <span class="f-tt">
	                 身份证背面:
	            </span>
	            <div class="f-boxr">
	                  <ul class="m-tpyllst clearfix">
            				<li id="idcardNegativeImg-box" class="">
                                <a id="img-preview-idcardNegativeImg" class="m-tpyllst-img" href="javascript:;" target="_self">
                                	<img src="{{$data['authenticate']['idcardNegativeImg']}}" alt=""  >
                                </a>
            				</li>
						</ul>
	            </div>
	        </div>
			<div id="businessLicenceImg-form-item" class="u-fitem clearfix ">
	            <span class="f-tt">
	                 营业执照:
	            </span>
	            <div class="f-boxr">
	                  <ul class="m-tpyllst clearfix">
            				<li id="businessLicenceImg-box" class="">
                                <a id="img-preview-businessLicenceImg" class="m-tpyllst-img" href="javascript:;" target="_self">
                                	<img src="{{$data['authenticate']['businessLicenceImg']}}" alt=""  >
                                </a>
            				</li>
						</ul>
	            </div>
	        </div> 
	        @if($data['isCheck'] == -1)
			<div id="-form-item" class="u-fitem clearfix ">
	            <span class="f-tt">
	                 拒绝原因:
	            </span>
	            <div class="f-boxr">
	                {{$data['checkVal']}}
	            </div> 
	        </div> 
	        <div class="u-antt tc" style=" background: #ffffff;">
                <a href="{{u('PropertyApply/index')}}" class="mt15 ml15 on m-sjglbcbtn">返回</a>
            </div>
	        @endif
			@if($data['isCheck'] == 0)
			<yz:fitem name="checkVal" label="审核原因" type="textarea"></yz:fitem>
			<yz:fitem label="状态">
   				<yz:radio name="isCheck" options="-1, 1" texts="拒绝, 同意" checked="1"></yz:radio>
   			</yz:fitem>
   			@endif
		</yz:form> 
	@yizan_end 
	@if($data['isCheck'] != 0)
	<script type="text/javascript">
		$(".u-addspbtn").parent().parent().remove();
	</script>
	@endif
@stop