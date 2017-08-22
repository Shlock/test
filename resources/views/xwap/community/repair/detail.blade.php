@extends('xwap.community._layouts.base')

@section('show_top')
    <header class="bar bar-nav">
      <a class="button button-link button-nav pull-left back" href="#" data-transition='slide-out'>
        <span class="icon iconfont">&#xe600;</span>
      </a>
      <h1 class="title f16">报修记录</h1>
    </header>
@stop

@section('content')
    <div class="content" id=''>
      <div class="card x-records">
        <div class="card-header">
          <span class="f12 c-gray">{{ $data['createTime'] }}</span>
          <span class="f14 c-red">{{ $data['statusStr'] }}</span>
        </div>
        <div class="card-content">
            <p class="f18">{{$data['repairType']}}</p>
            <p>{{$data['content']}}</p>
            @if($data['images'])
                @foreach($data['images'] as $item)
                    <img src="{{$item}}">
                @endforeach
            @endif
        </div>
      </div>
    </div>
@stop

@section($js)
@stop