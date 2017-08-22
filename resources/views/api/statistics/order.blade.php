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
            <h1 style="margin-bottom:0px;text-align:center;">订单编号：{{ $data['sn'] }}</h1>
        </article>
        <div class="weui-cells">
            <div class="weui-cell">
                <div class="weui-cell__bd">
                    <p>会 员 名</p>
                </div>
                <div class="weui-cell__ft">{{ $data['name'] }}</div>
            </div>
            <div class="weui-cell">
                <div class="weui-cell__bd">
                    <p>配送地址</p>
                </div>
                <div class="weui-cell__ft">{{ $data['address'] }}</div>
            </div>
            <div class="weui-cell">
                <div class="weui-cell__bd">
                    <p>配送时间</p>
                </div>
                <div class="weui-cell__ft">{{ $data['appTime'] }}</div>
            </div>
            <div class="weui-cell">
                <div class="weui-cell__bd">
                    <p>支付方式</p>
                </div>
                <div class="weui-cell__ft">{{ $data['payType'] }}</div>
            </div>
        </div>
        <div class="weui-cells">
            <div class="weui-cell">
                <div class="weui-cell__bd">
                    <p>订单备注</p>
                </div>
                <div class="weui-cell__ft">{{ $data['buyRemark'] }}</div>
            </div>
            <div class="weui-cell">
                <div class="weui-cell__bd">
                    <p>配送人员</p>
                </div>
                <div class="weui-cell__ft">{{ $data['staff']['name']}}    {{ $data['staff']['mobile']}} </div>
            </div>
            <div class="weui-cell">
                <div class="weui-cell__bd">
                    <p>订单状态</p>
                </div>
                <div class="weui-cell__ft">{{ $data['payStatusStr']}}    {{ $data['orderStatusStr']}} </div>
            </div>
        </div>
        <div class="weui-cells">
            @foreach($data[orderGoods] as $goods)
            <div class="weui-cell">
                <div class="weui-cell__bd">
                    <p>{{ $goods[goodsName] }}</p>
                </div>
                <div class="weui-cell__ft">￥{{(double)round(($goods[price]),2)}} <span style="color:red;">x</span> {{ $goods[num] }}</div>
            </div>
            @endforeach
        </div>
        <div class="weui-cells">
            <div class="weui-cell">
                <div class="weui-cell__bd">
                    <p>总金额</p>
                </div>
                <div class="weui-cell__ft">￥{{$data['totalFee']}}</div>
            </div>
            <div class="weui-cell">
                <div class="weui-cell__bd">
                    <p>商品金额</p>
                </div>
                <div class="weui-cell__ft">￥{{$data['goodsFee']}}</div>
            </div>
            <div class="weui-cell">
                <div class="weui-cell__bd">
                    <p>配送费</p>
                </div>
                <div class="weui-cell__ft">￥{{$data['freight']}} </div>
            </div>
            <div class="weui-cell">
                <div class="weui-cell__bd">
                    <p>优惠金额</p>
                </div>
                <div class="weui-cell__ft">￥{{$data['discountFee']}}</div>
            </div>
            <div class="weui-cell">
                <div class="weui-cell__bd">
                    <p>支付金额</p>
                </div>
                <div class="weui-cell__ft">{{$data['payFee']}}</div>
            </div>
            <div class="weui-cell">
                <div class="weui-cell__bd">
                    <p>商家金额</p>
                </div>
                <div class="weui-cell__ft">￥{{$data['sellerFee']}}</div>
            </div>
            <div class="weui-cell">
                <div class="weui-cell__bd">
                    <p>抽成金额</p>
                </div>
                <div class="weui-cell__ft">￥{{(double)$data['drawnFee']}}</div>
            </div>
        </div>
    </body>
</html>