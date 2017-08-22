@extends('wap.community._layouts.base')

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
	$sharedByUserId =Input::get('sharedByUserId');
	if(!empty($sharedByUserId)){
		$url = 'regshare?sharedByUserId='.$sharedByUserId;
	}else{
		$url = 'reg';
	}
 ?>
	<div data-role="header" data-position="fixed" class="x-header">
		<h1>{{ $siteName }}</h1>
        <a href="/User/<?php echo $url;?>" data-iconpos="notext" class="x-back ui-nodisc-icon" data-shadow="false"></a>
	</div>
@stop

@section('content')
	<div class="y-gywm">
    	<div>
    		<p class="f14">{!! $about !!}</p>
        </div>
    </div>
@stop
