@extends('admin._layouts.base')
@section('css')
@stop
@section('right_content')
@yizan_begin
	<yz:form id="yz_form" action="update"> 
		<yz:fitem name="avatar" label="头像" type="image"></yz:fitem>  
		<yz:fitem name="mobile" label="手机号码"></yz:fitem>  
		<yz:fitem name="name" label="昵称"></yz:fitem>    
		<yz:fitem name="pwd"  label="密码" type="password" tip="不修改请保留为空"></yz:fitem>   
		<yz:fitem label="余额" >
			<p>￥{{$data['balance'] or '0.00'}}</p>
		</yz:fitem>  
		<yz:fitem name="status" label="会员状态">
			<yz:radio name="status" options="0,1" texts="锁定,正常" checked="$data['status']"></yz:radio>
		</yz:fitem>
		<yz:fitem name="regIp" label="注册IP" type="text"></yz:fitem>
		<yz:fitem name="recommendUser" label="推荐人" type="text"></yz:fitem> 
		<yz:fitem name="regAddress" label="注册时地址" type="text"></yz:fitem> 
		<yz:fitem name="LatLng" label="注册时地址坐标" type="text"></yz:fitem> 

		<yz:fitem name="isExtensionWorker" label="是否推广人员">
			<yz:radio name="isExtensionWorker" options="0,1" texts="否,是" checked="$data['isExtensionWorker']"></yz:radio>
		</yz:fitem>
		<yz:fitem name="extensionAddress" label="推广的地理位置"></yz:fitem> 
		<yz:fitem name="extensionLatLng" label="推广位置坐标" ></yz:fitem> 

		<div id="mapPointPicker-form-item" class="u-fitem clearfix ">
            <span class="f-tt">
                &nbsp;
            </span>
            <div class="f-boxr">
                通过<a target="_blank" href="http://www.mayixueyuan.com/tool/baidumapapi/">百度坐标拾取器</a>获得精确的位置坐标
            </div>
        </div>

		<yz:fitem name="extensionRange" label="推广有效范围" tip="公里"></yz:fitem> 
	</yz:form>
	<script type="text/javascript">
		$(document).ready(function(){
			$('#isExtensionWorker-form-item').find('.uniform').click(function(){
				//alert($(this).val());
				if($(this).val() == 1){
					$('#extensionAddress-form-item').show();
					$('#extensionLatLng-form-item').show();
					$('#extensionAddress-form-item').show();
					$('#mapPointPicker-form-item').show();
					$('#extensionRange-form-item').show();
				}else{
					$('#extensionAddress-form-item').hide();
					$('#extensionLatLng-form-item').hide();
					$('#extensionAddress-form-item').hide();
					$('#mapPointPicker-form-item').hide();
					$('#extensionRange-form-item').hide();
				}
			});

			if($('#yz_form input[name="isExtensionWorker"]:checked ').val()==1){
				$('#extensionAddress-form-item').show();
				$('#extensionLatLng-form-item').show();
				$('#extensionAddress-form-item').show();
				$('#mapPointPicker-form-item').show();
				$('#extensionRange-form-item').show();
			}else{
				$('#extensionAddress-form-item').hide();
				$('#extensionLatLng-form-item').hide();
				$('#extensionAddress-form-item').hide();
				$('#mapPointPicker-form-item').hide();
				$('#extensionRange-form-item').hide();
		}
		});
	</script>
@foreach ($errors->all() as $error)
    <p class="error">{{ $error }}</p>
 @endforeach
 
@yizan_end

@stop 