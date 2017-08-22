@extends('staff.default._layouts.base')
@section('title'){{$title}}@stop
@section('show_top')
    <header class="bar bar-nav">
        <a class="button button-link button-nav pull-left" href="#" onclick="JumpURL('{{u('Seller/carry')}}','#seller_carry_view',2)" data-transition='slide-out'>
            <span class="icon iconfont">&#xe64c;</span>
        </a>
        <h1 class="title">{{$title}}</h1>
    </header>
@stop
@section('css')
@stop

@section('contentcss')infinite-scroll infinite-scroll-bottom pull-to-refresh-content @stop
@section('distance')data-ptr-distance="20" @stop
@section('content')
    @include('staff.default._layouts.refresh')
    @if($account)
        <div class="withdrawal_list">
            <ul class="pl-085 fine-bor list-container-show lists_item_ajax">
                @include('staff.default.seller.withdrawlog_item')
            </ul>
        </div>
    @else
        <div class="x-null tc"  style="top:40%">
            <i class="icon iconfont">&#xe60c;</i>
            <p>很抱歉，提现记录</p>
        </div>
    @endif
@stop