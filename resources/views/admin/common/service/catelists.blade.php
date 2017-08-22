@extends('admin._layouts.base')
@section('css')
<style type="text/css">
</style>
@stop
@section('right_content')
	@yizan_begin
		<yz:list> 
		<table pager="no" relmodule="GoodsCate">
			<columns>
				<column code="id" label="分类编号" align="center"  ></column> 
				<column code="name" label="分类名称" align="center" css="name"></column> 
				<!--column code="levelrel" label="层级视图" css="sort" align="center"></column -->
				<!-- <column code="img" label="图标">
					<img src="{{$list_item['img']}}" style="max-width:32px;"/>
				</column>  -->
				<column code="sort" label="排序" css="sort"></column> 
				<column code="status" label="状态" type="status"></column>
				<actions> 
					<action type="edit" css="blu">
						<attrs>
							<url>{{ u('Service/cateedit',['id'=>$list_item['id'],'sellerId'=>$list_item['sellerId']]) }}</url>
						</attrs>
					</action> 
					<action type="destroy" css="red" >
						<attrs>
							<url>{{ u('GoodsCate/destroy',['id'=>$list_item['id'], 'sellerId'=>$list_item['sellerId']]) }}</url>
						</attrs>
					</action> 
				</actions>
			</columns>
		</table>
	</yz:list>
	@yizan_end
@stop

@section('js')
<script type="text/javascript">
	$(function(){
		$('#cate_id').prepend("<option value='0' selected>全部分类</option>");
	});
</script>
@stop
 