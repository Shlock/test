@extends('xwap.community._layouts.base')

@section('show_top')
    <header class="bar bar-nav">
        <a class="button button-link button-nav pull-left back" href="#" data-transition='slide-out'>
            <span class="icon iconfont">&#xe600;</span>
        </a>
        <h1 class="title f16">关于我们</h1>
    </header>
@stop

@section('content')
    @include('xwap.community._layouts.bottom')
    <div class="content c-bgfff" id=''>
        <div class="y-about f14">
            <p>{!! $aboutus !!}</p>
        </div>
    </div>
@stop


