@extends('wap.community._layouts.base')

@section('css') 
<style type="text/css">

</style>
@stop
@section('js') 
    <script src="{{ asset('static/infinite-scroll/jquery.infinitescroll.js') }}"></script> 
    <script src="{{ asset('js/dot.js') }}"></script>
@stop 

@section('show_top') 
    <div data-role="header" data-tap-toggle="false" data-position="fixed" class="x-header">
        <h1>{{$data['name']}}</h1>
        <a href="{{u('Goods/detail', ['goodsId'=>$data['id']])}}" data-iconpos="notext" class="x-back ui-nodisc-icon" data-shadow="false"></a>
        <span class="x-sjr ui-btn-right"><i class="x-sjsc collect_it @if($data['iscollect']) on @endif" data-id="{{$data['id']}}"></i></span>
    </div>
@stop  

@section('content')   
    <!-- /content --> 
    <div role="main" class="ui-content">
        <div class="x-lh45 c-green" style="margin-top:-17px;"></div>
        <div class="x-pdel">
            <p>{!!$data['brief']!!}</p>
        </div>
    </div>
@stop 
