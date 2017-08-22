@extends('admin._layouts.base_index')
@section('css')
@stop
@section('right_content')
    @yizan_begin
            <div class="p20">
            @yizan_yield("total")
                <div class="m-zxnavct">
                    <ul class="clearfix">
                        <li class="ls1 curl-top-left">
                            <a href="{{u('SellerApply/index',['isCheck' => 2])}}">
                                <p class="clearfix"><b class="fr f24 mr10 mt10">{{$total['seller']}}</b></p>
                                <p class="clearfix"><span class="fr f16 mr10">待审核商家</span></p>
                            </a>
                        </li>
                        <li class="ls2 curl-top-left">
                            <a href="{{u('Order/index')}}">
                                <p class="clearfix"><b class="fr f24 mr10 mt10">{{$total['order']}}</b></p>
                                <p class="clearfix"><span class="fr f16 mr10">订单管理</span></p>
                            </a>
                        </li>
                        <li class="ls3 curl-top-left">
                            <a href="{{u('UserRefund/index',array('status'=>0,'nav'=>1))}}">
                                <p class="clearfix"><b class="fr f24 mr10 mt10">{{$total['refund']}}</b></p>
                                <p class="clearfix"><span class="fr f16 mr10">待处理退款</span></p>
                            </a>
                        </li>
                        <li class="ls4 curl-top-left">
                            <a href="{{u('SellerWithdraw/index')}}">
                                <p class="clearfix"><b class="fr f24 mr10 mt10">{{$total['withdraw']}}</b></p>
                                <p class="clearfix"><span class="fr f16 mr10">待处理提现</span></p>
                            </a>
                        </li>
                    </ul>
                </div>
            @yizan_stop
                <div class="m-ddsjct">
                    <div class="f-tt clearfix">
                        <b class="fl f16 ml10">订单数据</b>
                        <ul class="fr clearfix">
                            <li class="@if(Input::get('type') == 1 || Input::get('type') == '') on @endif wobble-top">
                                <a href="{{u('Index/index',array('type'=>1))}}">今天</a>
                            </li>
                            <li class="@if(Input::get('type') == 2) on @endif wobble-top">
                                <a href="{{u('Index/index',array('type'=>2))}}">昨天</a>
                            </li>
                            <li class="@if(Input::get('type') == 3) on @endif wobble-top">
                                <a href="{{u('Index/index',array('type'=>3))}}">本周</a>
                            </li>
                            <li class="@if(Input::get('type') == 4) on @endif wobble-top">
                                <a href="{{u('Index/index',array('type'=>4))}}">本月</a>
                            </li>
                        </ul>
                    </div>
                    <!-- 订单表 -->
                    <div class="m-orderct">
                        <div class="u-orderct">
                        </div>
                    </div>
                </div>
            </div>
    @yizan_end
@stop

@section('js')
<script type="text/javascript">
    $(function () {
        $('.u-orderct').highcharts({
            title: {
                text: '订单数据概览',
                x: -20 //center
            },
            xAxis: {
                categories: [@foreach($data['time'] as $val)'{{$val}}',@endforeach]
            },
            yAxis: {
                min:0,
                title: {
                    text: ''
                },
                plotLines: [{
                    value: 0,
                    width: 1,
                    color: '#808080'
                }]
            },

            legend: {
                layout: 'vertical',
                align: 'right',
                verticalAlign: 'middle',
                borderWidth: 0
            },
            series: [
            @foreach($data['data'] as $val)
            {
                name: '{{$val['name']}}',
                data: [@foreach($val['val'] as $vo){{$vo}},@endforeach]
            },
            @endforeach
            ]
        });
    });
</script>
@stop
