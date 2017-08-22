@extends('wap.community._layouts.base')
@section('show_top')
    <div data-role="header" data-position="fixed" class="x-header">
        <h1>新手帮助</h1>
        <a href="@if(!empty($nav_back_url)) {{ $nav_back_url }} @else javascript:history.back(-1); @endif" data-iconpos="notext" class="x-back ui-nodisc-icon" data-shadow="false"></a>
    </div>
@stop
@section('content')
    <div class="y-gywm">
        <div>
            <p class="f14">{!! $userhelp !!}</p>
        </div>
    </div>
    @include('wap.community._layouts.bottom')
@stop


