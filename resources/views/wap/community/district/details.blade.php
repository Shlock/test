@extends('wap.community._layouts.base')
@section('show_top')
    <div data-role="header" data-position="fixed" class="x-header">
        <h1>{{$data['name']}}</h1>
        <a href="@if(!empty($nav_back_url)) {{ $nav_back_url }} @else javascript:history.back(-1); @endif" data-iconpos="notext" class="x-back ui-nodisc-icon" data-shadow="false"></a>
    </div>
@stop
@section('content')
<?php $houseData = Lang::get('api.house_type');?>
    <div role="main" data-role="content">
        <div class="x-mt-1em"></div>
        <ul class="x-bgfff2 f14">
            <li class="x-joinxq">
                小区名<span class="fr c-green">{{$data['name']}}</span>
            </li>
            <li class="x-joinxq">
                户数<span class="fr c-green">{{$data['houseNum']}}户</span>
            </li>
            <li class="x-joinxq">
                占地面积<span class="fr c-green">{{$data['areaNum']}}平方米</span>
            </li>
            <li class="x-joinxq clearfix">
                <p>小区位置<span class="fr c-green">{{$data['province']['name']}}{{$data['city']['name']}}{{$data['area']['name']}}</span></p>
                <p class="fr c-green">{{$data['address']}}</p>
            </li>
            <li class="x-joinxq">
                房产类型<span class="fr c-green">{{$houseData[$data['houseType']]}}</span>
            </li>
            <li class="x-joinxq">
                物业公司<span class="fr c-green">{{$data['seller']['name']}}</span>
            </li>
        </ul>
        <div class="tc">
            <p class="f12 c-green pt15 mt20 mb10">@if($data['sellerId'] > 0)小区物业已入住平台@endif</p>
            <a class="ui-btn redbtn" href="javascript:;" id="add">加入我的小区</a>
        </div>
    </div>
@stop
@section('js')
<script type="text/javascript">
$(function() {
    $("#add").click(function () {
        var districtId = "{{$data['id']}}";
        $.post("{{u('District/save')}}",{'districtId':districtId},function(res){
            if(res.code == 0) {
                $.showSuccess(res.msg);
                window.location.href = "{{ u('District/index')}}";
            }else if(res.code == '99996'){
                window.location.href = "{{ u('User/login') }}";
            }else{
                $.showError(res.msg);
            }
        },'json');
    })

})
    
</script>

@stop