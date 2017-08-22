@extends('seller._layouts.base')
@section('css')
    <style type="text/css">
        #cateSave{display: none;}
        #map-search-1{padding:0px 10px;}
        .page_2,.page_3{display: none;}
        .ts{color: #ccc;margin: 5px 0;}
        
    </style>
@stop
@section('content')
<div>
    <div class="m-zjgltbg">                 
        <div class="p10">
            <div class="g-fwgl">
                <p class="f-bhtt f14 clearfix">
                    <span class="ml15 fl">门禁添加</span>
                </p>
            </div>
            <div class="m-tab m-smfw-ser pt20">
                @yizan_begin
                    <yz:form id="yz_form" action="save"> 
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
            </div>
        </div>
    </div>
</div>
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


            