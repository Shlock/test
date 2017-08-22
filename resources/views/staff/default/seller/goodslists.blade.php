@extends('staff.default._layouts.base')
@section('title'){{$title}}@stop
@section('show_top')
    <header class="bar bar-nav">
        <a class="button button-link button-nav pull-left "href="#" onclick="JumpURL('{{u('Seller/index')}}','#seller_index_view',2)" data-transition='slide-out'>
            <span class="icon iconfont">&#xe64c;</span>
        </a>
        <a href="#" onclick="JumpURL('{{u('Seller/goodsadd',['type' => 1])}}','#seller_goodsadd_view',2)" class="button button-link button-nav f_r" data-popup=".popup-about">
            添加分类
        </a>
        <h1 class="title">商品管理</h1>
    </header>
@stop
@section('css')
@stop
@section('contentcss')infinite-scroll infinite-scroll-bottom @stop
@section('distance')data-distance="20" @stop

@section('content')
    <div class="management-editor plr085 clearfix">
        <span class="f_l">商品分类列表</span>
        <span href="#" class=" f_r focus-color-f" id="goods_editor-but">编辑</span>
    </div>
    @if($goods)
    <ul class="management-ul fine-bor-top list-container">
        @include('staff.default.seller.goods_item')
    </ul>
    @else
        <div class="x-null tc">
            <i class="icon iconfont">&#xe60c;</i>
            <p>很抱歉，暂无商品分类</p>
        </div>
    @endif
@stop
