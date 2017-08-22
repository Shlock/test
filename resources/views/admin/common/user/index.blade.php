@extends('admin._layouts.base')
@section('css')
<style type="text/css">
	td p{padding: 2px;}
</style>
@stop

@section('right_content')
	@yizan_begin
	<yz:list>
		<search> 
			<row>
				<item name="name" label="会员名称"></item>  
				<item name="mobile" label="会员手机"></item>
				<item name="recommendUserName" label="推荐人"></item>
				<item label="会员状态">
					<yz:select name="status" options="-1,1,2" texts="所有会员,锁定,正常" selected="$search_args['status']"></yz:select>
				</item>
                <item label="会员类型">
                    <yz:select name="userType" options="0,1,2" texts="所有会员,商家会员,买家会员" selected="$search_args['userType']"></yz:select>
                </item>
                <item label="会员类型">
                    <yz:select name="userType" options="0,1,2" texts="所有会员,商家会员,买家会员" selected="$search_args['userType']"></yz:select>
                </item>
                <btn type="search"></btn>
			</row>
		</search>
		<table>
			<columns> 
				<column code="avatar" label="头像" width="40">
					<img src="{{ $list_item['avatar'] }}" style="max-width:40px">
				</column>
				<column label="名称" align="left" width="75" code="name">
					<p>{{ $list_item['name'] }}</p>
				</column>
                <column label="余额" align="center" width="30" code="balance">
                    <p><a href="{{u('PayLog/index',['mobile'=>$list_item['mobile']])}}">{{ (int)$list_item['balance'] }}</a></p>
                </column>
				<column label="手机" align="left" width="75" code="mobile">
					<p>{{ $list_item['mobile'] }}</p>
				</column>
				<column label="注册信息" align="left" width="180">
					 <p><b>注 册 IP:</b>{{ $list_item['regIp'] }}</p>
					 <p><b>注册时间:</b>{{ yztime($list_item['regTime'])  }}</p>
					 <p><b>注册推荐人:</b>
					 @if($list_item['recommendUid'])
					 	<a target="_blank" href="/User/edit?id={{$list_item['extensionWorker']['id']}}">{{$list_item['extensionWorker']['name']}}</a>
					 @else
					 	无
					 @endif
					 </p>
					 <!--
					 <p>{{ $list_item['regCity'] != "" ? "<b>注册城市:</b>".$list_item['regCity'] .'-' .$list_item['regProvince'] : "未获取到注册城市"}}</p> 
					<p></p>-->
				</column> 
				<column label="登录信息" align="left"  width="180">
					<p><b>登 录 I P:</b>{{ $list_item['loginIp']   != "" ?  $list_item['loginIp'] : "未获取到最后登录IP"}}</p> 
					<!-- <p>{{ $list_item['loginCity'] != "" ? "登录城市:".$list_item['loginCity']['name'] .'-' .$list_item['loginProvince']['name'] : "未获取到最后登录城市"}}</p> -->
					<p><b>登录时间:</b>{{ $list_item['loginTime'] != "" ?  yztime($list_item['loginTime']) : "未获取到最后登录时间"}}</p> 
				</column>
				<column code="status" label="状态" type="status"  width="30"></column>
				<actions>
					<action type="edit" css="blu"></action> 
					@if(!$list_item['seller'] && !$list_item['staff'])
					<action type="destroy" css="red"></action>
					@endif
				</actions>
			</columns>
		</table>
	</yz:list>
	@yizan_end
@stop

@section('js')

@stop