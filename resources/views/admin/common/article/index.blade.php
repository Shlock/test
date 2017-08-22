@extends('admin._layouts.base')
@section('css')
@stop
@section('right_content')
	@yizan_begin
	<yz:list>
		<search> 
			<row>
				<item name="title" label="标题" ></item>
				<item label="分类">
					<yz:select name="cateId" options="$cate_list" textfield="levelname" valuefield="id" attr="style='min-width:160px;width:auto'" firstvalue="0" first="全部分类" selected="$search_args['cateId']"> 
					</yz:select>
				</item>  
				<btn type="search"></btn>
			</row>
		</search>
		<btns>
			<linkbtn type="create" label="添加文章" url="{{ u('Article/create') }}"></linkbtn>
			<btn type="destroy" label="删除"></btn>
		</btns>
		<table checkbox="1">
			<columns> 
				<column code="id" label="编号" width="40"></column> 
				<column code="title" label="文章标题" align="left"></column>  
				<column label="文章分类" align="left">
					{{ $cate_list[$list_item['cateId']]['levelrel'] }}
				</column> 
				<column code="sort" label="排序"></column> 
				<column code="status" label="状态" type="status"></column> 
				<actions> 
					<action type="edit" css="blu"></action>
					<action type="destroy" css="red"></action>
				</actions>
			</columns>
		</table>
	</yz:list>
	@yizan_end
@stop
@section('js')
@stop
