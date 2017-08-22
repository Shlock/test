@extends('admin._layouts.base')
@section('css')
@stop
@section('right_content')
    @yizan_begin
    <yz:list>
        <btns>
            <btn type="destroy" label="删除"></btn>
        </btns>
        <table checkbox="1">
            <columns>
                <column code="id" label="编号" width="30"></column>
                <column code="content" label="内容" align="left"></column>
                <column code="username" label="收件人" align="left" width="120"></column>
                <column code="sendTime" label="发送时间" type="time" width="120"></column>
                <column code="typeStr" label="类型" width="60"></column>
                <actions width="60">
                    <p><action type="destroy" css="red"></action></p>
                </actions>
            </columns>
        </table>
    </yz:list>
    @yizan_end
@stop