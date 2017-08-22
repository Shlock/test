@extends('seller._layouts.base')
@section('css')
@stop
@section('content')
    @yizan_begin
    <yz:form id="yz_form" action="save" nobtn="1">
        <div class="pageBox page_1">
            <div class="m-zjgltbg">
                <div class="p10">
                	<p class="f-bhtt f14 clearfix" style="border-bottom: none;">
                        <span class="ml15 fl">添加菜单分类</span>
                        <a href="{{ u('GoodsType/index') }}" class="fr mr15 btn f-bluebtn" style="margin-top:8px;">返回</a>
                    </p>
                    <div class="g-szzllst pt10">
                        <yz:fitem name="name" label="分类名称"></yz:fitem>
                        <yz:fitem name="ico" label="分类图标" type="image"></yz:fitem>
                        <yz:fitem name="sort" label="排序"></yz:fitem>
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