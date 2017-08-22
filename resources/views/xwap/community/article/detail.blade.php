@extends('wap.community._layouts.base')
@section('show_top')
 <div data-role="header" data-position="fixed" class="x-header">
    <h1>@if(strlen($data['title'] > 15)){{ mb_substr($data['title'], 0, 15) }}...@else {{ $data['title']}} @endif</h1>
    <a href="@if(!empty($nav_back_url)) {{ $nav_back_url }} @else javascript:$.back(); @endif" data-iconpos="notext" class="x-back ui-nodisc-icon" data-shadow="false"></a>
</div>
@stop
@section('content')
<div class="y-gywm" style="background-color:#fff;">
	<div>
		<p class="f14">{!!$data['content']!!}</p>
    </div>
</div>
<script type="text/javascript">
    $(function() {
        $(".y-gywm").css("min-height",$(window).height()-45);
    })
</script>
@stop