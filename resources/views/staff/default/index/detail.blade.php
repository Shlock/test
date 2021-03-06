@extends('staff.default._layouts.base')
@section('title'){{$title}}@stop
@section('show_top')
    <header class="bar bar-nav">
        <a class="button button-link button-nav pull-left" href="#" onclick="JumpURL('{{ $nav_back_url }}','{{ $url_css }}',2)" data-transition='slide-out'>
            <span class="icon iconfont">&#xe64c;</span>
        </a>
        <h1 class="title">{{$title}}</h1>
    </header>
@stop


@section('contentcss')admin-order-bmanage pull-to-refresh-content @stop
@section('distance')data-ptr-distance="20" @stop
@section('content')
    @include('staff.default._layouts.refresh')
    <!-- 下面是正文 -->
    <div class="order-timestute  bt0">
        <div class="order-red"> </div>
        <div class="order-time  w_b_f_1">下单时间：{{$data['createTime']}}</div>
        <div class="order-state  w_b_f_1 mr045">{{$data['orderStatusStr']}}</div>
    </div>
    <div class="blank045"> </div>
    <div external>
        <div class="send-border"> </div>
        <div class="send-content">
            <div class="blank055"> </div>
            <div class="send-info">
                <span class="send-name">{{$data['name']}}</span>
                <span onclick="javascript:;" class="send-phone">{{$data['mobile']}}</span>
            </div>
            <div class="blank070"> </div>
            <div class="send-adress">
                <span>{{$data['province'] . $data['city'] . $data['area'] . $data['address']}}</span>
                <span class="send-distance bdr-50">{{$data['distance']}}km</span>
            </div>
            <div class="blank045"> </div>
            <div class="send-time">预计到达时间：{{$data['appTime']}}</div>
            <div class="blank0625"> </div>
        </div>
        <div class="send-border"> </div>
    </div>
    <div class="blank050"></div>
    <div class="list-block m0 f0675 ">
        <ul>
            <li class="item-content">
                <div class="item-inner flex">
                    <div class="item-title">商品</div>
                    <div class="item-after">数量</div>
                    <div class="item-after">金额</div>
                </div>
            </li>
            @foreach($data['orderGoods'] as $v)
                <li class="item-content">
                    <div class="item-inner flex">
                        <div class="item-title flex-1">{{$v['goodsName']}}</div>
                        <div class="item-after flex-1">×{{$v['num']}}</div>
                        <div class="item-after">￥{{$v['price']}}</div>
                    </div>
                </li>
            @endforeach

            <li class="item-content">
                <div class="item-inner">
                    <div class="item-title">配送费</div>
                    <div class="item-after"></div>
                    <div class="item-after fred">￥{{$data['freight']}}</div>
                </div>
            </li>
            <li class="item-content">
                <div class="item-inner">
                    <div class="item-title">合计</div>
                    <div class="item-after">×{{$data['count']}}</div>
                    <div class="item-after fred">￥{{$data['totalFee']}}</div>
                </div>
            </li>
        </ul>
    </div>
    <div class="blank050"></div>
    <div class="order-info ">
        <div class="blank0925"> </div>
        <span>在线支付订单：{{$data['orderStatusStr']}}</span>
        <div class="blank0775"> </div>
        <span>订单编号：{{$data['sn']}}</span>
        <div class="blank0775"> </div>
        @if(in_array($role,['1','3','5','7']))
            <div class="order-padding fine-bor-top flex">
                <div class="flex-2">
                    <div class="f_l flex-1">@if($data['orderType'] == 1)配送员@else服务人员@endif:&nbsp;&nbsp;</div>
                    <div class="f_l flex-1">{{$data['staff']['name']}}</div>
                </div>
                <a href="tel:{{$data['staff']['mobile']}}" external class="fred f_r">{{$data['staff']['mobile']}}<i class="iconfont">&#xe60e;</i></a>
                @if($data['isCanChangeStaff'])
                    <a id="isCanChangeStaff" href="{{u('Order/staff',['id'=>$data['id'],'staffId'=>$data['staff']['id'],'type'=>$data['orderType'],'tpl'=>$tpl])}}" class="mr045 f_r text-align-right" >更换<i class="iconfont">&#xe64b;</i></a>
                    {{--onclick="$.isCanChangeStaff({{$data['id']}})"--}}
                @endif
            </div>
            <div class="blank0825"> </div>
        @endif

        @if($data['buyRemark'])
            <span>备&nbsp;&nbsp;&nbsp;注：{{$data['buyRemark']}}</span>
            <div class="blank0775"> </div>
        @endif

    </div>
    <div class="blank085"> </div>
    @include('staff.default._layouts.order')
    <div class="blank0825"> </div>
@stop


@section('preloader')@stop 