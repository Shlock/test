@extends('wap.community._layouts.base')

@section('show_top')
    <div data-role="header" data-position="fixed" class="x-header">
        <h1>故障报修</h1>
        <a href="@if(!empty($nav_back_url)) {!! $nav_back_url !!} @else {{u('Property/index',['districtId'=>$args['districtId']])}} @endif" data-iconpos="notext" class="x-back ui-nodisc-icon" data-shadow="false"></a>
    </div>
@stop
@section('js') 
    <script src="{{ asset('static/infinite-scroll/jquery.infinitescroll.js') }}"></script> 
@stop 
@section('content')
    <div role="main" data-role="content" class="lists">
        <div class="x-mt-1em"></div>
        @if(!empty($list))
        <ul class="x-bgfff2">
            @include('wap.community.repair.item')
        </ul>
        @else
        <div class="x-serno c-green">
            <span>暂时没有报修记录</span>
        </div>
        @endif
        <div class="x-bxbtn">
            <p><a href="{{ u('Repair/repair', ['districtId'=>$args['districtId']])}}" class="ui-btn x-btnbr">我要报修</a></p>
        </div>
    </div>
@include('wap.community._layouts.swiper')
@stop
@section('js')
<script type="text/javascript">
    $(function() {
        $.SwiperInit('.lists','.x-bgfff2',"{{ u('Repair/index',$args) }}");
    });
</script>
@stop