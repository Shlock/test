@extends('xwap.community._layouts.base')

@section('css')
<link rel="stylesheet" href="//g.alicdn.com/msui/sm/0.6.2/css/sm-extend.min.css">
<style>
    .list-block .item-after .z-img{width:65px !important;height: auto !important; }
</style>
@stop

@section('show_top')
    <header class="bar bar-nav">
        <a class="button button-link button-nav pull-left back" href="#" data-transition='slide-out'>
            <span class="icon iconfont">&#xe600;</span>
        </a>
        <h1 class="title f16">{{$data['name']}}</h1>
    </header>
@stop

@section('content')
    <div class="content" id=''>
        <div class="list-block z-wyjs">
            <ul>
                <li class="item-content">
                    <div class="item-inner f14">
                        <div class="item-title">公司名</div>
                        <div class="item-after c-gray">{{$data['name']}}</div>
                    </div>
                </li>
                <li class="item-content">
                    <div class="item-inner f14">
                        <div class="item-title">联系人</div>
                        <div class="item-after c-gray">{{$data['contacts']}}</div>
                    </div>
                </li>
                <li class="item-content">
                    <div class="item-inner f14">
                        <div class="item-title">物业电话</div>
                        <div class="item-after c-blue"><a href="tel:{{$data['serviceTel']}}">{{$data['serviceTel']}}</a></div>
                    </div>
                </li>
                <li class="item-content">
                    <div class="item-inner f14">
                        <div class="item-title">营业执照</div>
                        <div class="item-after z-imgh">
                            <span class="pb-standalone"><img src="{{$data['authenticate']['businessLicenceImg']}}" class="z-img"></span>
                            <!-- <i class="icon iconfont ml10 mt20 c-gray vat">&#xe602;</i> -->
                        </div>
                    </div>
                </li>
            </ul>
        </div>
    </div>
@stop

@section('js')
<script type='text/javascript' src='//g.alicdn.com/msui/sm/0.6.2/js/sm-extend.min.js' charset='utf-8'></script>
<script type="text/javascript">
    $(function(){
          /*=== 默认为 standalone ===*/
          var myPhotoBrowserStandalone = $.photoBrowser({
              photos : [
                  "{{$data['authenticate']['businessLicenceImg']}}",
              ]
          });
          //点击时打开图片浏览器
          $(document).on('click','.pb-standalone',function () {
            //移除上一次加载
            $(".photo-browser").remove();
            //创建
            myPhotoBrowserStandalone.open();
            //页面样式调整
            $(".photo-browser-close-link").addClass("pull-right").removeClass("pull-left").removeClass("icon-left").addClass("iconfont").html('&#xe604;');
          });

    })
</script>
@stop