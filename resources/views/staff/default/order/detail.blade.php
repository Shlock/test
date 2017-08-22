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
@section('contentcss')pull-to-refresh-content @stop
@section('distance')data-ptr-distance="20" @stop
@section('content')
    @include('staff.default._layouts.refresh')
    <div class="order-finish fred text-align-center bt1 title-text-align-center">{{$data['orderStatusStr']}}</div>
    <div class="order-wrap">
        <div class="delivery-info">
            <div class="delivery-title">配送信息</div>
            <div class="delivery-content">
                <div class="flex delivery-name-phone btop1">
                    <div class="flex-2 ml0575 br-1">
                        <span>{{$data['name']}}</span>
                        <span onclick="javascript:;" class="ml0575 f-light-color">{{$data['mobile']}}</span>
                    </div>
                    <a href="tel:{{$data['mobile']}}" external class="flex-1 text-align-center fred"><i class="iconfont">&#xe60e;</i></a>
                </div>
                <div class="flex delivery-location fine-bor-top clearfix">
                    <span class="flex-2 ml0575 br-1 delivery-location-padding" style="padding:.6rem 0;">{{$data['province'] . $data['city'] . $data['area'] . $data['address']}}</span>
                    <div class="flex-1 delivery-distance">
                        <div class=""><i class="iconfont">&#xe615;</i></div>
                        <div>{{$data['distance']}}千米</div>
                    </div>
                </div>
                <div class="servertime fine-bor-top">
                    服务时间:&nbsp;&nbsp;{{$data['appTime']}}
                </div>
            </div>
        </div>
        <div class="good-info">
            <div class="good-title">@if($data['orderType'] == 1)商品信息@else服务信息@endif</div>
            <div class="good-content btop1">
                <ul>
                    @foreach($data['orderGoods'] as $v)
                        <li class="item-content good-padding">
                            <div class="item-inner flex">
                                <div class="item-title flex-1">{{$v['goodsName']}}</div>
                                <div class="item-after flex-1">×{{$v['num']}}</div>
                                <div class="item-after">￥{{$v['price']}}</div>
                            </div>
                        </li>
                    @endforeach
                    <li class="item-content good-padding">
                        <div class="item-inner flex">
                            <div class="item-title flex-1">配送费</div>
                            <div class="item-after flex-1"></div>
                            <div class="item-after">￥{{$data['freight']}}</div>
                        </div>
                    </li>
                        <li class="item-content good-padding">
                            <div class="item-inner flex">
                            <div class="item-title flex-1">合&nbsp;&nbsp;&nbsp;&nbsp;计</div>
                            <div class="item-after flex-1"> </div>
                            <div class="item-after fred">￥{{$data['totalFee']}}</div>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
        <div>
            <div class="order-title">订单信息</div>
            <div class="order-content btop1">
                <div class="order-padding">支付方式:&nbsp;&nbsp;{{$data['payType']}}</div>
                <div class="order-padding">订单编号:&nbsp;&nbsp;{{$data['sn']}}</div>
                <div class="order-padding">店铺:&nbsp;&nbsp;{{$data['sellerName']}}</div>
                <div class="good-padding">顾客下单时间:&nbsp;&nbsp;{{$data['createTime']}}</div>
                @if($data['buyRemark'])
                    <div class="good-padding">备&nbsp;&nbsp;&nbsp;注：{{$data['buyRemark']}}</div>
                @endif
                @if(in_array($role,['1','3','5','7']))
                <div class="order-padding fine-bor-top flex">
                    <div class="flex-2">
                        <div class="f_l flex-1">@if($data['orderType'] == 1)配送员@else服务人员@endif:&nbsp;&nbsp;</div>
                        <div class="f_l flex-1">{{$data['staff']['name']}}</div>
                    </div>
                    <a href="tel:{{$data['staff']['mobile']}}" external class="fred f_r mr_percentage_5">{{$data['staff']['mobile']}}<i class="iconfont">&#xe60e;</i></a>
                    @if($data['isCanChangeStaff'])
                        <a id="isCanChangeStaff" href="{{u('Order/staff',['id'=>$data['id'],'staffId'=>$data['staff']['id'],'type'=>$data['orderType'],'tpl'=>$tpl])}}" class="mr045 f_r text-align-right" >更换<i class="iconfont">&#xe64b;</i></a>
                        {{--onclick="$.isCanChangeStaff({{$data['id']}})"--}}
                    @endif
                </div>
                <div class="blank0825"> </div>
                @endif
            </div>
        </div>
    </div>
    <div class="blank050"></div>
    @include('staff.default._layouts.order')
    <div class="blank0825"> </div>
@stop