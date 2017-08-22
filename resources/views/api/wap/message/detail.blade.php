@extends('wap.community._layouts.base')
@section('show_top')

@stop

@section('content')
    <div role="main" class="ui-content">
        @include("api.wap.message.detail_item")
    </div>
    @include('wap.community._layouts.swiper')
@stop

@section('js')
    <script src="{{ asset('static/infinite-scroll/jquery.infinitescroll.js') }}"></script>
    <script>
        $(function() {
            $.SwiperInit('.ui-content', '.msg-list', "{{!! u('staff/v1/msg.msgshow',$args) !!}}");
            $(".msgnative").on("touchend",function(){
                var id = $(this).data("args");
                if(window.stub){
                    window.stub.callmsgnative(id);
                }else{
                    callmsgnative(id);
                }
            })
        })
    </script>

@stop