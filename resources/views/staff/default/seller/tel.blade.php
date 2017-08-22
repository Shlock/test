@extends('staff.default._layouts.base')
@section('title'){{$title}}@stop
@section('show_top')
    <header class="bar bar-nav">
        <a class="button button-link button-nav pull-left" href="#" onclick="JumpURL('{{ u('Seller/info') }}','#seller_info_view',2)" data-transition='slide-out'>
            <span class="icon iconfont">&#xe64c;</span>
        </a>
        <a href="#" class="button button-link pull-right" onclick="$.saveinfo('{{u('Seller/savetel')}}')">保存</a>
        <h1 class="title">{{$title}}</h1>
    </header>
@stop
@section('contentcss')hasbottom @stop
@section('content')
    <div class="blank050"></div>
    <div class="fwfw">
        <span>联系电话：</span>
        <input type="text"id = "save_info_name" data-type="tel" placeholder="输入新联系电话"  value="">
    </div>
@stop


@section($js)
<script type="text/javascript">
    $(".page-current input[id='save_info_name']").attr("maxlength","11").attr("onkeyup", "this.value=this.value.replace(/\\D/g,'')").attr("onafterpaste", "this.value=this.value.replace(/\\D/g,'')");
</script>
@stop