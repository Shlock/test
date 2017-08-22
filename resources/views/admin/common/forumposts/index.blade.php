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
				<item name="title" label="标题"></item> 
                <item name="username" label="发帖人"></item> 
                <item label="板块名称">
                	<yz:select name="plateId" options="$plates" textfield="name" valuefield="id" first="全部" firstvalue="0" selected="$search_args['plateId']"></yz:select>
                </item> 
				<item name="beginTime" label="开始时间" type="datetime"></item>
				<item name="endTime" label="结束时间" type="datetime"></item>
				<item label="状态">
					<yz:select name="status" options="0,1,2" texts="全部,关闭,开启" selected="$search_args['status']"></yz:select>
				</item>
				<btn type="search"></btn>
			</row>
		</search>
		<table >
			<columns> 
				<column code="" label="板块名称" css="sort">
					{{$list_item['plate']['name']}}
				</column> 
				<column code="title" label="标题" align="left" ></column>
				<column code="user" width="80"  label="发帖人" align="center" >
					{{$list_item['user']['name']}}
				</column> 
				<column name="createTime" label="发帖时间" >
					{{yzday($list_item['createTime'])}}
				</column>
				<column code="rateNum" label="评论数" align="center" ></column>
				<column code="goodNum" label="点赞数" align="center" ></column> 
				<column code="top" label="置顶" css="sort">
					<p><input type="checkBox" name="top" data-id="{{$list_item['id']}}" class="top" @if($list_item['top'] == 1) checked="checked" @endif /></p>
				</column> 
				<column code="hot" label="热门" css="sort">
					<p><input type="checkBox" name="hot" data-id="{{$list_item['id']}}" class="hot" @if($list_item['hot'] == 1) checked="checked" @endif /></p>
				</column> 
				<column code="status" label="状态" type="status"></column>
				<actions> 
					<action label="详情" css="blu">
						<attrs>
							<url>{{ u('ForumPosts/detail',['id'=>$list_item['id']]) }}</url>
						</attrs>
					</action>
					<action type="destroy" css="red"></action> 
				</actions>
			</columns>
		</table>
	</yz:list>
	@yizan_end
	<script type="text/javascript">
	$(function(){ 
		$(".top").click(function(){ 
			var obj = new Object();
			obj.id = $(this).data('id');
			obj.key = 'top';
			if ($(this).is(':checked')) {
				obj.val = 1;
			} else {
				obj.val = 0;
			}
			$.post("{{u('ForumPosts/update')}}", obj, function(){},'json');
		})
		$(".hot").click(function(){
			var obj = new Object();
			obj.id = $(this).data('id');
			obj.key = 'hot';
			if ($(this).is(':checked')) {
				obj.val = 1;
			} else {
				obj.val = 0;
			}
			$.post("{{u('ForumPosts/update')}}", obj, function(){},'json');
		})
	})
	</script>
@stop