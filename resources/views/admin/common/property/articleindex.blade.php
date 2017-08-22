@extends('admin._layouts.base')

@section('right_content')
	@yizan_begin
        <yz:list>
        	<search> 
				<row>
					<item label="物业公司">
						{{$seller['name']}}
					</item>
					<item label="小区名称">
						{{$seller['district']['name']}}
					</item>
					<item name="sellerId" type="hidden"></item>
				</row>
				<row>
					<item name="title" label="公告标题"></item>
					<item name="beginTime" label="开始时间" type="date"></item>
					<item name="endTime" label="结束时间" type="date"></item>
					<btn type="search"></btn>
				</row>
			</search>
			<btns>				
				<linkbtn label="添加公告">
					<attrs>
						<url>{{ u('Property/articlecreate', ['sellerId'=>$seller['id']]) }}</url>
					</attrs>
				</linkbtn>
			</btns> 
			<table>
				<columns>
					<column code="title" label="公告标题" align="center"></column>    
					<column code="createTime" label="发布时间">
						<p>{{ yztime($list_item['createTime'])}}</p>
					</column> 
					<actions> 
						<action label="编辑" >
							<attrs>
								<url>{{ u('Property/articleedit',['sellerId'=>$list_item['seller']['id'], 'id'=>$list_item['id']]) }}</url>
							</attrs>
						</action>
						<action label="删除" css="red">
							<attrs>
								<click>$.RemoveItem(this, '{!!u('Property/articledestroy',['sellerId'=>$list_item['seller']['id'], 'id'=>$list_item['id']])!!}', '你确定要删除该数据吗？');</click>
							</attrs>
						</action>
					</actions>
				</columns>
			</table> 
        </yz:list>
    @yizan_end

@stop
@section('js')
@stop
