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
				<!-- 请假管理 -->
				<div class="g-fwgl">
					<p class="f-bhtt f14 clearfix">
						<span class="ml15 fl">人员请假管理</span>
					</p>
				</div>
				<!-- 人员表格 -->
				<div class="m-tab m-smfw-ser">
					@yizan_begin
	                    <yz:list>


	                        <table css="goodstable">
                                <columns>
                                    <column code="name" label="姓名" width="60" iscut="1">{{ $list_item['staff']['name'] }}</column>
                                    <column code="beginTime" label="开始时间" width="60" ></column>
                                    <column code="endTime" label="结束时间" width="60" ></column>
                                    <column code="remark" label="请假理由" width="60" ></column>
                                    <column code="statusStr" label="是否同意" width="60"  css="is_agree"></column>
                                    <actions width="90">
                                        @if($list_item['disposeTime'] == 0)
                                        <a href="javascript:;" class=" blu agree" data-pk="{{ $list_item['id']  }}" >同意</a>
                                        <a href="javascript:;" class=" blu refuse" data-pk="{{ $list_item['id']  }}"  >拒绝</a>
                                        @endif
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
<script type="text/javascript">
    $(document).on("click",".agree",function(){
        var pk = $(this).data("pk");
        var obj = $(this);
        $.post("{{ u('Staffleave/dispose') }}",{id:pk,agree:1},function(res){
            if(res.code == 0){
                obj.parents("tr").find(".is_agree").html("同意");
                obj.siblings(".refuse").remove();
                obj.remove();
            }
        },"json");
    }).on("click",".refuse",function(){
        var pk = $(this).data("pk");
        var obj = $(this);
        $.post("{{ u('Staffleave/dispose') }}",{id:pk,agree:'-1'},function(res){
            if(res.code == 0){
                obj.parents("tr").find(".is_agree").html("拒绝");
                obj.siblings(".agree").remove();
                obj.remove();
            }
        },"json");
    })
</script>
@stop
