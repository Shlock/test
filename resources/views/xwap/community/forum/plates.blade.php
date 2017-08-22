@extends('xwap.community._layouts.base')

@section('show_top')
    <header class="bar bar-nav">
        <a class="button button-link button-nav pull-left back" href="#" data-transition='slide-out'>
            <span class="icon iconfont">&#xe600;</span>
        </a>
        <h1 class="title f16">所有版块</h1>
    </header>
@stop

@section('content')
    <div class="content c-bgfff" id=''>
        <div class="list-block x-sortlst bfh0">
            @if($plates)
                <ul>
                    @foreach($plates as $v)
                    <?php 
                        if($v['id'] == 1) { 
                            $url = u('Property/index',['id'=>$v['id']]);
                        }else{
                            $url = u('Forum/lists',['plateId'=>$v['id']]);
                        } 
                    ?>
                        <li class="item-content" onclick="$.router.load('{{$url}}', true);">
                            <div class="item-inner pl0">
                                <div class="item-title">
                                    <img src="@if(!empty($v['icon'])) {{formatImage($v['icon'],36,36)}} @else {{ asset('wap/community/client/images/b1.png')}} @endif" class="x-sortpic mr10" />{{$v['name']}}
                                </div>
                                <i class="icon iconfont c-gray">&#xe602;</i>
                            </div>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>
@stop
