@extends('xwap.community._layouts.base')

@section('show_top')
    <header class="bar bar-nav">
        <a class="button button-link button-nav pull-left" href="@if(!empty($nav_back_url)) {!! $nav_back_url !!} @else javascript:$.back(); @endif" data-transition='slide-out'>
            <span class="icon iconfont">&#xe600;</span>
        </a>
        <a href="{{ u('Index/cityservice') }}"><h1 class='title'>城市选择</h1></a>
    </header>
@stop

@section('content')
    <div class="content" id=''>
        <div class="p10 f14 c-black c-bgfff mb10">当前定位城市：{{$cityinfo['name']}}</div>
        <div class="p10 f14 c-black c-bgfff">已开通的城市</div>
        <div class="list-block nobor x-hislst">
            <ul class="x-hislst2">
                @foreach($city as $k=>$v)
                    <li data-name="{{$v['name']}}" data-mappoint="{{ preg_replace("/\s+/","",$v['citylocation']['lat']).",".preg_replace("/\s+/","",$v['citylocation']['lng']) }}" data-city="{{$v['name']}}">
                        <a href="javascript:;" class="item-content c-black">
                            <div class="item-inner">
                                <div class="item-title-row">
                                    <div class="item-title">{{$v['name']}}</div>
                                </div>
                            </div>
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
@stop

@section($js)
<script>
    $(".x-hislst2 li").click(function () {
        var address = $(this).attr('data-name')
        var mapPointStr = $(this).attr('data-mappoint');
        var city = $(this).attr('data-city');
        var type = {{$args['type']}};
        var data = {
            "address":address,
            "mapPointStr":mapPointStr,
            "city":city
        };
        $.post("{{ u('Index/relocation2') }}",data,function(res){
            if(res.code == 1){
                $.toast("抱歉，当前城市未开通服务，请选择其他城市吧");
            }else{
                if(type == 1){
                    $.router.load("{{u('UserCenter/addressdetail')}}", true)
                }else if(type == 2){
                    $.router.load("{{u('Index/addressmap')}}", true)
                }
            }
        },"json");
    });
</script>
@stop