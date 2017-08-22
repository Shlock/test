<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>@section('title'){{$site_config['site_title']}}</title>
    <meta name="viewport" content="initial-scale=1, maximum-scale=1">
    <!-- <link rel="shortcut icon" href="/favicon.ico"> -->
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <meta name="format-detection" content="telephone=no, email=no">

    <link rel="stylesheet" href="{{ asset('wap/community/newclient/suimobile/sm.min.css') }}">
    <link rel="stylesheet" href="{{ asset('wap/community/newclient/suimobile/sm-extend.min.css') }}">
    <link rel="stylesheet" href="{{ asset('wap/community/newclient/css/base.css') }}">
    <link rel="stylesheet" href="{{ asset('wap/community/newclient/iconfont/iconfont.css') }}">
    <link rel="stylesheet" href="{{ asset('wap/community/newclient/css/color.css') }}">
    <link rel="stylesheet" href="{{ asset('wap/community/newclient/css/style.css') }}">
    @yield('css')

</head>
<body>
    <div class="page-group">
      <div class="page page-current">
            <!-- 顶部 -->
            @section('show_top')
                <header class="bar bar-nav">
                    <a class="button button-link button-nav pull-left" href="@if(!empty($nav_back_url)) {{ $nav_back_url }} @else javascript:$.back(); @endif" data-transition='slide-out'>
                        <span class="icon iconfont">&#xe600;</span>
                    </a>
                    <a class="button button-link button-nav pull-right open-popup" data-popup=".popup-about"></a>
                    <h1 class="title f16">o2o社区{{$title}}</h1>
                </header>
            @show

            <!-- 底部 -->
            @yield('footer')

            <!-- 中间 -->
            @yield('content')

            <!-- Ajax Js -->
            @yield('ajax')
      </div>
    </div> 

    <!-- SUI基础js -->
    <script src="{{ asset('wap/community/newclient/suimobile/zepto.min.js') }}" charset='utf-8'></script>
    <script src="{{ asset('wap/community/newclient/suimobile/sm.min.js') }}" charset='utf-8'></script>
    <script src="{{ asset('wap/community/newclient/suimobile/sm-extend.min.js') }}" charset='utf-8'></script>
    <!-- 公用js -->
    <script src="{{ asset('wap/community/newbase.js') }}" charset='utf-8'></script>

    <!-- 页面js -->
    @yield('js')

    <script type="text/javascript">
        //订单操作处理URL
        var conOrder_url = "{{ u('Order/confirmorder') }}";
        var delOrder_url = "{{ u('Order/delorder') }}";
        var canOrder_url = "{{ u('Order/cancelorder') }}";

        $("input").keyup(function(){
            this.value=this.value.replace(/\ud83c[\udf00-\udfff]|\ud83d[\udc00-\ude4f]|\ud83d[\ude80-\udeff]/g,'');
        });
    </script>
</body>
</html>


