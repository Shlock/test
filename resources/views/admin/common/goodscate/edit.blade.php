@extends('admin._layouts.base')
@section('css')
<style type="text/css">
	.ts1{color: #ccc;margin-left: 5px;}
</style>
@stop
@section('right_content')
	@yizan_begin
		<yz:form id="yz_form" action="save">
			<yz:fitem name="name" label="分类名称" attr="maxlength='20'"></yz:fitem>
			<yz:fitem label="所属分类">
				<yz:select name="pid" options="$cate" textfield="levelname" valuefield="id" attr="style='min-width:234px;width:auto'" selected="$data['pid']">
				</yz:select>
				<span class="ts ts1"></span>
			</yz:fitem>
			<yz:fitem label="类型"> 
				<yz:radio name="type" options="1,2" texts="商品,服务" checked="$data['type']"></yz:radio>
			</yz:fitem>
			<yz:fitem name="img" label="图标" type="image"></yz:fitem>
			<yz:fitem name="sort" label="排序" val="100"></yz:fitem>
			<yz:fitem label="状态">
				<php> $status = isset($data['status']) ? $data['status'] : 1 </php>
				<yz:radio name="status" options="1,0" texts="开启,关闭" checked="$status"></yz:radio>
			</yz:fitem>
		</yz:form>
	@yizan_end
@stop
@section('js')
<script type="text/javascript">
//排除自身（无限极）
var son = eval( {{$son}} );
//排除二级（限制2级）
var levs = eval( {{$levs}} );
//是否存在子集（限制2级）
var hasson = eval( {{$hasson}} );
$(function(){
	if(son){
		for (var i = son.length - 1; i >= 0; i--) {
			$("#pid option[value='"+son[i]+"']").attr("disabled","disabled");
		};
	}

	if(levs){
		$.each(levs,function(key,value){
			$("#pid option[value='"+value+"']").attr("disabled","disabled");
		});
	}

	if(hasson==1){
		$("#pid").attr("disabled","disabled");
		$(".ts1").text("不允许移动存在子集的分类");
		$("#yz_form").append("<input name='pid' type='hidden' value='0'>");
	}
	

})

</script>
@stop