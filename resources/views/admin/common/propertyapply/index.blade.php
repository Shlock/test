@extends('admin._layouts.base')
@section('css')
@stop
@section('right_content')
    @yizan_begin 
    <yz:list>
        <tabs>
            <navs>
                <nav label="未审核">
                    <attrs>
                        <url>{{ u('PropertyApply/index',['status'=>'2']) }}</url>
                        <css>@if($status == 2) on @endif</css>
                    </attrs>
                </nav>
                <nav label="已拒绝">
                    <attrs>
                        <url>{{ u('PropertyApply/index',['status'=>'1']) }}</url>
                        <css>@if($status == 1) on @endif</css>
                    </attrs>
                </nav> 
            </navs>
        </tabs>  
        <table>
            <columns>
				<column code="name" label="公司名称"  ></column>  
				<column label="小区名称" align="center" >
					{{$list_item['district']['name']}}
				</column> 
				<column code="contacts" label="联系人"  ></column>  
				<column code="mobile" label="联系电话" ></column>
				<column code="createTime" label="提交日期" >
					{{ yztime($list_item['createTime'])}}
				</column>
                <actions width="60"> 
                	@if($list_item['isCheck'] == 0)
                    <action label="审核" css="blu">
                        <attrs>
                            <url>{{ u('PropertyApply/detail', ['id'=>$list_item['id']]) }}</url>
                        </attrs>
                    </action> 
                    @else
                    <action label="申请详情" css="">
                        <attrs>
                            <url>{{ u('PropertyApply/detail', ['id'=>$list_item['id']]) }}</url>
                        </attrs>
                    </action> 
                    @endif
                </actions>
            </columns>
        </table>
    </yz:list>
    @yizan_end 
	<script type="text/javascript">
    	$(function(){ 
    	});
    </script>
@stop

