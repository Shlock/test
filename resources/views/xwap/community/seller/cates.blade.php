@extends('xwap.community._layouts.base')

@section('show_top')
    <header class="bar bar-nav">
        <a class="button button-link button-nav pull-left pageloading" href="@if(!empty($nav_back_url)) {{ $nav_back_url }} @else {{u('Index/index')}} @endif" data-transition='slide-out'>
            <span class="icon iconfont">&#xe600;</span>
        </a>
        <h1 class="title f16">全部分类</h1>
    </header>
@stop

@section('content') 
    <div class="content c-bgfff" id=''>
        <div class="list-block x-sortlst bfh0">
            <ul class="x-allsort">
                @foreach($cates as $item)
                <li class="item-content" onclick="$.href('{{u('Seller/index',['id'=>$item['id']])}}')">
                    <div class="item-inner pl0">
                        <div class="item-title"><img src="{{ $item['logo']}}" class="x-sortpic mr10" />{{ $item['name']}}</div>
                        <i class="icon iconfont c-gray">&#xe602;</i>
                    </div>
                </li>
                    @foreach($item['childs'] as $val)
                        <li class="item-content ml20" onclick="$.href('{{u('Seller/index',['id'=>$val['id']])}}')">
                            <div class="item-inner pl0">
                                <div class="item-title">{{ $val['name']}}</div>
                                <i class="icon iconfont c-gray">&#xe602;</i>
                            </div>
                        </li>
                    @endforeach
                 @endforeach
            </ul>
        </div>
    </div>
    <!-- @include('wap.community._layouts.swiper') -->
@stop 

@section($js)
<script type="text/javascript">
    // $.SwiperInit('.x-allsort','li',"{{ u('Seller/cates') }}");
</script>
@stop
