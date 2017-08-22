@extends('wap.community._layouts.base')

@section('show_top')
    <div data-role="header" data-position="fixed" class="x-header">
        <h1>生活圈</h1>
    </div>
@stop

@section('content')
    <div role="main" class="ui-content">
        <ul class="x-life1">
            <li>
                <a href="{{ u('Forum/mylists') }}" class="db left">
                    <img src="{{  asset('wap/community/client/images/tz.png') }}" />
                    <p>我的帖子<span>({{$postsnum}})</span></p>
                </a>
            </li>
            <li>
                <a href="{{ u('Forummsg/index') }}" class="db">
                    <img src="{{  asset('wap/community/client/images/lt.png') }}" />
                    <p>论坛消息@if($messagenum > 0)<span>({{$messagenum}})</span>@endif</p>
                </a>
            </li>
        </ul>
        @if($plates)
        <ul class="x-life2 x-index1 clearfix">
            @foreach($plates as $v)
            @if($v['id'] != 0)
            <li><a href="@if($v['id'] == 1){{u('Property/index',['id'=>$v['id']])}} @else{{u('Forum/lists',['plateId'=>$v['id']])}}@endif"><img src="@if(!empty($v['icon'])) {{formatImage($v['icon'],36,36)}} @else {{ asset('wap/community/client/images/b12.png')}} @endif"><p>{{$v['name']}}</p></a></li>
            @else
            <li><a href="{{ u('Forum/plates') }}"><img src="{{ asset('wap/community/client/images/b11.png')}}"><p>更多</p></a></li>
            @endif
            @endforeach
        </ul>
        @endif
        <ul class="x-lifelst">
            @if($lists)
            @foreach($lists as $item)
            <li>
                <a href="{{ u('Forum/detail',['id'=>$item['id']])}}"><p class="f16 mb10">{{$item['title']}}</p></a>
                <p class="f12 c-green">
                    <a href="{{u('Forum/lists',['plateId'=>$item['plate']['id']])}}" class="lf1">{{$item['plate']['name']}}</a>
                    <a href="#" class="lf2 ml20">{{$item['rateNum']}}</a>
                    <span class="lf3">{{yzday($item['createTime'])}}</span>
                </p>
            </li>
            @endforeach
            @endif
        </ul>
    </div>
    @include('wap.community._layouts.swiper')
    @include('wap.community._layouts.bottom')
@stop
@section('js')
    <script src="{{ asset('static/infinite-scroll/jquery.infinitescroll.js') }}"></script> 
    <script type="text/javascript">
        $(function() {
            $.SwiperInit('#lists','.x-post',"{{ u('Forum/index',$args) }}");


        });
    </script>
@stop