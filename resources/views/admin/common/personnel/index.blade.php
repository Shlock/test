@extends('admin._layouts.base')

@section('right_content')
@yizan_begin 
		<yz:list>
			<search> 
				<row>
					<item name="realName" label="认证名称"></item>  
					<item name="mobile" label="认证手机"></item>
					<item label="认证状态">
						<yz:select name="status" options="0,2,1,3" texts="所有,待处理,未通过,通过" selected="$search_args['status']"></yz:select>
					</item>  
					@yizan_yield('business_licence_sn')
					<item name="businessLicenceSn" label="营业执照"></item>  
					<item name="companyName" label="机构名称"></item>
					@yizan_stop
					<btn type="search"></btn>
				</row>
			</search>
			<table>
				<rows>
					<headers>						
						<header label="人员信息" width="110"></header>
						<header label="身份信息" width="150"></header>
						@if( $nav == 1 )
						<header label="图片"></header>
						@else						
						<header label="机构证书"></header>
						@endif
						<header label="更新时间" width="90"></header>
						<header label="状态" width="50"></header>
					</headers>
					<row>
						<tr class="{{ $list_item_css }}">
							<td style="text-align:left">
								<p>{{ $list_item['seller']['name'] }}</p>
								<p>{{ $list_item['seller']['mobile'] }}</p>
							</td>
							@if( $nav == 1 )
							<td style="text-align:left">
								<p>{{ $list_item['realName'] }}</p>
								<p>{{ $list_item['idcardSn'] }}</p>
							</td>
							<td style="text-align:left">
								<a href="{{ $list_item['idcardPositiveImg'] }}" target="_blank"><img src="{{ formatImage($list_item['idcardPositiveImg'],0,40,0) }}" alt="" height="40"></a>
								<a href="{{ $list_item['idcardNegativeImg'] }}" target="_blank"><img src="{{ formatImage($list_item['idcardNegativeImg'],0,40,0) }}" alt="" height="40"></a>
							</td>
							@else
							<td style="text-align:left">
								<p>{{ $list_item['companyName'] }}</p>
								<p>{{ $list_item['businessLicenceSn'] }}</p>
							</td>
							<td style="text-align:left">
								<a href="{{ $list_item['businessLicenceImg'] }}" target="_blank"><img src="{{ formatImage($list_item['businessLicenceImg'],0,40,0) }}" alt="" height="40"></a> 
							</td>
							@endif
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
							<td colspan="2" style="text-align:left">
								<p>处理人员：{{ $list_item['admin']['name'] }}</p>
								<p>处理时间：{{ yztime($list_item['disposeTime']) }}</p>
							</td>
							<td colspan="3" style="text-align:left;vertical-align:top;word-wrap:break-word">
								<p>处理备注：{{ $list_item['disposeRemark'] }}</p>
							</td>
						</tr>
					</row>
				</rows>
			</table>
		</yz:list>
	@yizan_end
@stop