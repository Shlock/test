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
<div class="ma">
    <div class="m-ydtt" style="margin-top:0px;">
        <div class="x-bbmain">
            <div class="x-pjgltt">门禁使用记录</div>
            @yizan_begin
                <yz:list>
                    <search>
                        <row>
                            <item name="name" label="门禁名称"></item> 
                            <item name="userName" label="姓名"></item>   
                            <item name="build" label="楼栋号"></item>  
                            <item name="roomNum" label="房间号"></item>    
                            <item name="beginTime" label="开始时间" type="date"></item>
                            <item name="endTime" label="结束时间" type="date"></item>
                            <btn type="search" css="btn-gray"></btn>
                        </row>
                    </search> 
                    <btns>
                        <linkbtn label="导出到EXCEL" type="export" url="{{ u('DoorOpenLog/export') }}" css="btn-gray"></linkbtn>
                    </btns>
                    <table>
                        <columns>
                            <column code="id" label="编号"  ></column>  
                            <column label="门禁名称"  >
                                {{$list_item['door']['name']}}
                            </column>   
                            <column label="楼栋号"  >
                                {{$list_item['build']['name']}}
                            </column> 
                            <column label="房间" align="center" >
                                {{$list_item['room']['roomNum']}}
                            </column> 
                            <column label="业主姓名" align="center" >
                                {{$list_item['puser']['name']}}
                            </column> 
                            <column code="contacts" label="联系人"  >
                                {{$list_item['puser']['mobile']}}
                            </column>  
                            <column label="开门时间" >
                                {{yztime($list_item['createTime'])}}
                            </column> 
                        </columns>
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
