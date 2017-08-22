@extends('wap.community._layouts.base')
@section('show_top')
@stop
@section('content')
    <div role="main" class="" style="background:#fff;">
        <ul class="y-xtxx y-xtxxnew">
            @if(!empty($list))
                @include('api.wap.message.index_item')
            @else
                <div class="x-serno c-green">
                    <img src="{{  asset('wap/community/client/images/ico/cry.png') }}"  />
                    <span>暂时没有消息</span>
                </div>
            @endif
        </ul>

    </div>
@include('wap.community._layouts.swiper')
@stop
@section('js')
    <script src="{{ asset('static/infinite-scroll/jquery.infinitescroll.js') }}"></script>
    <script>
        $(function() {
            $.SwiperInit('.y-xtxxnew', 'li', "{{!! u('buyer/v1/msg.message',$args) !!}}");
        })
    </script>

@stop