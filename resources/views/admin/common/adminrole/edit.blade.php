@extends('admin._layouts.base')
@section('css')
<style>
fieldset {
	border: solid 1px #999;
	border-radius: 4px;
	width: 80%;
	font-size:14px;
}

fieldset legend {
	padding: 0 5px;
	width:auto;
	border:none;
	margin:0;
    font-size:14px;
}

fieldset div.actions {
	width: 96%;
	margin: 5px 5px;
}

fieldset div label{display:inline-block; margin-right:15px;}

.blank15 {
	height: 15px;
	line-height: 10px;
	clear: both;
	visibility: hidden;
}

.actions label{margin-right:10px!important;}

.my_fieldset{width: 100%;}
</style>
@stop
@section('right_content')
	@yizan_begin
		<form id="yz_form" name="yz_form" class="validate-form ajax-form" method="post" action="{{ u('AdminRole/save') }}" enctype="application/x-www-form-urlencoded" target="_self" novalidate="novalidate">
			<div class="u-fitem clearfix name-form-item">
	            <span class="f-tt">组名称:</span>
	            <div class="f-boxr">
	                  <input type="text" name="name" id="name" class="u-ipttext" value="{{$data['name']}}">
	            </div>
	        </div>
	        <div class="u-fitem clearfix name-form-item">
	            <span class="f-tt">组权限:</span>
	        </div>
            <div class="formdiv" style="width:700px;">
                <!-- @foreach( $role as $vkey => $v ) -->
		                <fieldset class="fieldset-1">
		                    <legend class="checked_all">
		                    	<label><input type="checkbox" class="uniform" data-level="1" /><span>{{$v['name']}}</span></label>
		                    </legend>
		                    <div class="actions">
		                    	<!-- @if(is_array($v['nodes'])) -->
			                        <!-- @foreach($v['nodes'] as $vckey => $vc) -->
			                        <fieldset class="my_fieldset fieldset-2">
					                    <legend  class="checked_module">
					                    	<label>
					                    		<input type="checkbox" class="uniform"  value="{{$vckey}}" data-level="2" />
					                    		<span>{{$vc['name']}}</span>
					                    	</label>
					                    </legend>
					                    <div class="actions fieldset-3">
					                        <!-- @foreach($vc['controllers'] as $conkey => $con) -->
					                        	<!-- @foreach($con['actions'] as $vakey => $va) -->
							                        <label>
							                        	<input type="checkbox" class="uniform" name="{{$conkey}}[]" value="{{$vakey}}" data-level="3" 
							                        	@if( isset($access[$conkey][$vakey]) ) checked @endif />
							                        	<span>{{$va['name']}}</span>
							                        </label>
					                        	<!-- @endforeach -->
					                        <!-- @endforeach -->
					                    </div>
				                    </fieldset>
			                        <!-- @endforeach -->
			                    <!-- @endif -->
			                    <!-- @if(is_array($v['controllers'])) -->
			                        <!-- @foreach($v['controllers'] as $vckey => $vc) -->
			                        <fieldset class="my_fieldset fieldset-2">
					                    <legend  class="checked_module">
					                    	<label>
					                    		<input type="checkbox" class="uniform"  value="{{$vckey}}" data-level="2" />
					                    		<span>{{$vc['name']}}</span>
					                    	</label>
					                    </legend>
					                    <div class="actions fieldset-3">
					                        <!-- @foreach($vc['actions'] as $vakey => $va) -->
					                        <label>
					                        	<input type="checkbox" class="uniform" name="{{$vckey}}[]" value="{{$vakey}}" data-level="3" 
					                        	@if( isset($access[$vckey][$vakey]) ) checked @endif />
					                        	<span>{{$va['name']}}</span>
					                        </label>
					                        <!-- @endforeach -->
					                    </div>
				                    </fieldset>
			                        <!-- @endforeach -->
		                	<!-- @endif -->
		                    </div>
		                </fieldset>
		                <div class="blank15"></div>
                <!-- @endforeach -->
            </div>
            <input type="hidden" name="id" value="{{$data['id']}}">
            <p class="tc">
				<button type="submit" class="u-addspbtn">提 交</button>
			</p>
		</form>
	@yizan_end
@stop
@section('js')
<script type="text/javascript">
jQuery(function($){
	$.checkAllChecked = function(fieldset) {
		var count = fieldset.find('input').length;
		var checkedCount = fieldset.find('input:checked').length;
		return count == checkedCount;
	}

	$.updateInputsChecked = function(checkboxs,bln) {
		checkboxs.each(function(){
			$.updateInputChecked($(this), bln);
		})
	}

	$.updateInputChecked = function(checkbox,bln) {
		if(bln){
			checkbox.parent().addClass("checked");
		}else{
			checkbox.parent().removeClass("checked");	
		}
		checkbox.get(0).checked = bln;
	}
	
	$("fieldset input").change(function(){
		var level = $(this).data('level');
		var parent,perv,all_status;

		if (level != 3) {
			parent = $(this).parents('.fieldset-' + level);
			$.updateInputsChecked(parent.find('input'), this.checked);
		} else {
			parent = $(this).parents('.fieldset-3');
			perv = $(this).parents('.fieldset-2');
			all_status = $.checkAllChecked(parent);
			$.updateInputChecked(perv.find('input').eq(0), all_status);
		}

		if (level > 1) {
			parent = $(this).parents('.fieldset-1');
			all_status = $.checkAllChecked(parent.find('.fieldset-2'));
			$.updateInputChecked(parent.find('input').eq(0), all_status);
		}
	});
});

</script>
@stop
