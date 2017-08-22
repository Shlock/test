@extends('seller._layouts.base')
@section('css')
<style type="text/css">
    .m-tab table tbody td{padding: 5px 0px;}
</style>
@stop
@section('content')
    <div>
        <div class="m-zjgltbg">                 
            <div class="p10"> 
                <div class="g-fwgl">
                    <p class="f-bhtt f14 clearfix">
                        <span class="ml15 fl">门禁列表</span>
                    </p>
                </div> 
                <div class="m-tab m-smfw-ser">
                    @yizan_begin
                        <yz:list>
                            <btns>
                                <linkbtn label="添加门禁" url="{{ u('DoorAccess/create') }}" css="btn-gray"></linkbtn> 
                                <linkbtn label="导出到EXCEL" type="export" url="{{ u('DoorAccess/export') }}" css="btn-gray"></linkbtn>
                            </btns> 
                            <table css="goodstable" relmodule="GoodsSeller">
                                <columns>
                                <column code="id" label="编号" ></column>  
                                <column label="门禁名称" align="center" >
                                    {{$list_item['name']}}
                                </column> 
                                <column code="pid" label="门禁ID"  ></column>  
                                <column code="type" label="门禁类型"  >
                                    @if($list_item['type'] == 1)
                                    小区门禁
                                    @else
                                    楼宇门禁
                                    @endif
                                </column> 
                                <column label="楼栋" >
                                    {{$list_item['build']['name']}}
                                </column> 
                                <column code="remark"  label="备注"></column>
                                <actions> 
                                    <action type="edit" css="blu"></action> 
                                    <action type="destroy" css="red"></action> 
                                </actions>
                                </columns>
                            </table>
                        </yz:list>
                    @yizan_end
                </div>
            </div>
        </div>
    </div>
@stop

@section('js')
@stop
