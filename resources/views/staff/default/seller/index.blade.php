@extends('staff.default._layouts.base')
@section('title'){{$title}}@stop
@section('show_top')
    <header class="bar bar-nav">
        <h1 class="title">{{$title}}</h1>
    </header>
@stop

@section('contentcss')hasbottom @stop
@section('content')
    <div class="business_statistics">
        <h3>今日营业统计</h3>
        <p><a href="#" onclick="JumpURL('{{u('Seller/account')}}','#seller_account_view_1',2)"><span>账户余额</span><i>￥{{$seller['balance'] or 0}}</i></a></p>
        {{-- class="pageloding" external--}}
    </div>
    <div class="busines_nun  w_b">
        <div class="w_b_f_1 tc child">
            <p>{{$seller['orderNum'] or 0}}</p>
            <div>订单数</div>
        </div>
        <div class="w_b_f_1 tc child">
            <p>￥{{$seller['turnover'] or 0}}</p>
            <div>营业额</div>
        </div>
    </div>
    <div class="blank050"></div>
    <div class="shop-nav-parent">
        <div class="shop-nav mt070 w_b b-t">
            <a href="#" onclick="JumpURL('{{u('Seller/goodslists')}}','#seller_goodslists_view',2)" class="w_b_f_1">
                <div class="b-c6a6f1"><i class="icon iconfont">&#xe65c;</i></div>
                <p>商品管理</p>
            </a>
            <a href="#" onclick="JumpURL('{{u('Seller/seller')}}','#seller_seller_view',2)" class="w_b_f_1">
                <div class="b-a2d377"><i class="icon iconfont">&#xe65d;</i></div>
                <p>服务管理</p>
            </a>
            <a href="#" onclick="JumpURL('{{u('Seller/evaluation')}}','#seller_evaluation_view',2)" class="w_b_f_1">
                <div class="b-fdb563"><i class="icon iconfont">&#xe653;</i></div>
                <p>评价管理</p>
            </a>
        </div>
        <div class="shop-nav mt070 w_b ">
            <a href="#" onclick="JumpURL('{{u('Seller/analysis')}}','#seller_analysis_view_1',2)" class="w_b_f_1">
                <div class="b-88dac3"><i class="icon iconfont">&#xe65b;</i></div>
                <p>经营分析</p>
            </a>
            <a href="#" onclick="JumpURL('{{u('Seller/info')}}','#seller_info_view',2)" class="w_b_f_1">
                <div class="b-90cafc"><i class="icon iconfont">&#xe659;</i></div>
                <p>店铺信息</p>
            </a>
            <a href="#" class="w_b_f_1 seller_money">
                <div class="b-fb8486"><i class="icon iconfont">&#xe657;</i></div>
                <p>店铺对账</p>
            </a>
        </div>
    </div>
@stop
@section($js)
<script type="text/javascript">
    $(function(){
           $(".seller_money").on('click',function(){
                $.toast('暂未开放');
           });
    })
</script>
@stop
@section('preloader')@stop