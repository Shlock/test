@extends('admin._layouts.base')

@section('right_content')
	@yizan_begin
	<php> 
		$navs = ['nav1','nav2','nav3','nav4'];
		$nav = in_array(Input::get('nav'),$navs) ? Input::get('nav') : 'nav1' ; 
		$$nav = "on";
	</php>
	<yz:list>
			<tabs>
				<navs>
					<nav label="业绩排行榜">
						<attrs>
							<url>{{ u('Performance/index',['nav'=>'nav1']) }}</url>
							<css>{{$nav1}}</css>
						</attrs>
					</nav>
					<nav label="抽成排行榜">
						<attrs>
							<url>{{ u('Performance/bonus',['nav'=>'nav2']) }}</url>
							<css>{{$nav2}}</css>
						</attrs>
					</nav>
					<nav label="卖家业绩查看">
						<attrs>
							<url>{{ u('Performance/sellerperformance',['nav'=>'nav3']) }}</url>
							<css>{{$nav3}}</css> 
						</attrs>
					</nav>
					<nav label="员工业绩查看">
						<attrs>
							<url>{{ u('Performance/staffperformance',['nav'=>'nav4']) }}</url>
							<css>{{$nav4}}</css>
						</attrs>
					</nav>
				</navs>
			</tabs>
			
			<table>
				<search>
				<row>
					<item name="beginTime" label="开始时间" type="date"></item>
					<item name="endTime" label="结束时间" type="date"></item>
					<btn type="search"></btn>
				</row>
			</search>
            	<columns>
					<column code="" label="排名"></column>
					<column code="name" label="姓名"></column>
					<column code="totalPay" label="业绩"></column>
					<column code="totalService" label="抽成"></column>
					<actions>
						<action label="查看业绩">
							<attrs>
								<url>{{ u('Performance/sellerperformance',['seller'=>$list_item['seller']['id']]) }}</url>
							</attrs>
						</action>
					</actions>
				</columns>
			</table>
		</yz:list>


	@yizan_end
@stop

@section('js')
<script type="text/javascript">
	$(function(){
        $('#yzForm').submit(function(){
            var beginTime = $("#beginTime").val();
            var endTime = $("#endTime").val();
            if(beginTime!='' || endTime!='') {
                if(beginTime==''){
                    alert("开始时间不能为空");return false;
                }
                else if(endTime==''){
                    alert("结束时间不能为空");return false;
                }
                else if(endTime < beginTime){
                    alert("结束时间不能大于开始时间");return false;
                }
            }
        });
    });
</script>
@stop
