@extends('seller._layouts.base')
@section('css')
<style type="text/css">
	.m-tab table tbody td{padding: 10px 0px;}
	#checkListTable p{padding:0px 0px 0px 10px;}
</style>
@stop
@section('content')
	<div>
		<div class="m-zjgltbg">
			<div class="p10">
				<!-- 订单管理 -->
				<div class="g-fwgl">
					<p class="f-bhtt f14 clearfix" style="border-bottom:0;">
						<span class="ml15 fl">服务类订单管理</span>
					</p>
				</div>
				@yizan_begin
				<php> 
					$navs = ['nav1','nav2','nav3','nav4','nav5'];
					$nav = in_array(Input::get('nav'),$navs) ? Input::get('nav') : 'nav1' ; 
					$$nav = "on";
				</php>
                    <yz:list>
                        <tabs>
                            <navs>
                                <nav label="全部订单">
                                    <attrs>
                                        <url>{{ u('ServiceOrder/index',['nav'=>'nav1']) }}</url>
                                        <css>{{$nav1}}</css>
                                    </attrs>
                                </nav>
                                <nav label="待服务订单">
                                    <attrs>
                                        <url>{{ u('ServiceOrder/index',['status'=>'1','nav'=>'nav2']) }}</url>
                                        <css>{{$nav2}}</css>
                                    </attrs>
                                </nav>
                                <nav label="待完成订单">
                                    <attrs>
                                        <url>{{ u('ServiceOrder/index',['status'=>'2','nav'=>'nav3']) }}</url>
                                        <css>{{$nav3}}</css>
                                    </attrs>
                                </nav>
                                <nav label="已取消订单">
                                    <attrs>
                                        <url>{{ u('ServiceOrder/index',['status'=>'4','nav'=>'nav5']) }}</url>
                                        <css>{{$nav5}}</css>
                                    </attrs>
                                </nav>
                                <nav label="已完成订单">
                                    <attrs>
                                        <url>{{ u('ServiceOrder/index',['status'=>'3','nav'=>'nav4']) }}</url>
                                        <css>{{$nav4}}</css>
                                    </attrs>
                                </nav>
                            </navs>
                        </tabs>
                    	<search>
            				<row>
            					<item name="sn" label="订单SN码"></item>
                                <item name="name" label="会员名称"></item>
            					<item name="mobile" label="联系电话"></item>
	                        </row>
            				<row>
                                <item name="staffName" label="配送人员"></item>
            					<item name="beginTime" label="开始时间" type="date"></item>
					            <item name="endTime" label="结束时间" type="date"></item>
                                <btn type="search" css="btn-gray"></btn>
	                        </row>
	                    </search>

						<btns>
	                    	<linkbtn label="导出到EXCEL" type="export" url="{{ u('Order/export?'.$excel) }}" css="btn-gray"></linkbtn>
	                    </btns>
                        <table>
                            <columns>
                            <column code="sn" label="订单SN码" width="150"></column>
        					<column code="name" label="会员名称" width="100"></column>
        					<column code="mobile" label="联系电话"></column>
        					<column label="服务人员" width="100">{{ $list_item['staff']['name'] }}</column>
        					<column code="totalFee" label="订单金额" width="80"></column>
        					<column code="status" label="订单状态" width="80">
        						 {{ $list_item['orderStatusStr'] }}
        					</column>
        					<actions width="80">
    							<action label="详情" css="blu">
    								<attrs>
										<url>{{ u('ServiceOrder/detail',['orderId'=>$list_item['id']]) }}</url>
									</attrs>
    							</action>
                                <!--<action type="destroy" css="red"></action>-->
        					</actions>
        				</columns>
                        </table>
                    </yz:list>
                @yizan_end
			</div>
		</div>
	</div>
@stop
@section('js')
@stop