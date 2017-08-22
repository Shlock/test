@extends('seller._layouts.base')
@section('css')
<style type="text/css">
	.m-tab table tbody td{padding: 5px 0px;}
	.pl10 p{padding-left: 10px;}
</style>
@stop
@section('content')
	<div>
		<div class="m-zjgltbg">					
			<div class="p10">
				<div class="m-tab m-smfw-ser">
					@yizan_begin
	                    <yz:list>
	                    	<search> 
								<row>
									<item name="type" type="hidden" val="$args['type']"></item>
                                    <item name="name" label="姓名"></item>
                                    <item name="mobile" label="电话"></item>
									<btn type="search" css="btn-gray"></btn>
								</row>
							</search>	                        <table css="goodstable">
                                <columns>
                                    <column code="avatar" label="头像" type="image" width="40"></column>
                                    <column code="name" label="姓名" width="60" align="left" css="pl10">
                                    	<p>{{$list_item['name']}}</p>
                                    </column>
                                    <column code="mobile" label="电话" width="100">
                                    	<p>{{$list_item['mobile']}}</p>
                                    </column>
                                    <column code="orderStatus" label="状态" type="status" width="60">
                                    	@if($list_item['orderStatus'] == 0)
											停用
                                    	@elseif($list_item['orderStatus'] == 1)
                                    		正常
                                    	@endif
                                    </column>
                                    @yizan_yield('actions')
					                <actions width="80">
		    							<action label="查看日程" css="blu">
		    								<attrs>
												<url>{{ u('StaffSchedule/edit',['id'=>$list_item['id'],'type'=>$list_item['type']]) }}</url>
											</attrs>
										</action>
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
