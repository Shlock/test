@extends('staff.default._layouts.base')
@section('title'){{$title}}@stop
@section('show_top')
    <header class="bar bar-nav">
        <a class="button button-link button-nav pull-left" href="#" onclick="JumpURL('{{u('Seller/index')}}','#seller_index_view',2)" data-transition='slide-out'>
            <span class="icon iconfont">&#xe64c;</span>
        </a>
        <h1 class="title">{{$title}}</h1>
    </header>
@stop
@section('css')
@stop
@section('contentcss')pull-to-refresh-content @stop
@section('distance')data-ptr-distance="20"@stop
@section('content')
    @include('staff.default._layouts.refresh')
    <div class="lists_item_ajax">
        @include('staff.default.seller.info_item')
    </div>
@stop