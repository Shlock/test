@extends('admin._layouts.base')
@section('css')
@stop
@section('right_content')
    @yizan_begin
    <yz:list>
        <table relmodule="Staffleave">
            <columns>
                <column code="name" label="服务机构" width="60" iscut="1">{{ $list_item['seller']['name'] }}</column>
                <column code="name" label="姓名" width="60" iscut="1">{{ $list_item['staff']['name'] }}</column>
                <column code="beginTime" label="开始时间" width="60" iscut="1"></column>
                <column code="endTime" label="结束时间" width="60" iscut="1"></column>
                <column code="remark" label="请假理由" width="60" iscut="1">
	                <p style='text-align:left;display:block;word-break: break-all;word-wrap: break-word;'> 
						{{ $list_item['remark'] }}
					</p> 
                </column>
                <column code="isAgree" label="是否同意" width="60" iscut="1"></column>
                <actions width="90">
                    <action label="查看详情" css="blu">
                        <attrs>
                            <url>{{ u('Staffleave/detail', ['id'=>$list_item['id']]) }}</url>
                        </attrs>
                    </action>
                    <action type="destroy" css="red"></action>
                </actions>
            </columns>
        </table>
    </yz:list>
    @yizan_end
@stop

@section('js')

@stop
