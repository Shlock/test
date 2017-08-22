@extends('admin._layouts.base')
@section('css')
@stop
@section('right_content')
@yizan_begin  
		<yz:list>
			<search> 
				<row>
					@yizan_yield('search')
					<item name="mobileName" label="人员名称/手机"></item> 
					@yizan_stop
					<item label="认证状态">
						<yz:select name="status" options="0,2,1,3" texts="所有,待处理,未通过,通过"></yz:select>
					</item>  
					<btn type="search"></btn>
				</row>
			</search>
			<tabs>
			<navs>
				<nav label="机构">
					<attrs>
						<url>{{ u('ServiceCertificate/index',['type'=>'2','nav'=>2]) }}</url>
						<css> @if( $nav == 2 ) on @endif </css>
					</attrs>
				</nav>
				<nav label="个人">
					<attrs>
						<url>{{ u('ServiceCertificate/index',['type'=>1,'nav'=>1]) }}</url>
						<css>@if( $nav == 1 ) on @endif</css>
					</attrs>
				</nav>
			</navs>
			</tabs>
			<table>
				<rows>
					<headers>
						@if( $nav == 2 ) 
							<header label="机构信息"></header>
						@else
							<header label="个人信息"></header>
						@endif
						<header label="处理人员" width="130"></header>
						<header label="处理备注"></header>
						<header label="更新时间" width="90"></header>
						<header label="状态" width="50"></header>
					</headers>
					<row>
						<tr class="{{ $list_item_css }}">
							<td style="text-align:left">
								<p>{{ $list_item['seller']['name'] }}</p>
								<p>{{ $list_item['seller']['mobile'] }}</p>
							</td>
							<td style="text-align:left">
								<p>{{ $list_item['admin']['name'] }}</p>
								<p>{{ yztime($list_item['disposeTime']) }}</p>
							</td>
							<td style="text-align:left;vertical-align:top;word-wrap:break-word">
								{{ $list_item['disposeRemark'] }}
							</td>
							<td>
								{{yztime($list_item['updateTime'])}}
							</td>
							<td>
								@if($list_item['status'] == 1)
								<span style="color:#00f;">通过</span>
								@elseif($list_item['status'] == -1)
								<span style="color:#f00;">未通过</span>
								@else
								<span>待处理</span>
								@endif
							</td>
							<td rowspan="2">
								<actions width="50">
									<action type="edit" label="查看" css="blu"></action>
								</actions>
							</td>
						</tr>
						<tr class="{{ $list_item_css }}">
							<td colspan="5" style="text-align:left;">
								@foreach($list_item['certificates'] as $img) 
								<a href="{{ $img }}" target="_blank"><img src="{{ formatImage($img,0,40,0) }}" alt="" height="40"></a>
				           		@endforeach 
							</td>
						</tr>
					</row>
				</rows>
			</table>
		</yz:list>
	@yizan_end
@stop