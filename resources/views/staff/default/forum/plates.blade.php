@extends('wap.community._layouts.base')

@section('show_top')
    <div data-role="header" data-position="fixed" class="x-header">
        <h1>所有版块</h1>
        <a href="@if(!empty($nav_back_url)) {!! $nav_back_url !!} @else javascript:history.back(-1); @endif" data-iconpos="notext" class="x-back ui-nodisc-icon" data-shadow="false"></a>
    </div>
@stop

@section('content')
    <div role="main" class="ui-content x-sorta">
        @if($plates)
        <ul class="x-allsort">
            @foreach($plates as $v)
            <li><a href="@if($v['id'] == 1){{u('Property/index',['id'=>$v['id']])}} @else{{u('Forum/lists',['plateId'=>$v['id']])}}@endif"><img src="@if(!empty($v['icon'])) {{formatImage($v['icon'],36,36)}} @else {{ asset('wap/community/client/images/b1.png')}} @endif">{{$v['name']}}<i class="x-rightico"></i></a></li>
            @endforeach
        </ul>
        @endif
    </div>
@stop
