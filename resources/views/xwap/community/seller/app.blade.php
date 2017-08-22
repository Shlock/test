<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>@section('title'){{$site_config['site_title']}}</title>    
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" />
    <meta name="format-detection" />
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">      
    <style type="text/css">
    /* 微信引导页 */
    .x-webpage{width: 100%; height: 100%; background: url({{ asset('wap/community/client/images/webbg.jpg')}}) no-repeat; background-size: 100% 100%; position: fixed; top: 0; left: 0;}
    .x-webpage img{width: 100%;}
    </style>
</head>
<body>
    <div class="x-webpage">
        <img src="{{ asset('wap/community/client/images/webyd.png')}}">
    </div>
</body>