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
		<div>
		<p style="margin:20px 20px 20px 97px;">说明：过滤的关键字用<font color="red">英文逗号</font>“,”分隔提交。过滤关键字在正文中会用“*”代替。</p>
		</div>
		@foreach($list as $key => $value)
			<php>
				$code = $value['code']; 
				$data[$code] = $value['val'];
				$showType = $value['showType']; 
				$val = trim($value['val']); 
				$name = $value['name'];
			</php>
			@if( $value['showType'] == 'image' )
				<!-- @if( $code === 'admin_logo' ) -->
					<yz:fitem name="admin_logo" label="{{$name}}" val="{{$val}}" type="image" ></yz:fitem>
				<!-- @else -->
					<yz:fitem name="{!! $code !!}" label="{{$name}}" val="{{$val}}" type="image" ></yz:fitem>
				<!-- @endif -->
			@elseif( $value['showType'] == 'textarea' )
			<yz:fitem name="{!! $code !!}" label="{{$name}}" val="{{$val}}" type="textarea"></yz:fitem>
			@elseif( $showType == 'editor' ) 
			<yz:fitem name="{!! $code !!}" label="{{ $name }}">
				<yz:editor name="{!! $code !!}" value="{{$val}}"></yz:editor>
			</yz:fitem>
            @elseif( $showType == 'select' && $code == 'wap_tpl')
            <yz:fitem label="{{$name}}">
                <yz:select name="wap_tpl" options="$wapTpls" textfield="text" valuefield="val" attr="style='min-width:234px;width:auto'" selected="$val">
                </yz:select>
            </yz:fitem>
			@else 
			<yz:fitem name="{!! $code !!}" label="{{$name}}" val="{{$val}}" append="1">
				<span class="ts">{{$value['tooltip']}}</span>
			</yz:fitem>
			@endif
		@endforeach
		</yz:form>
	@yizan_end
@stop

@section('js')

@stop
