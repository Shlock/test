@extends('staff.default._layouts.base')
@section('title'){{$title}}@stop
@section('show_top')
    <header class="bar bar-nav">
        <input id="chosedate-input" type="text" readonly onChange="$.onChange(this)"/>
        <div class="surchbmanage "><!--z-open-->
            <div class="surchbmanagebox">
                <input type="search" onkeydown="$.keywords();" id='keywords' placeholder='输入关键字...' value="{{Input::get('keywords')}}"/>
                <span class="icon iconfont" id="keywords_ck">&#xe674;</span>
            </div>
            <div class="closesurchbmanage">关闭</div>
        </div>
        <a class="button button-link button-nav pull-left @if(empty($nav_back_url)) back @endif" href="@if(!empty($nav_back_url)) {{ $nav_back_url }} @else javascript:history.back(-1); @endif" data-transition='slide-out'>
            <span class="icon iconfont">&#xe66f;</span>
        </a>
        <a class="icon button pull-right  button-link button-nav surchbmanagebtn" data-transition='slide-out'>
            <span class="icon iconfont">&#xe674;</span>
        </a>
        <h1 class="title">{{$title}}</h1>

    </header>
@stop
@section('contentcss')admin-order-bmanage infinite-scroll infinite-scroll-bottom pull-to-refresh-content @stop
@section('distance')data-ptr-distance="20" @stop
@section('content')
    @include('staff.default._layouts.refresh')
    <!-- 下面是正文 -->
    <div class="buttons-tab">
        <a onclick="JumpURL('{{u('Order/index',['status'=>2])}}','#order_index_view_2',2)" href="#" class="tab ingCount @if($status == 1) active @endif button">进行中(<em>{{$list['ingCount'] or 0}}</em>)</a>
        <a onclick="JumpURL('{{u('Order/index',['status'=>1])}}','#order_index_view_1',2)" href="#" class="tab count @if($status == 0) active @endif button">全部(<em>{{$list['count'] or 0}}</em>)</a>
    </div>
    @if($list['orders'])
        <div class="content-block list-container lists_item_ajax">
            @include("staff.default.order.item")
        </div>
    @else
        <div class="x-null tc">
            <i class="icon iconfont">&#xe60c;</i>
            <p>很抱歉，暂无订单</p>
        </div>
    @endif
@stop

@section($js)
    <script type="text/javascript">
        $("#{{$id_action}}{{$ajaxurl_page}} #chosedate-input").calendar({
            onClose:function(){
                $("#{{$id_action}}{{$ajaxurl_page}} .picker-calendar").remove();
            }
        });
    </script>
@stop