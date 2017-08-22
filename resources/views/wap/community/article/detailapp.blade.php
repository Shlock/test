@extends('wap.community._layouts.base')
@section('title'){{ $data['title'] }}@stop
@section('show_top')
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