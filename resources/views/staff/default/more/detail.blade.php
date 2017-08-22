@extends('staff.default._layouts.base')
@section('title'){{$title}}@stop

@section('contentcss')bcf pull-to-refresh-content @stop
@section('distance')data-ptr-distance="20"@stop
@section('content')
    @include('staff.default._layouts.refresh')
	<div class="y-gywm">
    	<div class="lists_item_ajax">
    		<p class="f14">{!! $data !!}</p>
        </div>
    </div>
@stop
@section('preloader')@stop
