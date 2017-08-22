@extends('wap.community._layouts.base')
@section('show_top')
    <div data-role="header" data-position="fixed" class="x-header">
        <h1>全部分类</h1>
        <a href="@if(!empty($nav_back_url)) {{ $nav_back_url }} @else {{u('Index/index')}} @endif" data-iconpos="notext" class="x-back ui-nodisc-icon" data-shadow="false"></a>
    </div>
@stop
@section('content') 
    <div role="main" class="ui-content x-sorta">
        <ul class="x-allsort">
            @foreach($cates as $item)
            <li><a href="{{u('Seller/index',['id'=>$item['id']])}}"><img src="{{ $item['logo']}}">{{ $item['name']}}<i class="x-rightico"></i></a></li>
                @foreach($item['childs'] as $val)
                    <li style="padding-left:40px;"><a href="{{u('Seller/index',['id'=>$val['id']])}}">{{ $val['name']}}</a></li>
                @endforeach
            @endforeach
        </ul>
    </div>

    @include('wap.community._layouts.swiper')
@stop 
<script type="text/javascript">
    $.SwiperInit('.x-allsort','li',"{{ u('Seller/cates') }}");
</script>
