@extends('wap.community._layouts.base')

@section('show_top')
    <div data-role="header" data-position="fixed" class="x-header">
        <h1>{{$data['name']}}</h1>
        <a href="@if(!empty($nav_back_url)) {!! $nav_back_url !!} @else {{ u('Property/index',['districtId'=>$args['districtId']])}} @endif" data-iconpos="notext" class="x-back ui-nodisc-icon" data-shadow="false"></a>
    </div>
@stop

@section('content')
    <div role="main" data-role="content">
        <div class="x-mt-1em"></div>
        <ul class="x-bgfff2 f14">
            <li class="x-joinxq">
                公司名<span class="fr c-green">{{$data['name']}}</span>
            </li>
            <li class="x-joinxq">
                联系人<span class="fr c-green">{{$data['contacts']}}</span>
            </li>
            <li class="x-joinxq">
                物业电话<span class="fr y-blue"><a href="tel:{{$data['serviceTel']}}">{{$data['serviceTel']}}</a></span>
            </li>
            <li class="x-joinxq clearfix">
                <span class="y-yyzzl">营业执照</span>
                <div class="y-yyzzimg">
                    <a href="{{$data['authenticate']['businessLicenceImg']}}"><img class="y-wdtxi" src="{{$data['authenticate']['businessLicenceImg']}}" /></a>
                    <i class="x-rightico y-jtright"></i>
                </div>
            </li>
        </ul>
    </div>
@stop