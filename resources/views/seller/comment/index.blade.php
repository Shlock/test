@extends('seller._layouts.base')
@section('css')
<style type="text/css">
    .m-ydtt .f-tt{line-height: 30px;}
    .star-rank{
        width: 85px;
        height: 40px;
        background: url("{{ asset('wap/community/client/images/ico/star.png') }}") left center repeat-x;
        background-size: 17px 12px;
    }
    .star-score{
        height: 40px;
        background: url("{{ asset('wap/community/client/images/ico/star1.png') }}") left center repeat-x;
        background-size: 17px 12px;
    }
</style>
@stop
@section('content')
<?php 
    $reply = [
        ['id'=> 0,'name'=>'全部'],
        ['id'=>'good','name'=>'好评'],
        ['id'=>'neutral','name'=>'中评'],
        ['id'=>'bad','name'=>'差评'],
    ];
 ?>  
<div class="ma">
    <div class="m-ydtt" style="margin-top:0px;">
        <div class="x-bbmain">
            <div class="x-pjgltt">评价管理</div>
            @yizan_begin
                <yz:list>
                    <search>
                        <row>
                            <yz:select name="result" options="$reply" textfield="name" valuefield="id" css="fl mr10" selected="$search_args['result']">></yz:select>
                            <item name="beginTime" label="开始时间" type="date"></item>
                            <item name="endTime"   label="结始时间" type="date"></item> 
                            <item name="orderSn" label="订单编号"></item>
                            <btn type="search" css="btn-gray"></btn>
                        </row>
                    </search>
                    <table>
                    	<div class="plall">
                        <ul class="x-plglul">
                        @foreach($list as $key => $val)
                        <li class="clearfix" style="position:relative;">
                            <div class="x-plglf9" style="width:100%">
                                <a href="javascript:;" class="x-pjimg" style="cursor:default"><img src="{{ $val['user']['avatar'] or asset('images/default_headimg.jpg')}}"  style="max-width:80px;max-height:80px;" /></a>             
                                <div class="x-pjr">
                                    <div class="x-pjname">
                                        <div class="fl"><span>订单编号：</span>{{ $val['order']['sn'] }}</div>
                                        <span class="fr mr15">{{ yztime($val['createTime']) }}</span>
                                    </div>
                                    <div><span>用户名称：</span>{{ $val['user']['name'] }}</div>
                                    <div style="line-height: 40px;">
                                        <span class="fl">评价星级：</span>
                                        <div class="star-rank fl">
                                            <div class="star-score" style="width:{{$val['star'] * 20}}%;"></div>
                                        </div>
                                    </div>
                                    <div class="x-yhpl mt20">
                                        <div class="x-khpj"><span>评价内容：</span>{{ $val['content'] }}</div>
                                        <ul class="x-plglimg">
                                            @foreach($val['images'] as $k => $v)
                                            <li><a href="{{ $v }}" target="_new"><img src="{{ $v }}" /></a></li> 
                                            @endforeach($list as $key => $val)
                                        </ul>
                                    </div>
                                    <div class="x-pjname mt10">
                                        <div class="fl"><span>回复：</span>{{ $val['reply'] }}</div>
                                    </div>

                                </div>
                            </div>
                            <div class="x-plglr" style="position:absolute;bottom:50px;right:0px;">
                                <a class="btn f-tj fr btn f-30btn mt5 mr15" href="{{ u('Comment/reply',['id'=>$val['id']]) }}">回复</a>
                            </div>
                        </li> 
                        @endforeach
                        </ul>
                        </div>
                </table>
                </yz:list>
            @yizan_end
        </div>
    </div>
</div>
@stop
@section('js')
<script type="text/javascript">
    $(function(){
        $('#yzForm').submit(function(){
            var beginTime = $("#beginTime").val();
            var endTime = $("#endTime").val();
            if(beginTime!='' || endTime!='') {
                if(beginTime==''){
                    alert("开始时间不能为空");return false;
                }
                else if(endTime==''){
                    alert("结束时间不能为空");return false;
                }
                else if(endTime < beginTime){
                    alert("结束时间不能大于开始时间");return false;
                }
            }
        });
    });
</script>
@stop
