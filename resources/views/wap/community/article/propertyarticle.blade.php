@extends('wap.community._layouts.base')
@section('show_top')
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
