@extends('seller._layouts.base')
@section('css')
<style type="text/css">
	.m-tab table tbody td{padding: 5px 0px;}
	.pl10 p{padding-left: 10px;}
</style>
@stop
@section('content')
<php>
	$types = array(
		0 => '个人加盟人员',
		1 => '配送人员',
		2 => '服务人员',
		3 => '配送和服务人员',
	);
</php>
	<div>
		<div class="m-zjgltbg">					
			<div class="p10">
				<!-- 人员管理 -->
				<div class="g-fwgl">
					<p class="f-bhtt f14 clearfix">
						<span class="ml15 fl">人员管理</span>
					</p>
				</div>
				<!-- 人员表格 -->
				<div class="m-tab m-smfw-ser">
					@yizan_begin
	                    <yz:list>
	                    	@if($seller['type'] == 2)
							@yizan_yield('btns')
	                    	<btns>
								<linkbtn label="添加人员" url="{{ u('Staff/create') }}" css="btn-gray"></linkbtn>
							</btns>
							@yizan_stop
							@endif
	                    	<search> 
								<row>
									<item name="type" type="hidden" val="$args['type']"></item>
                                    <item name="name" label="姓名"></item>
                                    <item name="mobile" label="电话"></item>
									<btn type="search" css="btn-gray"></btn>
								</row>
							</search>	                        
							<table css="goodstable">
                                <columns>
                                    <column code="avatar" label="头像" type="image" width="60"></column>
                                    <column code="name" label="姓名" width="60" align="left" css="pl10">
                                    	<p>{{$list_item['name']}}</p>
                                    </column>
                                    <column code="mobile" label="电话" width="100">
                                    	<p>{{$list_item['mobile']}}</p>
                                    </column>
                                    <column code="address" label="地址" width="120">
                                    	<p>{{$list_item['address']}}</p>
                                    </column>
                                    <column code="type" label="类型" width="150">
                                    	<p>{{$types[$list_item['type']]}}</p>
                                    </column>
                                    <column code="status" label="状态" type="status" width="60">
                                    </column>
                                    @yizan_yield('actions')
					                <actions width="80">
		    							<action label="编辑" css="blu">
		    								<attrs>
												<url>{{ u('Staff/edit',['id'=>$list_item['id'],'type'=>$list_item['type']]) }}</url>
											</attrs>
										</action>
		        						<action type="destroy" css="red"></action>                						
		        					</actions>
					                @yizan_stop
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
