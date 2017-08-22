@extends('staff.default._layouts.base')
@section('title'){{$title}}@stop
@section('show_top')
    <header class="bar bar-nav">
        <a class="button button-link button-nav pull-left" href="#" onclick="JumpURL('{{ u('Seller/info') }}','#seller_info_view',2)" data-transition='slide-out'>
            <span class="icon iconfont">&#xe64c;</span>
        </a>
        <a href="#" class="button button-link pull-right" id="J_save_show_data" onclick="$.saveinfo('{{u('Seller/savename')}}')">保存</a>
        <h1 class="title">{{$title}}</h1>
    </header>
@stop
@section('css')
@stop

@section('contentcss')hasbottom @stop
@section('content')
    <div class="blank050"></div>
    <div class="fwfw">
        <span>店铺名称：</span>
        <input type="text" id = "save_info_name" placeholder="输入新店铺名称"  value="" data-type="name">
    </div>
@stop