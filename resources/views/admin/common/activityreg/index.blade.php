@extends('admin._layouts.base')
@section('right_content')
    @yizan_begin
    <yz:list>
        <search>
            <row>
                <item name="name" label="活动名称"></item>
                <yz:fitem label="状态">
                    <yz:select name="status" options="0,1,2" texts="全部,启用,禁用" selected="$search_args['status']"></yz:select>
                </yz:fitem>
            </row>
            <row>
                <item name="startTime" label="活动开始时间" type="date"></item>
                <item name="endTime" label="活动结束时间" type="date"></item>
                <btn type="search"></btn>
            </row>
        </search>
        <btns>
            <linkbtn label="添加活动" url="{{ u('ActivityReg/create') }}" css="btn-green"></linkbtn>
        </btns>
        <table relmodule="SystemGoods">
            <columns>

                <column code="name" label="姓名" width="120" iscut="1"></column>
                <column label="创建时间" width="120" iscut="1">{{ yztime($list_item['createTime']) }}</column>
                <column label="活动时间" width="180">
                    {{ yztime($list_item['startTime'],'Y-m-d') }} 至 {{ yztime($list_item['endTime'],'Y-m-d') }}
                </column>
                <column label="状态" width="30" type="status" code="status">
                </column>

                <actions width="60">
                        <a href="{{ u('ActivityReg/edit',['id'=>$list_item['id']]) }}" class=" blu" data-pk="1" target="_self">编辑</a>
                    <action type="destroy" css="red"></action>
                </actions>
            </columns>
        </table>
    </yz:list>
    @yizan_end
@stop

