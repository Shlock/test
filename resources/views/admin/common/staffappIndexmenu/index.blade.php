@extends('admin._layouts.base')
@section('css')
@stop
@section('right_content')
@yizan_begin
	<a href="{{ url('UserAppIndexMenu/create') }}">添加</a>
	<a href="{{ url('UserAppIndexMenu/edit') }}">编辑</a>
@foreach ($errors->all() as $error)
    <p class="error">{{ $error }}</p>
 @endforeach
 
@yizan_end

@stop  
