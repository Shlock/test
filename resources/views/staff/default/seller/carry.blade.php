@extends('staff.default._layouts.base')
@section('title'){{$title}}@stop
@section('show_top')
    <header class="bar bar-nav">
        <a class="button button-link button-nav pull-left"  href="#" onclick="JumpURL('{{ u('Seller/account') }}','#seller_account_view',2)"  data-transition='slide-out'>
            <span class="icon iconfont">&#xe64c;</span>
        </a>
        <a  href="#" onclick="JumpURL('{{ u('Seller/withdrawlog') }}','#seller_withdrawlog_view',2)" class="button button-link  pull-right">提现记录</a>
        {{--externalpageloding--}}
        <h1 class="title">{{$title}}</h1>
    </header>
@stop
@section('css')
@stop
@section('contentcss')pull-to-refresh-content @stop
@section('distance')data-ptr-distance="20" @stop
@section('content')
    @include('staff.default._layouts.refresh')
    <div class="admin-shop-money-carry   lists_item_ajax">
        @include("staff.default.seller.carry_item")
    </div>
@stop
