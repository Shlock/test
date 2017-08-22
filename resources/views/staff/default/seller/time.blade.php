@extends('staff.default._layouts.base')
@section('title'){{$title}}@stop
@section('show_top')
    <header class="bar bar-nav">
        <a class="button button-link button-nav pull-left" href="#" onclick="JumpURL('{{ u('Seller/info') }}','#seller_info_view',2)" data-transition='slide-out'>
            <span class="icon iconfont">&#xe64c;</span>
        </a>
        <a href="#" class="button button-link pull-right" id="J_save_time" onclick="$.saveitmes()">保存</a>
        <h1 class="title">{{$title}}</h1>
    </header>
@stop
@section('css')
@stop

@section('contentcss')infinite-scroll infinite-scroll-bottom @stop
@section('distance')data-distance="20" @stop
@section('content')
    <div class="admin-shop-sale-time">
        <div class="list-block">
            <ul>
                <li class="item-content">
                    <div class="item-inner" id="weeks">
                        @foreach($data['weeks'] as $v)
                            <div class="week">
                                <input type="checkbox" @if($v['status'] == 1) checked @endif name="week" value="{{$v['week']}}" class="week_checkbox" data-attr="{{$v['weekday']}}" />
                            </div>
                        @endforeach
                    </div>
                </li>
            </ul>
        </div>
        <div class="time_box clearfix">
            @foreach($data['hours'] as $v)
                <div class="time">
                    <input type="checkbox" name="hours" @if($v['status'] == 1) checked @endif  value="{{$v['hour']}}" class="time_checkbox" data-attr="{{$v['hour']}}" />
                </div>
            @endforeach
        </div>
    </div>
@stop

@section($js)
<script type="text/javascript">
    $.saveitmes = function  (){
        var week = [];
        $(".page-current input[name='week']:checked").each(function(){
            week.push($(this).val());
        });
        var hours = [];
        $(".page-current input[name='hours']:checked").each(function(){
            hours.push($(this).val());
        });
        var data  = {
            'week':week,
            'hours':hours
        }
        $.post("{{ u('Seller/savetime') }}",{'businessHour':data},function(res){
            $.toast(res.msg);
            JumpURL('{{ u('Seller/info') }}','#seller_info_view',2)
        },"json");
    }
</script>
@stop