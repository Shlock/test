@extends('seller._layouts.base')
@section('css')
<style type="text/css">
    .loginId,span.password{margin-left: 10px;color:red;}
    .licenseImgTs{color: red;}
    #beginTime,#endTime{width: 96px;}
</style>
@stop
@section('content')
    @yizan_begin
    <yz:form id="yz_form" action="save">
        <div class="pageBox page_1">
            <div class="m-zjgltbg">
                <div class="p10">
                    <p class="f-bhtt f14 clearfix" style="border-bottom: none;">
                        <span class="ml15 fl">重新编辑餐厅详细</span>
                        <a href="{{ u('RestaurantAudit/index') }}" class="fr mr15 btn f-bluebtn" style="margin-top:8px;">返回</a>
                    </p>
                    <div class="g-szzllst pt10">
                        <yz:fitem name="name" label="餐厅名称"></yz:fitem>
                        <yz:fitem name="contacts" label="负责人"></yz:fitem>
                        <yz:fitem name="tel" label="联系电话1" append="1">
                            <span class="loginId">登录账号</span>
                        </yz:fitem>
                        <yz:fitem name="mobile" label="联系电话2"></yz:fitem>
                        <yz:fitem label="营业时间" type="text">
                            <input type="text" name="beginTime" id="beginTime" class="u-ipttext" value="{{$data['beginTime']}}">
                            -
                            <input type="text" name="endTime" id="endTime" class="u-ipttext" value="{{$data['endTime']}}">
                        </yz:fitem>
                        <yz:fitem name="licenseImg" label="营业执照" type="image" append="1">
                        <p class="licenseImgTs">
                            推荐分辨率640*200，最大允许上传2MB，必须上传营业执照、卫生许可证，可选择是否上传身份证
                        </p>
                        </yz:fitem>
                        <yz:fitem name="license" label="营业执照号"></yz:fitem>
                        <yz:fitem name="expired" label="执照有效期" type="date"></yz:fitem>
                        <yz:fitem name="address" label="常驻地址"></yz:fitem>
                        <yz:fitem name="source" label="来源" type="text">
                            @if($data['source'] == 0)
                                web端加盟
                            @elseif($data['source'] == 1)
                                服务站添加
                            @else
                                未知
                            @endif
                        </yz:fitem>
                        <yz:fitem name="$data.seller.name" label="所属服务站" type="text"></yz:fitem>
                        <yz:fitem name="disposeStatus" label="审核状态" type="text">
                            @if($data['disposeStatus'] == -1)
                                未通过
                            @elseif($data['disposeStatus'] == 0)
                                等待审核
                            @elseif($data['disposeStatus'] == 1)
                                通过
                            @endif
                        </yz:fitem>
                        <yz:fitem name="disposeResult" label="审核结果" type="text"></yz:fitem>
                    </div>
                </div>
            </div>
        </div>
    </yz:form>
    @yizan_end
@stop

