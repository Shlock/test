@extends('admin._layouts.base')
@section('css') 
@stop
@section('right_content')
	@yizan_begin 
		<yz:form id="yz_form" url="{{u('Property/doorsave')}}"> 
			<div id="name-form-item" class="u-fitem clearfix ">
	            <span class="f-tt">
	                公司名称:
	            </span>
	            <div class="f-boxr">
	                {{$seller['name']}}
	                <input type="hidden" name="sellerId" value="{{$seller['id']}}" >
	            </div>
	        </div>
	        <div id="name-form-item" class="u-fitem clearfix ">
	            <span class="f-tt">
	                小区名称:
	            </span>
	            <div class="f-boxr">
	                {{$seller['district']['name']}}
	                <input type="hidden" name="districtId" value="{{$seller['district']['id']}}" >
	            </div>
	        </div>
			<yz:fitem name="name" label="门禁名称"></yz:fitem>  
			<yz:fitem name="pid" label="门禁ID（唯一）" ></yz:fitem> 
            <yz:fitem name="type" label="门禁类型">
                <yz:radio name="type" options="0,1" texts="楼宇大门,小区大门" checked="$data['type']" default="0"></yz:radio>
            </yz:fitem>
            <yz:fitem label="楼栋号" pstyle="display:none;" id="building">
                <yz:select name="buildId" options="$buildIds" valuefield="id" textfield="name" selected="$data['buildId']"></yz:select>
            </yz:fitem>
			<yz:fitem name="remark" label="备注"></yz:fitem> 
			<yz:fitem name="installAddress" label="安装具体位置"></yz:fitem>
			<yz:fitem name="installWork" label="安装人姓名"></yz:fitem>
			<yz:fitem name="installTelete" label="安装人电话"></yz:fitem>
		</yz:form> 
	@yizan_end
<script type="text/javascript">
    jQuery(function($){
        var type = "{{$data['type']}}";
        if (type == 1) {
            $("#building-form-item").hide();
        } else {
            $("#building-form-item").show();
        }
        $("input[name='type']").change(function() {
            type  = $(this).val();
            if (type == 0) {
                $("#building-form-item").show();
            } else {
                $("#building-form-item").hide();
            }
        });
        
    });
</script>
@stop