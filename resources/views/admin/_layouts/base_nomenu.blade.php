<!DOCTYPE html>
<!--[if IE 6]><html lang="zh-CN" class="ie6 ie9- ie8-"><![endif]-->
<!--[if IE 7]><html lang="zh-CN" class="ie7 ie9- ie8-"><![endif]-->
<!--[if IE 8]><html lang="zh-CN" class="ie8 ie9-"><![endif]-->
<!--[if IE 9]><html lang="zh-CN" class="ie9"><![endif]-->
<!--[if (gt IE 8)|!(IE)]><!-->
<html lang="zh-CN">
<!--<![endif]-->
<head>
	<meta charset="UTF-8">
	<title>{{ $site_config['site_name'] }}系统管理平台</title>
	<link rel="stylesheet" type="text/css" href="{{ asset('css/font-awesome.min.css') }}">
	<!--[if IE 7]><link rel="stylesheet" type="text/css" href="{{ asset('css/font-awesome-ie7.css') }}"><![endif]-->
	<link rel="stylesheet" type="text/css" href="{{ asset('static/kindeditor/themes/default/default.css') }}">
	<link rel="stylesheet" type="text/css" href="{{ asset('css/hover.css') }}">
	<link rel="stylesheet" type="text/css" href="{{ asset('css/base.css') }}">
	<link rel="stylesheet" type="text/css" href="{{ asset('css/yzht.css') }}">
	<link rel="stylesheet" type="text/css" href="{{ asset('static/jqueryui/ui.css') }}">
	<link rel="stylesheet" type="text/css" href="{{ asset('css/zydialog.css') }}">
	<link rel="stylesheet" type="text/css" href="{{ asset('static/qtip/jquery.qtip.min.css') }}">
	<link rel="stylesheet" type="text/css" href="{{ asset('static/uniform/css/uniform.default.css') }}">
	<link rel="stylesheet" type="text/css" href="{{ asset('static/jqueryui/datepicker.css') }}">

	<script src="{{ asset('js/jquery.1.8.2.js') }}"></script>
	<script src="{{ asset('js/yz.js') }}"></script>
	<script src="{{ asset('js/htbase.js') }}"></script>
	<script src="{{ asset('js/zydialog.js') }}"></script>
	<script src="{{ asset('js/jquery.bgiframe.js') }}"></script>
	<script src="{{ asset('js/jquery.validate.js') }}"></script>
	<script src="{{ asset('js/json.js') }}"></script>
	<script src="{{ asset('js/datalist.js') }}"></script>
	<script src="{{ asset('js/dot.js') }}"></script>
	<script src="{{ asset('static/jqueryui/ui.js') }}"></script>
	<script src="{{ asset('static/jqueryui/datepicker.js') }}"></script>
	<script src="{{ asset('static/uniform/jquery.uniform.min.js') }}"></script>
	<script src="{{ asset('static/qtip/jquery.qtip.min.js') }}"></script>
	<script src="{{ asset('static/kindeditor/kindeditor-min.js') }}"></script>
	<script src="{{ asset('static/kindeditor/lang/zh_CN.js') }}"></script>
	@yield('css')
</head>
<?php
function getAuthUrl($type, $key, $data, $role_auths) {
	$ca = strtolower($data['url']);
	if (!isset($role_auths['actions'][$ca])) {
		if ($type == 'nav') {
			$data['url'] = $role_auths['navs'][$key]['url'];
		} else {
			$data['url'] = $role_auths['controllers'][$key]['url'];
		}
	}
	return u($data['url']);
}
?>
<body>
	<div class="all">
		<!-- 状态栏 -->
		<div class="g-state">
			<div class="w1000 ma clearfix">
				<span class="fl">{{ $site_config['site_name'] }}系统管理平台</span>
				<ul class="fr clearfix m-stnav">
					<li>
						<span class="f-tt">{{ $login_admin['name'] }} <i class="ml5 fa fa-chevron-down"></i></span>
						<div class="m-ztbox none">
							<i class="dwsj"></i>
							<a href="{{ u('AdminUser/repwd',['id'=>$login_admin['id']]) }}">修改密码</a>
							<a href="{{ u('Public/logout') }}" style="color:#b40001;border-bottom:none;"><i class="fa fa-share-square-o"></i>退出</a>
						</div>
					</li>					
				</ul>
			</div>
		</div>
		<!-- 导航栏 -->
		<div class="g-nav">
			<div class="w1000 ma clearfix">
				<a href="{{ u('Index/index') }}" class="fl m-logo"><img src="{{ $site_config['admin_logo'] }}" height="58" alt=""></a>
				<ul class="u-nav fl">
					@foreach ($admin_auth as $top_nav_key => $top_nav)
					@if(isset($role_auths['navs'][$top_nav_key]))
					<li @if (isset($top_nav['selected']))class="on"@endif><a href="{{ getAuthUrl('nav', $top_nav_key, $top_nav, $role_auths) }}"><i class="fa fa-{{ $top_nav['icon'] }}"></i>{{ $top_nav['name'] }}</a></li>
					@endif
					@endforeach
				</ul>
			</div>
		</div>
		<!-- body -->
		<div class="g-bd mt15">
			<div class="w1000 ma clearfix">
				<div class=" m-qpct">
					<div class="u-rtt">
						<i class="fa fa-home mr15 ml15"></i>您的位置：您的位置：
						@foreach ($controller_navs as $controller_nav)
						<a href="{{ u($controller_nav['url']) }}">{{ $controller_nav['name'] }}</a>
						<i class="fa fa-chevron-right mr5 ml5"></i>
						@endforeach
						{{ $controller_action['name'] }}
					</div>
					<p>
						@yield('return_link')
					</p>
					@yield('right_content')
				</div>
			</div>
		</div>
	</div>
</body>

<script src="{{ asset('js/yz.js') }}"></script>
<script src="{{ asset('static/kindeditor/kindeditor-min.js') }}"></script>
<script src="{{ asset('static/kindeditor/lang/zh_CN.js') }}"></script>
@yield('js')
</html>