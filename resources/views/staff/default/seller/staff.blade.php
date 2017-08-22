@extends('staff.default._layouts.base')
@section('title'){{$title}}@stop
@section('show_top')
    <header class="bar bar-nav">
        <a class="button button-link button-nav pull-left back"  data-transition='slide-out' onclick="$.changeStaff();">
            <span class="icon iconfont">&#xe64c;</span>
        </a>
        <span class="button button-link button-nav f_r"  onclick="$.changeStaff();">
            保存
        </span>
        <h1 class="title">{{$title}}</h1>
    </header>
@stop
@section('css')
@stop
@section('distance')id="service-add" @stop

@section('content')
    <ul class="employees_choose">
        @foreach($list as $k=>$vo)
        <li>
            <label class="w_b">
                <span class="name" style="width:30%;">{{ $vo['name'] }}</span>
                <span class="phone w_b_f_1">{{ $vo['mobile'] }}</span>
                <input type="checkbox" name="staff" class="radio-mt" @if(in_array($vo['id'],$staffId)) checked="checked" @endif value="{{ $vo['id'] }}"/>
            </label>
        </li>
        @endforeach
    </ul>
@stop
@section('show_nav')@stop
