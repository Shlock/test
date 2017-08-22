@extends('staff.default._layouts.base')
@section('title'){{$title}}@stop
@section('show_top')
    <header class="bar bar-nav">
        <a class="button button-link button-nav pull-left @if(empty($nav_back_url)) back @endif" href="#" data-transition='slide-out'>
            <span class="icon iconfont">&#xe64c;</span>
        </a>
        <span class="button button-link button-nav f_r"  onclick="$.changeStaff();">
            保存
        </span>
        <h1 class="title">{{$title}}</h1>
    </header>
@stop
@section('css')
@stop
@section('distance')id="service-add" @stop

@section('content')
    <ul class="employees_choose">
        @foreach($list as $k=>$vo)
        <li>
            <label class="w_b">
                <span class="name" style="width:30%;">{{ $vo['name'] }}</span>
                <span class="phone w_b_f_1">{{ $vo['mobile'] }}</span>
                <input type="radio" name="staff" class="radio-mt" @if($order['staffId'] == $vo['id']) checked="checked" @endif value="{{ $vo['id'] }}"/>
            </label>
        </li>
        @endforeach
    </ul>
@stop
@section($js)
<script type="text/javascript">
    $(function(){
        $.changeStaff = function() {
            var orderId = {{$order['id'] or 0}};
            if(!orderId){
                $.toast("错误订单号！");
                return false;
            }
            var staffId =$("input[name=staff]:checked").val();
            if(!staffId){
                $.toast("选择人员不合法或为空！");
                return false;
            }
            $.post("{{ u('Order/savestaff') }}",{'id':orderId,'staffId':staffId},function(res){
                if(res.code == 0){
                    $.toast(res.msg);
                    JumpURL('{!! $return_url !!}','#order_detail_view',2)
                }else{
                    $.toast(res.msg);
                }
            });
        }
    });
</script>
@stop

@section('show_nav')@stop
@section('preloader')@stop
