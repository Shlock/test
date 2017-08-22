@extends('wap.community._layouts.base')

@section('show_top')
    <div data-role="header" data-position="fixed" class="x-header">
        <h1>业主信息</h1>
        <a href="@if(!empty($nav_back_url)) {!! $nav_back_url !!} @else {{ u('Property/index',['districtId'=>$args['districtId']])}} @endif" data-iconpos="notext" class="x-back ui-nodisc-icon" data-shadow="false"></a>
    </div>
@stop

@section('content')
    <div role="main" data-role="content">
        <div class="x-mt-1em"></div>
        <ul class="x-bgfff2 f14">
            <li class="x-joinxq">
                业主名<span class="fr c-green">{{$data['name']}}</span>
            </li>
            <li class="x-joinxq">
                房产单元<span class="fr c-green">{{$data['build']['name']}}#{{$data['room']['roomNum']}}</span>
            </li>
            <li class="x-joinxq">
                建筑面积<span class="fr c-green">{{$data['room']['structureArea']}}平方</span>
            </li>
            <li class="x-joinxq">
                套内面积<span class="fr c-green">{{$data['room']['roomArea']}}平方</span>
            </li>
            <li class="x-joinxq">
                手机<span class="fr c-green">{{$data['mobile']}}</span>
            </li>
            <li class="x-joinxq">
                入住时间<span class="fr c-green">{{ yzday($data['room']['intakeTime'])}}</span>
            </li>
            <li class="x-joinxq">
                物业费<span class="f12 c-green">（每月）</span>
                <span class="fr c-green">￥{{$data['room']['propertyFee']}}</span>
            </li>
        </ul>
    </div>
@stop