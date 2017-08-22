@extends('admin._layouts.base')
@section('css')
<style type="text/css">
</style>
@stop
@section('right_content')
	@yizan_begin
		<yz:list> 
		<search >
			<row>
				<item name="keywords" label="关键字"></item>  
				<item label="状态">
					<yz:select name="status" options="0,1,2,3" texts="全部,拒绝,未处理,已处理" selected="$search_args['status']"></yz:select>
				</item>
				<btn type="search"></btn>
			</row>
		</search>
		<table >
			<columns> 
				<column  label="会员名称" >
					{{$list_item['user']['name']}}
				</column> 
				<column  label="帖子标题" >
					{{$list_item['posts']['title']}}
				</column> 
				<column code="content" label="举报内容" align="center"  ></column>  
				<column label="举报时间" align="center"  >
					{{Time::toDate($list_item['createTime'])}}
				</column> 
				<column label="状态" type="status">
					{{ Lang::get('admin.postscomplain.'.$list_item['status']) }}
				</column>
				<column label="处理时间" align="center"  >
					{{Time::toDate($list_item['disposeTime'])}}
				</column> 
				<column code="disposeResult" label="备注" align="center"  ></column>  
				<actions> 
					@if($list_item['status'] == 0)
					<action label="处理" css="blu dispose" click="true"  ></action> 
					@endif
					<action type="destroy" css="red"></action> 
				</actions>
			</columns>
		</table>
	</yz:list>
	@yizan_end
	<script type="text/tpl" id="replyTpl">
	<select id="reply_status" style="width:120px;    height: 35px;" class="form-control">
		<option value="-1">拒绝</option>
		<option value="1">已处理</option>
	</select>
	<textarea id="reply_remark" style="width:400px;height:100px;border:1px #ccc solid;margin-top: 20px;"></textarea>
	</script>
	<script type="text/javascript">
	$(".dispose").click(function(){
		var id = $(this).data('pk');
		$.ShowConfirm($("#replyTpl").html(), function(){ 
			var remark = $("#reply_remark").val();
			var status = $("#reply_status").val();  
			if(status == 0){
				$.ShowAlert("请选择处理的状态");
				return ;
			}
			if(remark.length > 0) {
				$.post("{{ u('ForumComplain/dispose') }}",{"id":id,"status":status,"remark":remark},function(res){
					$.ShowAlert(res.msg);
					if(res.status==true){
						window.location.reload();
					} 
				},'json')
			}else{
				$.ShowAlert("请填写备注信息");
			}
		},function(){},'操作提示');
	})
	</script>
@stop