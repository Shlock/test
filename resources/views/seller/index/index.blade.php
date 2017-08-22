@extends('seller._layouts.base')
@section('css')
<style type="text/css">
    .fa-mobile{font-size: 25px;}
</style>
@stop
@section('content')
    <div class="m-ydtt">
        <p class="clearfix f-tt">
            <span class="fl f14"><span class="f24" style="color:#000;">{{ $sellerInfo['name'] or '服务人员' }}</span>， 欢迎您！ </span>
            <span class="fr">上一次登录时间： {{ isset($sellerInfo['loginTime']) ? yztime($sellerInfo['loginTime']) : yztime(UTC_TIME) }}</span>
        </p>
        <div class="u-ydct clearfix f14">
            <div class="f18"><p><i class="fa fa-mobile mr5"></i>手机号码 <span>{{ $sellerInfo['mobile'] or '无'}}</span></p></div>
            <div class="clearfix">
                <span class="fl ml10">账户余额 <span class="f24" style="color:#b40001;">{{ $useraccount or 0}}</span> 元</span>
                <a href="{{ u('Funds/index') }}" class="fr btn f-back" style="line-height:24px;">查看</a>
            </div>
        </div>
    </div>
    <div class="m-taotalct mt20">
        <div class="clearfix">
            <div class="u-ddgk fl">
                <p class="lh55 clearfix">
                    <span class="fl f16 ml15">商品概况</span>
                    <span class="fl f16"  style="margin-left: 60px">服务概况</span>
                    <a href="{{ u('Order/index') }}" class="fr btn f-80btn mr15" style="margin-top:13px;">查看详情</a>
                </p>
                <ul class="m-ddlst fl">
                    <li>
                        待发货订单<span>（<a href="{{ u('Order/index',['status'=>'1','nav'=>'nav2']) }}">{{$ordercount['unfinished'][1]}}</a>）</span>
                    </li>
                    <li>
                        待完成订单<span>（<a href="{{ u('Order/index',['status'=>'2','nav'=>'nav3']) }}">{{$ordercount['unpay'][1]}}</a>）</span>
                    </li>
                    <li>
                        已完成订单<span>（<a href="{{ u('Order/index',['status'=>'3','nav'=>'nav4']) }}">{{$ordercount['comfirm'][1]}}</a>)</span>
                    </li>
                </ul>
                <ul class="m-ddlst fl">                    
                    <li>
                       待服务订单<span>（<a href="{{ u('ServiceOrder/index',['status'=>'1','nav'=>'nav2']) }}">{{$ordercount['unfinished'][2]}}</a>）</span>
                    </li>
                    <li>
                        待完成订单<span>（<a href="{{ u('ServiceOrder/index',['status'=>'2','nav'=>'nav3']) }}">{{$ordercount['unpay'][2]}}</a>）</span>
                    </li>
                    <li>
                        已完成订单<span>（<a href="{{ u('ServiceOrder/index',['status'=>'3','nav'=>'nav4']) }}">{{$ordercount['comfirm'][2]}}</a>）</span>
                    </li>
                </ul>
            </div>
            <div class="u-ddgk fr">
                <p class="lh55 clearfix">
                    <span class="fl f16 ml15">评价分布</span>
                    <a href="{{ u('Comment/index') }}" class="fr btn f-80btn mr15" style="margin-top:13px;">查看详情</a>
                </p>
                <div class="m-fbt">
                    <div id="container" style="width:400px;height:180px;margin-top:-15px;"></div>
                </div>
            </div>
        </div>
        <!-- 本日营业统计 -->
        <div class="m-yyct">
            <p class="f-tt clearfix">
                <span class="fl f16">本日营业统计</span>
                <a href="{{ u('Report/index') }}" class="btn f-30btn fr">查看详情</a>
            </p>
            <div class="m-bgct">
                <table>
                    <thead>
                        <tr>
                            <th width="20%">
                                订单分类
                            </th>
                            <th width="20%">
                                订单数
                            </th>
                            <th width="20%">
                                时长
                            </th>
                            <th width="20%">
                                交易额
                            </th>
                            <th width="20%">
                                实际收入
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>完成订单</td>
                            <td>{{ $today['comfirm']['num'] }}</td>
                            <td>{{ $today['comfirm']['duration'] }}</td>
                            <td>{{ $today['comfirm']['trading'] }}</td>
                            <td>{{ $today['comfirm']['total'] }}</td>
                        </tr>
                        <tr>
                            <td>未完成订单</td>
                            <td>{{ $today['unfinished']['num'] }}</td>
                            <td>{{ $today['unfinished']['duration'] }}</td>
                            <td>{{ $today['unfinished']['trading'] }}</td>
                            <td>{{ $today['unfinished']['total'] }}</td>
                        </tr>
                        <tr>
                            <td>总计</td>
                            <td>{{ $today['total']['num']}}</td>
                            <td>{{ $today['total']['duration']}}</td>
                            <td>{{ $today['total']['trading']}}</td>
                            <td>{{ $today['total']['total']}}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@stop
@section('js')
<script type="text/javascript">
$(function () {
    $('#container').highcharts({
        chart: {
            plotBackgroundColor: null,
            plotBorderWidth: null,
            plotShadow: false
        },
        title: {
            text: ''
        },
        tooltip: {
            pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
        },
        plotOptions: {
            pie: {
                allowPointSelect: true,
                cursor: 'pointer',
                dataLabels: {
                    enabled: true,
                    color: '#000000',
                    connectorColor: '#000000',
                    format: '<b>{point.name}</b>: {point.percentage:.1f} %'
                }
            }
        },
        series: [{
            type: 'pie',
            name: 'Browser share',
            data: [
                {
                    name: '好评',
                    y: {{ $sellerInfo['extend']['commentGoodCount'] or 0}},
                    sliced: true,
                    selected: true
                },
                ['中评',   {{ $sellerInfo['extend']['commentNeutralCount'] or 0}}],
                ['差评',   {{ $sellerInfo['extend']['commentBadCount'] or 0}}]
            ]
        }],
        navigation: {
                buttonOptions: {
                    enabled: false
                }
            }
    });
});  

var good = {{ $sellerInfo['extend']['commentGoodCount'] or 0}};
var neutral = {{ $sellerInfo['extend']['commentNeutralCount'] or 0}};
var bad = {{ $sellerInfo['extend']['commentBadCount'] or 0}}; 
if(good==0 && neutral==0 && bad==0) {
    $("#container").remove();
    $('.m-fbt').html("<img src={{ asset('images/default_pie.jpg') }} style='width:410px;margin-left:50px;'>").hide().fadeIn(1500);
}       
</script>
@stop



