@extends('admin._layouts.base')
@section('css')
<style type="text/css">
	.m-spboxlst .f-tt{width: 165px;}
	.ts{color: #999;margin-left: 5px;}
</style>
@stop
@section('right_content')
	@yizan_begin
	<yz:form id="yz_form" action="save">
		@foreach($list as $key => $value)
			<php>
				$code = $value['code']; 
				$data[$code] = $value['val'];
				$val = trim($value['val']); 
				$name = $value['name'];
				$default_val = explode(',', $value['defaultVals']);
				$default_names = explode(',', $value['defaultNames']);
			</php>
			<!-- @if( $value['showType'] == 'image' ) -->
			<yz:fitem name="{!! $code !!}" label="{{$name}}" type="image"></yz:fitem>
			<!-- @elseif( $value['showType'] == 'textarea' ) -->
			<yz:fitem name="{!! $code !!}" label="{{$name}}" val="{{$val}}" type="textarea"></yz:fitem>
			<!-- @elseif( $value['showType'] == 'select' ) -->
			<yz:fitem label="{{$name}}">
				<yz:select name="{!! $code !!}" options="$default_val" texts="$default_names" selected="$val"></yz:select>
			</yz:fitem>
			<!-- @else -->
			<yz:fitem name="{!! $code !!}" label="{{$name}}" val="{{$val}}" append="1">
				<span class="ts">{{$value['tooltip']}}</span>
			</yz:fitem>
			<!-- @endif -->
		@endforeach
		</yz:form>
	@yizan_end
@stop
@section('js')
<script type="text/javascript">
	$(function(){
		var staff_deduct_type = $("#staff_deduct_type").val();
		if (staff_deduct_type == 2) {
			$("#staff_deduct_value").next().text('%');
		} else {
			$("#staff_deduct_value").next().text('元');
		}

		$('#staff_deduct_type').change(function(){
			var staff_deduct_type = $(this).val();
			if (staff_deduct_type == 2) {
				$("#staff_deduct_value").next().text('%');
			} else {
				$("#staff_deduct_value").next().text('元');
			}
		});
	})
</script>
@stop
