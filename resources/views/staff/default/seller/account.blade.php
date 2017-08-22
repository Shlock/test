@extends('staff.default._layouts.base')
@section('title'){{$title}}@stop
@section('show_top')
    <header class="bar bar-nav">
        <a class="button button-link button-nav pull-left" href="#" onclick="JumpURL('{{ u('Seller/index') }}','#seller_index_view',2)" data-transition='slide-out'>
            <span class="icon iconfont">&#xe64c;</span>
        </a>
        <h1 class="title">{{$title}}</h1>
    </header>
@stop
@section('css')
@stop

@section('contentcss')infinite-scroll infinite-scroll-bottom pull-to-refresh-content @stop
@section('distance')data-distance="20"  data-ptr-distance="20"@stop
@section('content')
    @include('staff.default._layouts.refresh')
    <div class="admin-shop-account">
        <div class="account_hd">
            <div class="account_hd_top content-padded">
                <div class="label">账户余额（元）</div>
                <div class="num">¥{{$balance or 0}}</div>
            </div>
            <div class="account_hd_bottom content-padded">
                <div class="row">
                    <div class="col-50">
                        <a href="#" onclick="JumpURL('{{u('Seller/carry',['type'=>3,'status'=> 3] )}}','#seller_acarry_view',2)" class="button button-fill button-success">我要提现</a>
                    </div>
                    <div class="col-50">
                        <a href="#" onclick="JumpURL('{{u('Seller/recharge')}}','#seller_acarry_view',2)" class="button button-fill button-success-blue">充值</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="account_bd">
            <div class="buttons-tab">
                <a href="#" onclick="JumpURL('{{u('Seller/account',['type'=>1,'status'=> 0] )}}','#seller_account_view_0',2)" class="tab @if($acut['status'] == 0)active @endif button" data-type="1">全部</a>
                <a href="#" onclick="JumpURL('{{u('Seller/account',['type'=>1,'status'=> 1] )}}','#seller_account_view_1',2)" class="tab @if($acut['status'] == 1)active @endif button">收入</a>
                <a href="#" onclick="JumpURL('{{u('Seller/account',['type'=>2,'status'=> 2] )}}','#seller_account_view_2',2)" class="tab @if($acut['status'] == 2)active @endif button" data-type="2">提现</a>
                <a href="#" onclick="JumpURL('{{u('Seller/account',['type'=>1,'status'=> 3] )}}','#seller_account_view_3',2)" class="tab @if($acut['status'] == 3)active @endif button" data-type="3">充值</a>
            </div>
            @if($account)
                <div class="tabs">
                    <div id="tab1" class="tab active">
                        <div class="list-block">
                            <ul class="list-container-account  lists_item_ajax">
                                @include("staff.default.seller.account_item")
                            </ul>
                        </div>
                    </div>
                </div>
            @else
                <div class="x-null tc"  style="top:70%">
                    <i class="icon iconfont">&#xe60c;</i>
                    <p>很抱歉，暂无@if($acut['status'] == 0)账单@elseif($acut['status'] == 1)收入@elseif($acut['status'] == 2)提现@else($acut['status'] == 3)充值@endif记录</p>
                </div>
            @endif
        </div>
    </div>
@stop