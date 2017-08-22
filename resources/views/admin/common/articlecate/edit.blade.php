@extends('admin._layouts.base')
@section('css')
@stop
@section('right_content')
	@yizan_begin
		<yz:form id="yz_form" action="save">
			<yz:fitem name="name" label="分类名称"></yz:fitem>
			<yz:fitem label="所属分类">
				<yz:select name="pid" options="$cate" textfield="levelname" valuefield="id" attr="style='min-width:234px;width:auto'" selected="$data['pid']">
				</yz:select>
			</yz:fitem>
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
var son = eval( {{$son}} );
	$(function(){
		if(son){
			for (var i = son.length - 1; i >= 0; i--) {
				$("#pid option[value='"+son[i]+"']").attr("disabled","disabled");
			};
		}
	});
</script>
@stop