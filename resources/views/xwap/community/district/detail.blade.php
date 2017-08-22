@extends('xwap.community._layouts.base')

@section('show_top')
    <header class="bar bar-nav">
        <a class="button button-link button-nav pull-left pageloading back" href="#" data-transition='slide-out'>
            <span class="icon iconfont">&#xe600;</span>
        </a>
        <h1 class="title f16">{{$data['name']}}</h1>
        @if($data['isUser'] == 1)
            <a class="button button-link button-nav pull-right open-popup del" data-popup=".popup-about"><i class="icon iconfont c-gray x-searchico">&#xe630;</i></a>
        @endif
    </header>
@stop

@section('content')
<!-- new -->
    <?php $houseData = Lang::get('api.house_type');?>
    <div class="content" id=''>
        <!-- 小区详细 -->
        <div class="list-block x-splotlst nobor f14 pb10">
            <ul>
                <li class="item-content">
                    <div class="item-inner">
                        <div class="item-title">小区名</div>
                        <div class="item-after c-gray">{{$data['name']}}</div>
                    </div>
                </li>
                <li class="item-content">
                    <div class="item-inner">
                        <div class="item-title">户数</div>
                        <div class="item-after c-gray">{{$data['houseNum']}}户</div>
                    </div>
                </li>
                <li class="item-content">
                    <div class="item-inner">
                        <div class="item-title">占地面积</div>
                        <div class="item-after c-gray">{{$data['areaNum']}}平方米</div>
                    </div>
                </li>
                <li class="item-content">
                    <div class="item-inner">
                        <div class="item-title">小区位置</div>
                        <div class="item-after c-gray ha tr">{{$data['province']['name']}}{{$data['city']['name']}}{{$data['area']['name']}}<br/>{{$data['address']}}</div>
                    </div>
                </li>
                <li class="item-content">
                    <div class="item-inner">
                        <div class="item-title">房产类型</div>
                        <div class="item-after c-gray">{{$houseData[$data['houseType']]}}</div>
                    </div>
                </li>
                <li class="item-content">
                    <div class="item-inner">
                        <div class="item-title">物业公司</div>
                        <div class="item-after c-gray">{{$data['seller']['name']}}</div>
                    </div>
                </li>
            </ul>
        </div>
        <p class="mt20 f12 c-gray tc mb10">@if($data['sellerId'] > 0)小区物业已入住平台@endif</p>
         @if($data['isUser'] == 1)
            @if($data['sellerId'] > 0)
            <p class="tc"><a class="x-btn pageloading" href="{{ u('Property/index', ['districtId'=>$data['id']])}}">物业</a></p>
            @endif
        @else
            <p class="tc"><a href="#" class="x-btn" id="add">加入我的小区</a></p>
        @endif
    </div>
@stop

@section($js)
<script type="text/javascript">
$(function() {
    var districtId = "{{$data['id']}}";
    $(document).on("touchend",".del",function(){
        $.confirm('删除此小区会影响物业功能。确定删除？', '操作提示', function(){
            $.delDistrict(districtId);
        });
    })
   
    $.delDistrict = function (districtId) {
        $.post("{{u('District/delete')}}",{'districtId':districtId},function(result){
            if(result.code == 0){
                $.router.load("{{ u('District/index')}}", true);
            } else {
                $.alert(result.msg);
            }
        },'json');
    }

    $(document).on("touchend", "#add", function(){
        $.post("{{u('District/save')}}",{'districtId':districtId},function(res){
            if(res.code == 0) {
                $.alert(res.msg, function(){
                    $.router.load("{{ u('District/index')}}", true);
                });
            }else if(res.code == '99996'){
                $.router.load("{{ u('User/login') }}", true);
            }else{
                $.alert(res.msg);
            }
        },'json');
    })

})
    
</script>

@stop