@extends('wap.community._layouts.base')
@section('show_top')
    <div data-role="header" data-position="fixed" class="x-header">
        <h1>社区公告详情</h1>
        <a href="@if(!empty($nav_back_url)) {!! $nav_back_url !!} @else javascript:history.back(-1); @endif" data-iconpos="notext" class="x-back ui-nodisc-icon" data-shadow="false"></a>
    </div>
@stop

@section('content')
    <div role="main" data-role="content">
        <div class="x-mt-1em"></div>
        <div class="x-bgfff2">
            <p class="tc pt10"><b>{{$data['title']}}</b></p>
            <p class="tc f12 c-green mt5">{{ yzday($data['createTime']) }}</p>
            <div class="f14 mt5 x-sqNdetail">
                <p class="tt">{!! $data['content'] !!}</p>
            </div>
        </div>
    </div>
<script type="text/javascript">
    $(function() {
        $(".x-bgfff2").css("min-height",$(window).height()-45);
    })
</script>
@stop
