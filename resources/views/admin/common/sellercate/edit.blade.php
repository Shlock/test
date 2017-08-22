@extends('admin._layouts.base')
@section('css')
<style type="text/css">
	.ts1{color: #ccc;margin-left: 5px;}
</style>
@stop
@section('right_content')
	@yizan_begin
		<yz:form id="yz_form" action="save">
			<yz:fitem name="name" label="分类名称" attr="maxlength='20'"></yz:fitem> 
			<div id="seller-type-form-item" class="u-fitem clearfix ">
	            <span class="f-tt">
	                 所属分类:
	            </span>
	            <div class="f-boxr">
                  	<select id="pid" name="pid" style="min-width:234px;width:auto" class="sle  ">
                  		@foreach($cate as $item)	                	
                  		<option data-type="{{$item['type']}}" value="{{$item['id']}}" @if($data['pid'] == $item['id']) selected @endif>{{$item['name']}}</option>
                  		@endforeach 
					</select>
					<span class="ts ts1"></span>
	            </div>
	        </div>
	        <div id="choose-type-form-item" style="display:none">
			<yz:fitem label="类型">
				<php> $type = isset($data['type']) ? $data['type'] : 1 </php>
				<yz:radio name="type" options="1,2" texts="商品,服务" checked="$type"></yz:radio>
			</yz:fitem>
			</div>
			<div id="single-type-form-item" class="u-fitem clearfix " style="display:none">
	            <span class="f-tt">
	                 类型:
	            </span>
	            <div class="f-boxr">
	            	<label id="type_label" style="margin-left:10px;">商品</label>
	                <input type="hidden" name="type" id="type" class="u-ipttext" value="100">
	            </div>
	        </div>
			<yz:fitem name="logo" label="图标" type="image"></yz:fitem>
			<yz:fitem label="排序">
				<input type="text" name="sort" class="u-ipttext" defalut="100" value="{{ $data['sort'] }}"onkeyup="if(isNaN(value))execCommand('undo')" onafterpaste="if(isNaN(value))execCommand('undo')">
			</yz:fitem>
			<yz:fitem label="状态">
				<php> $status = isset($data['status']) ? $data['status'] : 1 </php>
				<yz:radio name="status" options="1,0" texts="开启,关闭" checked="$status"></yz:radio>
			</yz:fitem>
		</yz:form>
	@yizan_end
@stop
@section('js')
<script type="text/javascript">
//排除自身（无限极）
/*var son = eval( {{$son}} );
//排除二级（限制2级）
var levs = eval( {{$levs}} );
//是否存在子集（限制2级）
var hasson = eval( {{$hasson}} );*/
$(function(){
	/*if(son){
		for (var i = son.length - 1; i >= 0; i--) {
			$("#pid option[value='"+son[i]+"']").attr("disabled","disabled");
		};
	}

	if(levs){
		$.each(levs,function(key,value){
			$("#pid option[value='"+value+"']").attr("disabled","disabled");
		});
	}

	if(hasson==1){
		$("#pid").attr("disabled","disabled");
		$(".ts1").text("不允许移动存在子集的分类");
		$("#yz_form").append("<input name='pid' type='hidden' value='0'>");
	}*/
	
	$("#pid").change(function(){
		var type = $(this).find("option:selected").data('type');
		var value = $(this).val(); 
		if(value > 0){
			$("#choose-type-form-item").hide();
			$("#choose-type-form-item input").attr('disabled','disabled');
			$("#single-type-form-item").show();
			$("#single-type-form-item input").removeAttr('disabled');
			if(type == 1){
				$("#type_label").text('商品');
			} else {
				$("#type_label").text('服务'); 
			}
			$("#type").val(type); 
		} else {
			$("#choose-type-form-item").show();
			$("#choose-type-form-item input").removeAttr('disabled');
			$("#single-type-form-item").hide();
			$("#single-type-form-item input").attr('disabled','disabled'); 
		} 
	}).trigger('change');

	$("input[name='sort']").attr("maxlength","3");
})
</script>
@stop