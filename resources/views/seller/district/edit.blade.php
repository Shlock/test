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
    @yizan_begin
    <yz:form id="yz_form" action="save" nobtn="1">
        <!-- 第一页 -->
        <div class="pageBox page_1">
            <div class="m-zjgltbg">
                <div class="p10">
                    <p class="f-bhtt f14 clearfix" style="border-bottom: none;">
                        <span class="ml15 fl">@if (Input::get('id') > 0)编辑小区@else添加小区@endif</span>
                        <a href="{{ u('district/index') }}" class="fr mr15 btn f-bluebtn" style="margin-top:8px;">返回</a>
                    </p>
                    <div class="g-szzllst pt10"> 
                        <yz:fitem name="provinceId" label="所在地区">
                            <yz:region pname="provinceId" pval="$data['provinceId']" cname="cityId" cval="$data['cityId']" aname="areaId" aval="$data['areaId']"></yz:region>
                        </yz:fitem>
                        <yz:fitem name="name" label="小区名称"></yz:fitem>
                        <yz:fitem name="mapPos" label="小区位置">
                            <yz:mapArea name="mapPos" pointVal="$data['mapPointStr']" addressVal="$data['address']" posVal="$data['mapPosStr']"></yz:mapArea>
                            <span class="ts">小区范围不做强制要求</span>
                        </yz:fitem> 
                        <p class="tc pb20">
                            <input type="submit" class="btn f-170btn ml20" style="width:120px;" value="提交">
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </yz:form>
    @yizan_end
@stop

