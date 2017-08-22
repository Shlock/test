@extends('seller._layouts.base')
@section('css')
    <style type="text/css">
        #cateSave{display: none;}
        .page_2,.page_3{display: none;}
        #map-search-1{padding:0px 10px;}
        .m-spboxlst .f-boxr{width: 550px;}
    </style>
@stop
@section('content')
    @yizan_begin
    <yz:form id="yz_form" action="save">
        <!-- 第一页 -->
        <div class="pageBox page_1">
            <div class="m-zjgltbg">
                <div class="p10">
                    <p class="f-bhtt f14 clearfix" style="border-bottom: none;">
                        <span class="ml15 fl">@if (Input::get('id') > 0)编辑人员@else添加人员@endif</span>
                        <a href="{{ u('Staff/index') }}" class="fr mr15 btn f-bluebtn" style="margin-top:8px;">返回</a>
                    </p>
                    <div class="g-szzllst pt10">
                        @if($data)
                            <yz:fitem name="mobile" label="手机号" attr="maxlength=11" tip="修改手机号码,如果已注册会员,请输入已设置的密码"></yz:fitem>
                            <yz:fitem name="pwd"  label="密码" type="password" tip="不修改请保留为空"></yz:fitem>
                        @else
                            <yz:fitem name="mobile" label="手机号" attr="maxlength=11"></yz:fitem>
                            <yz:fitem name="pwd"  label="密码" type="password">
                                <attrs>
                                    <btip><![CDATA[如果手机号未注册会员，必须设置密码；<br/>如果手机号已经注册过会员，必须输入已设置的密码；]]></btip>
                                </attrs>
                            </yz:fitem>
                        @endif
                        <yz:fitem name="type" type="hidden" val="$type"></yz:fitem>
                        <yz:fitem name="name" label="名称"></yz:fitem>
                        <yz:fitem name="avatar" label="头像" type="image"></yz:fitem>
                        @if($seller['type'] != 1)
                        <yz:fitem name="type" label="类型">
                            <yz:radio name="type" options="1,2,3" texts="配送人员,服务人员,配送和服务人员" checked="$data['type']" default="1"></yz:radio>
                        </yz:fitem>
                        @endif
                        <yz:fitem name="sex" label="性别">
                            <yz:radio name="sex" options="1,2" texts="男,女" checked="$data['sex']" default="1"></yz:radio>
                        </yz:fitem>
                        <yz:fitem name="provinceId" label="所在地区">
                            <yz:region pname="provinceId" pval="$data['province']['id']" cname="cityId" cval="$data['city']['id']" aname="areaId" aval="$data['area']['id']"></yz:region>
                        </yz:fitem>
                        <yz:fitem name="mapPos" label="服务范围">
                            <yz:mapArea name="mapPos" pointVal="$data['mapPoint']" addressVal="$data['address']" posVal="$data['mapPos']"></yz:mapArea>
                        </yz:fitem>
                        <yz:fitem name="authentication" label="证书号码"></yz:fitem>
                        <yz:fitem name="authenticateImg" label="证书图片" type="image"></yz:fitem>
                        <yz:fitem name="status" label="状态">
                            <yz:radio name="status" options="0,1" texts="停用,启用" checked="$data['status']" default="1"></yz:radio>
                        </yz:fitem>
                    </div>
                </div>
            </div>
        </div>
    </yz:form>
    @yizan_end
@stop
@section('js')
<script type="text/javascript">
    $(function(){

        $("#birthday").change(function(){
            $.post("{{ u('Staff/get_zodiac_sign') }}",{'time':$(this).val()},function(res){
                $("#constellation2").text(res);
                $("#constellation").val(res);
            });
        });
        
    });
</script>
@stop

