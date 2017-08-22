@extends('seller._layouts.base')
@section('css')
<style type="text/css">
	.m-tab table tbody td{
		padding: 10px 0px;
		font-size: 12px;
  		text-align: center; 
	}
	.m-tab{
		margin-top: -11px;
	}
	#money-form-item,#waitConfirmMoney-form-item,#lockMoney-form-item{
		  margin-right: 40px;
	}
</style>
@stop
@section('content')
	<div>
		<div class="m-zjgltbg">									
			<div  class="p10">		
				<p class="f-bhtt f14">
					<span class="ml15">账户结算</span>
				</p>			
				<p class="f-bhtt f14 mt10">
					<span class="ml15">账户余额：￥ {{ $useracount['money'] or 0 }}</span>
					@if($useracount['money'] != 0)
		                 <a href="{{ u('Funds/withdraw') }}" class=" ml15 btn f-bluebtn" style="margin-bottom:8px;">提现</a>
	             	@endif
				</p>	
				@yizan_begin
				<php>  
					switch ($args[status]) {
						case '1':
							$nav1 = 'on';
							break; 
						case '2':
							$nav2 = 'on';
							break; 
						default:
							$nav = 'on';
							break;
					}
				</php> 
                <!-- 账户交易记录 -->
				<div class="m-jyjlct"  style="margin-top: 10px;"> 
					<yz:list> 
                        <tabs>
                            <navs>
                                <nav label="全部">
                                    <attrs>
                                        <url>{{ u('Funds/index',['status'=>0, 'beginTime'=>$args['beginTime'], 'endTime'=>$args['endTime']]) }}</url>
                                        <css>{{$nav}}</css>
                                    </attrs>
                                </nav>
                                <nav label="收入">
                                    <attrs>
                                        <url>{{ u('Funds/index',['status'=>1, 'beginTime'=>$args['beginTime'], 'endTime'=>$args['endTime']]) }}</url>
                                        <css>{{$nav1}}</css>
                                    </attrs>
                                </nav>
                                <nav label="支出">
                                    <attrs>
                                        <url>{{ u('Funds/index',['status'=>2, 'beginTime'=>$args['beginTime'], 'endTime'=>$args['endTime']]) }}</url>
                                        <css>{{$nav2}}</css>
                                    </attrs>
                                </nav> 
                            </navs>
                        </tabs>
						<search>  
							<php>
							$search_args = $args;
							</php>
							<row>									
								<item name="beginTime" label="开始时间" type="date"></item>
								<item name="endTime" label="结束时间" type="date"></item>
								<!--item label="状态">
									<yz:select name="status" options="0,1,2,3,4" texts="全部,待审核,已通过,已拒绝,已确认" selected="$search_args['status']"></yz:select>
								</item -->
								<input type="hidden" name="status" value="{{$args['status']}}" />
								<btn label="查询" type="search" css="btn-gray fr"></btn> 
							</row>
						</search>
						<table>
							<columns>  
								<column code="createTime" label="日期" type="time"></column>
								<column code="typeStr" label="类型" width="40"></column>  
								<column code="money" label="入账金额" width="100">
									@if($list_item['money'] > 0)+@endif{{number_format($list_item['money'], 2)}}
								</column>
<!-- 								<column code="balance" label="当前余额" width="100"></column> -->
								<column code="status" label="处理状态"  width="100">
								    {{ Lang::get('seller.status_money_logs.'.$list_item['status']) }}
								</column>
								<column code="content" label="备注"></column> 
							</columns>
						</table>
					</yz:list>
				</div>
                @yizan_end		 
			</div>
		</div>
	</div>
@stop

@section('js')

<script type="text/javascript">
	$(function(){
		$('.date').datepicker({
			changeYear:true,
			changeMonth:false,
		}); 
	}); 
</script>
@stop
