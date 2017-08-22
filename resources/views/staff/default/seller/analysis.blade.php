@extends('staff.default._layouts.base')
@section('title'){{$title}}@stop
@section('show_top')
    <header class="bar bar-nav">
        <a class="button button-link button-nav pull-left" href="#" onclick="JumpURL('{{ u('Seller/index') }}','#seller_index_view',2)" data-transition='slide-out'>
            <span class="icon iconfont">&#xe64c;</span>
        </a>
        <h1 class="title">{{$title}}</h1>
    </header>
@stop
@section('css')
@stop
@section('content')
    <div class="admin-shop-analysis">
        <div class="content-padded">
            <p class="buttons-row">
                <a href="#" onclick="JumpURL('{{ u('Seller/analysis',['days'=> 1]) }}','#seller_analysis_view_1',2)" class="button @if($args['days'] == 1) active @endif ">最近1天</a>
                <a href="#" onclick="JumpURL('{{ u('Seller/analysis',['days'=> 3]) }}','#seller_analysis_view_3',2)" class="button @if($args['days'] == 3) active @endif ">最近3天</a>
                <a href="#" onclick="JumpURL('{{ u('Seller/analysis',['days'=> 7]) }}','#seller_analysis_view_7',2)" class="button @if($args['days'] == 7) active @endif ">最近7天</a>
                <a href="#" onclick="JumpURL('{{ u('Seller/analysis',['days'=>30]) }}','#seller_analysis_view_30',2)" class="button @if($args['days'] == 30) active @endif ">最近30天</a>
            </p>
        </div>
        <div class="buttons-tab">
            <a href="#tab1" class="tab-link active button">订单金额</a>
            <a href="#tab2" class="tab-link button">订单数量</a>
        </div>
        <div class="tabs">
            <div id="tab1" class="tab active">
                <canvas id="myChart_money"></canvas>
                <div class="list-block">
                    <ul>
                        <li class="item-content">
                            <div class="item-inner">
                                <div class="item-title">日期</div>
                                <div class="item-after">订单金额</div>
                            </div>
                        </li>
                        @foreach($data as $val)
                            <li class="item-content">
                                <div class="item-inner">
                                    <div class="item-title">{{ $val['date'] }}</div>
                                    <div class="item-after">¥{{$val['money']}}</div>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
            <div id="tab2" class="tab">
                <canvas id="myChart_num" width="320" height="200"></canvas>
                <div class="list-block">
                    <ul>
                        <li class="item-content">
                            <div class="item-inner">
                                <div class="item-title">日期</div>
                                <div class="item-after">订单数量</div>
                            </div>
                        </li>
                        @foreach($data as $val)
                            <li class="item-content">
                                <div class="item-inner">
                                    <div class="item-title">{{ $val['date'] }}</div>
                                    <div class="item-after">{{$val['num']}}</div>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
    @if($js == "bnt_js")
        <script src="{{ asset('seller/js/Chart.js') }}?{{TPL_VERSION }}" charset='utf-8'></script>
    @endif
    <script type="text/javascript">
        // $("#myChart").css({"width":$(window).width()+"px"});
        var data_myChart_money = {


            labels: [
                @foreach($data as $val)
                "{{ $val['date'] }}",
                @endforeach
           ],
            datasets : [
                {
                    fillColor : "#9be2fc",
                    strokeColor : "#9be2fc",
                    pointColor : "#00adf1",
                    pointStrokeColor : "#fff",
                    data:[
                        @foreach($data as $val)
                        "{{ $val['money']}}",
                        @endforeach
                       ]
                }
            ]
        }
        var data_myChart_num = {
            labels: [
                @foreach($data as $val)
                "{{ $val['date'] }}",
                @endforeach
           ],
            datasets : [
                {
                    fillColor : "#9be2fc",
                    strokeColor : "#9be2fc",
                    pointColor : "#00adf1",
                    pointStrokeColor : "#fff",
                    data: [
                        @foreach($data as $val)
                        "{{$val['num']}}",
                        @endforeach
                    ]
                }
            ]
        }

        var myChart_money = document.getElementById("myChart_money").getContext("2d");
        var myChart_num = document.getElementById("myChart_num").getContext("2d");
        var myNewChart = new Chart(myChart_money).Line(data_myChart_money);
        var myNewChart = new Chart(myChart_num).Line(data_myChart_num);
    </script>
@stop