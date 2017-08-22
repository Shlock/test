@extends('seller._layouts.base')
@section('css')
@stop
@section('content')
	<div>
		<div class="m-zjgltbg">					
			<div class="p10">
				<!-- 服务管理 -->
				<div class="g-fwgl">
					<p class="f-bhtt f14 clearfix">
						<span class="ml15 fl">公告管理</span>
					</p>
				</div>
				<!-- 服务表格 -->
				<div class="m-tab m-smfw-ser">
					@yizan_begin
	                    <yz:list>
							<btns>
								<linkbtn label="添加公告" url="{{ u('Article/create') }}" css="btn-gray"></linkbtn> 
							</btns> 
							<table pager="no" css="goodstable" relmodule="Article">
								<columns> 
								<column code="title" label="公告标题" align="center"></column>     
								<!-- <column code="content" label="公告内容" align="left"></column>   -->
								<column code="createTime" label="发布日期" align="center">
									{{ yztime($list_item['createTime']) }}
								</column>     
								<actions> 
									<action type="edit" css="blu"></action>
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
@stop
