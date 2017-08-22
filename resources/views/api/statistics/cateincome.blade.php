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
        <div class="weui-cells weui-cells_form" style="margin-top:0px;">
            <div class="weui-cell">
                <div class="weui-cell__hd"><label for="" class="weui-label">起始日期</label></div>
                <div class="weui-cell__bd">
                    <input class="weui-input" id="beginTime" name="beginTime" type="date" value="{{ $args[beginTime] }}">
                </div>
            </div>
            <div class="weui-cell">
                <div class="weui-cell__hd"><label for="" class="weui-label">结束日期</label></div>
                <div class="weui-cell__bd">
                    <input class="weui-input" id="endTime" name="endTime" type="date" value="{{ $args[endTime] }}">
                </div>
            </div>
        </div>
        <input type="hidden" id="token" name="token" value="{{ $args[token] }}">
        <input type="hidden" id="agent" name="agent" value="{{ $args[agent] }}">
        <input type="hidden" id="userId" name="userId" value="{{ $args[userId] }}">
        <div class="weui-btn-area" style="margin-bottom:20px;">
            <a class="weui-btn weui-btn_primary" id="searchBtn">立刻搜索</a>
        </div>
        <div class="weui-cells__title" style="text-align:right;">商户可结算金额</div>
        <div class="weui-cells">
            @foreach($data as $val)
            <a class="weui-cell weui-cell_access" href="/staff/v1/order.seller?sellerId={{ $val->cate_id }}&token={{ $args[token] }}&userId={{ $args[userId] }}&agent={{ $args[agent] }}&beginTime={{ $args[beginTime] }}&endTime={{ $args[endTime] }}">
                <div class="weui-cell__bd">
                    <p>{{ $val->name }}</p>
                </div>
                <div class="weui-cell__ft">¥ {{number_format($val->money,2)}}</div>
            </a>
            @endforeach
        </div>
        <script type="text/javascript">
            $(function(){
                $('#searchBtn').on('click',function(){
                    window.location.href = '/staff/v1/order.cateincome?token='+$('#token').val()+'&userId='+$('#userId').val()+'&agent='+$('#agent').val()+'&beginTime='+$('#beginTime').val()+'&endTime='+$('#endTime').val();
                });
            });
        </script>
    </body>
</html>