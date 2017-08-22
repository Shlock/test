@extends('xwap.community._layouts.base')

@section('show_top')
    <header class="bar bar-nav">
        <a class="button button-link button-nav pull-left back" href="#" data-transition='slide-out'>
            <span class="icon iconfont">&#xe600;</span>
        </a>
        <h1 class="title f16">业主信息</h1>
    </header>
@stop

@section('content')
    <div class="content" id=''>
        <!-- 业主信息 -->
        <div class="list-block x-splotlst nobor f14 pb10">
            <ul>
                <li class="item-content">
                    <div class="item-inner">
                        <div class="item-title">业主名</div>
                        <div class="item-after c-gray">{{$data['name']}}</div>
                    </div>
                </li>
                <li class="item-content">
                    <div class="item-inner">
                        <div class="item-title">房产单元</div>
                        <div class="item-after c-gray">{{$data['build']['name']}}#{{$data['room']['roomNum']}}</div>
                    </div>
                </li>
                <li class="item-content">
                    <div class="item-inner">
                        <div class="item-title">建筑面积</div>
                        <div class="item-after c-gray">{{$data['room']['structureArea']}}平方</div>
                    </div>
                </li>
                <li class="item-content">
                    <div class="item-inner">
                        <div class="item-title">套内面积</div>
                        <div class="item-after c-gray">{{$data['room']['roomArea']}}平方</div>
                    </div>
                </li>
                <li class="item-content">
                    <div class="item-inner">
                        <div class="item-title">手机</div>
                        <div class="item-after c-gray">{{$data['mobile']}}</div>
                    </div>
                </li>
                <li class="item-content">
                    <div class="item-inner">
                        <div class="item-title">入住时间</div>
                        <div class="item-after c-gray">{{ yzday($data['room']['intakeTime'])}}</div>
                    </div>
                </li>
                <li class="item-content">
                    <div class="item-inner">
                        <div class="item-title">物业费<span class="f12 c-gray">（每月）</span></div>
                        <div class="item-after c-gray">￥{{$data['room']['propertyFee']}}</div>
                    </div>
                </li>
            </ul>
        </div>
    </div>
@stop