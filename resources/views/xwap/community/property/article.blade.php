@extends('xwap.community._layouts.base')

@section('show_top')
    <header class="bar bar-nav">
        <a class="button button-link button-nav pull-left back" href="#" data-transition='slide-out'>
            <span class="icon iconfont">&#xe600;</span>
        </a>
        <h1 class="title f16">社区公告</h1>
    </header>
@stop

@section('content')
    <!-- new -->
    <div class="content" id=''>
        @if($list)
            <div class="list-block media-list x-noticelst bfh0">
                <ul>
                    @foreach($list as $v)
                        <li>
                            <a href="{{ u('Property/articledetail', ['id'=>$v['id']]) }}" class="item-link item-content">
                                <div class="item-inner">
                                    <div class="item-title-row">
                                        <div class="item-title f14"><span class="on c-bg"></span>{{$v['title']}}</div>
                                    </div>
                                    <div class="item-subtitle mb10 c-gray">{!! mb_substr($v['content'], 0, 20) !!}......</div>
                                    <div class="item-text c-gray f12 ha">
                                        <span>{{ $v['createTime'] }}</span>
                                        <span class="fr">点击查看<i class="icon iconfont f13">&#xe602;</i></span>
                                    </div>
                                </div>
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
        @else
            <div class="x-null pa w100 tc">
                <i class="icon iconfont">&#xe645;</i>
                <p class="f12 c-gray mt10">暂时还没有社区公告！</p>
            </div>
        @endif
    </div>
@stop

@section($js)

@stop