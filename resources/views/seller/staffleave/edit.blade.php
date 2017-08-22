@extends('seller._layouts.base')
@section('css')
    <style type="text/css">
        #cateSave{display: none;}
        .page_2,.page_3{display: none;}
        #map-search-1{padding:0px 10px;}
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
                        @yizan_yield('name')
                        <yz:fitem name="name" label="名称"></yz:fitem>
                        @yizan_stop
                        <yz:fitem name="avatar" label="头像" type="image"></yz:fitem>
                        <yz:fitem name="sex" label="性别">
                            <yz:radio name="sex" options="1,2" texts="男,女" checked="$data['sex']" default="1"></yz:radio>
                        </yz:fitem>
                        <yz:fitem name="birthday" label="生日" type="date"></yz:fitem>
                        <yz:fitem name="constellation2" label="星座" type="text" val="{{$data['constellation']}}"></yz:fitem>
                        <yz:fitem name="constellation" type="hidden"></yz:fitem>
                        <yz:fitem name="recruitment" label="籍贯"></yz:fitem>
                        <yz:fitem name="provinceId" label="所在地区">
                            <yz:region pname="provinceId" pval="$data['provinceId']" cname="cityId" cval="$data['cityId']" aname="areaId" aval="$data['areaId']"></yz:region>
                        </yz:fitem>
                        <yz:fitem name="mapPos" label="服务范围">
                            <yz:mapArea name="mapPos" pointVal="$data['mapPoint']" addressVal="$data['address']" posVal="$data['mapPos']"></yz:mapArea>
                        </yz:fitem>
                        <yz:fitem name="businessDistrict" label="小区" type="textarea"></yz:fitem>
                        <yz:fitem name="brief" label="个人简介" type="textarea"></yz:fitem>
                        <yz:fitem name="hobbies" label="个人爱好" type="textarea"></yz:fitem>
                        <yz:fitem name="photos" label="个人相册" type="image">
                            <yz:imageList name="photos." images="$data['photos']"></yz:imageList>
                        </yz:fitem>
                        <yz:fitem name="authentication" label="认证信息"></yz:fitem>
                        <yz:fitem name="sort" label="排序"></yz:fitem>
                        @if($data != "")
                        <yz:fitem name="sort" label="预约时间">
                            <dd class="clearfix" style="padding:0px 15px 15px 15px;">
                                @include('seller.staff.showtime')
                            </dd>
                        </yz:fitem>
                        @endif
                        <p class="tc pb20">
                            <input type="submit" class="btn f-170btn ml20" style="width:120px;" value="提交">
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </yz:form>
    <!-- 分类存储 -->
    <div id="cateSave">
        <ul>
            @foreach( $cate as $key => $value )
                <li class="cateId_{{$key}}">{{$value['levelrel']}}</li>
            @endforeach
        </ul>
    </div>
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

