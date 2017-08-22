@extends('xwap.community._layouts.base')

@section('show_top')
<?php 
	$code = [
		'1' => '注册协议',
		'2' => '服务范围',
		'3' => '关于我们',
		'4' => '下单须知',
		'5' => '优惠券使用说明',
		'6' => '退款协议',
		'7' => '使用帮助',
		'8' => '平台洗车服务介绍',
		'9' => '查看开通小区'
	];
	$siteName = Input::get('code') ? $code[Input::get('code')] : '方维';
 ?>
	<header class="bar bar-nav">
        <a class="button button-link button-nav pull-left back" href="{{ u('User/reg') }}" data-transition='slide-out'>
            <span class="icon iconfont">&#xe600;</span>
        </a>
        <h1 class="title f16">{{ $siteName }}</h1>
    </header>
@stop

@section('content')
    <div class="content c-bgfff" id=''>
        <div class="y-about f14">
            <p>{!! $about !!}</p>
        </div>
    </div>
@stop
