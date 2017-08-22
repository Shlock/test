<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>收入分类统计</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" />
        <meta name="format-detection" />
        <meta name="apple-mobile-web-app-capable" content="yes" />
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <link rel="stylesheet" href="{{ asset('staff/css/weui.css') }}">
        <script src="{{ asset('staff/js/zepto.min.js') }}"></script>
        <script src="{{ asset('staff/js/weui.min.js') }}"></script>
    </head>
    <body>
        <article class="weui-article">
            <h1>{{ $name }}</h1>
        </article>
        <div class="weui-cells__title" style="text-align:right;">商户资金分布</div>
        <div class="weui-cells">
            @foreach($data as $val)
            <a class="weui-cell weui-cell_access" href="/staff/v1/order.olist?status={{ $val[status] }}&cateId={{ $args[sellerId] }}&token={{ $args[token] }}&userId={{ $args[userId] }}&agent={{ $args[agent] }}&beginTime={{ $args[beginTime] }}&endTime={{ $args[endTime] }}">
                <div class="weui-cell__bd">
                    <p>{{ $val[name] }}</p>
                </div>
                <div class="weui-cell__ft">¥ {{number_format($val[money],2)}}</div>
            </a>
            @endforeach
        </div>
    </body>
</html>